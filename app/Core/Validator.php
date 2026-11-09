<?php

declare(strict_types=1);

namespace App\Core;

class Validator
{
    private array $data;
    private array $rules;
    private array $errors = [];

    public function __construct(array $data, array $rules)
    {
        $this->data = $data;
        $this->rules = $rules;
    }

    public static function make(array $data, array $rules): self
    {
        $validator = new self($data, $rules);
        $validator->validate();
        return $validator;
    }

    public function validate(): bool
    {
        $this->errors = [];

        foreach ($this->rules as $field => $fieldRules) {
            $rulesList = is_string($fieldRules) ? explode('|', $fieldRules) : $fieldRules;
            $value = $this->data[$field] ?? null;

            foreach ($rulesList as $rule) {
                $ruleName = $rule;
                $ruleParam = null;

                if (str_contains($rule, ':')) {
                    [$ruleName, $ruleParam] = explode(':', $rule, 2);
                }

                switch ($ruleName) {
                    case 'required':
                        if ($value === null || $value === '' || (is_array($value) && empty($value))) {
                            $this->addError($field, "Bidang {$field} wajib diisi.");
                        }
                        break;

                    case 'email':
                        if ($value !== null && $value !== '' && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                            $this->addError($field, "Format {$field} harus berupa email yang valid.");
                        }
                        break;

                    case 'min':
                        $min = (int)$ruleParam;
                        if (is_string($value) && mb_strlen($value) < $min) {
                            $this->addError($field, "Bidang {$field} minimal terdiri dari {$min} karakter.");
                        } elseif (is_numeric($value) && (float)$value < $min) {
                            $this->addError($field, "Nilai {$field} minimal adalah {$min}.");
                        }
                        break;

                    case 'max':
                        $max = (int)$ruleParam;
                        if (is_string($value) && mb_strlen($value) > $max) {
                            $this->addError($field, "Bidang {$field} maksimal terdiri dari {$max} karakter.");
                        } elseif (is_numeric($value) && (float)$value > $max) {
                            $this->addError($field, "Nilai {$field} maksimal adalah {$max}.");
                        }
                        break;

                    case 'numeric':
                        if ($value !== null && $value !== '' && !is_numeric($value)) {
                            $this->addError($field, "Bidang {$field} harus berupa angka.");
                        }
                        break;

                    case 'in':
                        $allowed = explode(',', (string)$ruleParam);
                        if ($value !== null && $value !== '' && !in_array((string)$value, $allowed, true)) {
                            $this->addError($field, "Pilihan {$field} tidak valid.");
                        }
                        break;

                    case 'confirmed':
                        $confirmField = $field . '_confirmation';
                        $confirmValue = $this->data[$confirmField] ?? null;
                        if ($value !== $confirmValue) {
                            $this->addError($field, "Konfirmasi {$field} tidak cocok.");
                        }
                        break;
                }

                if (isset($this->errors[$field])) {
                    break; // Stop at first error for this field
                }
            }
        }

        return empty($this->errors);
    }

    public function passes(): bool
    {
        return empty($this->errors);
    }

    public function fails(): bool
    {
        return !$this->passes();
    }

    public function errors(): array
    {
        return $this->errors;
    }

    public function firstError(): ?string
    {
        if (empty($this->errors)) {
            return null;
        }
        $first = reset($this->errors);
        return is_array($first) ? reset($first) : $first;
    }

    private function addError(string $field, string $message): void
    {
        $this->errors[$field][] = $message;
    }
}

