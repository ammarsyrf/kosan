<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Config;
use App\Core\Logger;
use RuntimeException;

class FonnteService
{
    private string $apiToken;
    private string $apiUrl;
    private bool $enabled;
    private static bool $mockMode = false;
    private static ?array $mockResponse = null;

    public function __construct(
        ?string $apiToken = null,
        ?string $apiUrl = null,
        ?bool $enabled = null
    ) {
        $this->apiToken = $apiToken ?? (string)(getenv('FONNTE_API_TOKEN') ?: getenv('FONNTE_TOKEN') ?: Config::get('app.fonnte_token', ''));
        $this->apiUrl = $apiUrl ?? (string)(getenv('FONNTE_API_URL') ?: 'https://api.fonnte.com/send');
        $this->enabled = $enabled ?? true;
    }

    public static function setMockMode(bool $mock, ?array $mockResponse = null): void
    {
        self::$mockMode = $mock;
        self::$mockResponse = $mockResponse;
    }

    public function isEnabled(): bool
    {
        $env = getenv('FONNTE_ENABLED');
        if ($env !== false && $env !== '') {
            return filter_var($env, FILTER_VALIDATE_BOOLEAN);
        }
        return $this->enabled;
    }

    public static function sanitizePhoneNumber(string $phone): string
    {
        if (preg_match('/[a-zA-Z]/', $phone)) {
            throw new \InvalidArgumentException("Nomor telepon tidak boleh mengandung huruf ({$phone}).");
        }

        // Remove non-numeric characters
        $clean = preg_replace('/[^0-9]/', '', $phone);

        // Normalize Indonesian phone numbers:
        // 0812... -> 62812...
        if (str_starts_with($clean, '08')) {
            $clean = '62' . substr($clean, 1);
        } elseif (str_starts_with($clean, '8')) {
            $clean = '62' . $clean;
        }

        // Validate minimum and maximum standard phone length (10 - 16 digits)
        if (strlen($clean) < 10 || strlen($clean) > 16) {
            throw new \InvalidArgumentException("Nomor telepon tidak valid ({$phone}).");
        }

        return $clean;
    }

    public function sendWhatsAppMessage(string $recipientPhone, string $message): array
    {
        if (!$this->isEnabled()) {
            return [
                'success' => false,
                'status' => 'disabled',
                'message' => 'Integrasi Fonnte WhatsApp dinonaktifkan dalam pengaturan sistem.',
                'raw' => null,
            ];
        }

        $formattedPhone = self::sanitizePhoneNumber($recipientPhone);

        // Testing / Mock mode
        if (self::$mockMode) {
            $mock = self::$mockResponse ?? ['status' => true, 'id' => 'mock_' . bin2hex(random_bytes(4)), 'message' => 'Mock message sent successfully'];
            return [
                'success' => !empty($mock['status']),
                'status' => !empty($mock['status']) ? 'sent' : 'failed',
                'message' => $mock['message'] ?? 'OK',
                'raw' => json_encode($mock),
            ];
        }

        if (empty($this->apiToken)) {
            throw new RuntimeException("Fonnte API Token belum dikonfigurasi.");
        }

        // Prepare HTTP Request via cURL with timeout
        $payload = [
            'target' => $formattedPhone,
            'message' => $message,
            'countryCode' => '62',
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->apiUrl);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($payload));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Authorization: {$this->apiToken}",
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10); // 10 seconds timeout
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);

        $responseBody = curl_exec($ch);
        $curlError = curl_error($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($responseBody === false || !empty($curlError)) {
            $logMsg = "Fonnte API Request Timeout/Error to {$formattedPhone}: {$curlError}";
            Logger::error($logMsg);

            return [
                'success' => false,
                'status' => 'failed',
                'message' => "Koneksi ke Fonnte timeout atau gagal: {$curlError}",
                'raw' => json_encode(['error' => $curlError, 'http_code' => $httpCode]),
            ];
        }

        $decoded = json_decode((string)$responseBody, true);
        $isSuccess = ($httpCode >= 200 && $httpCode < 300) && (!empty($decoded['status']) && $decoded['status'] == true);

        // Audit logging without exposing api token
        Logger::info("Fonnte API Send to {$formattedPhone} - Status: " . ($isSuccess ? 'SUCCESS' : 'FAILED'), [
            'phone' => $formattedPhone,
            'http_code' => $httpCode,
            'success' => $isSuccess,
        ]);

        return [
            'success' => $isSuccess,
            'status' => $isSuccess ? 'sent' : 'failed',
            'message' => $decoded['message'] ?? ($isSuccess ? 'Terkirim' : 'Gagal mengirim pesan via Fonnte'),
            'raw' => (string)$responseBody,
        ];
    }
}
