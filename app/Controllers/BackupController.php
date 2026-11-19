<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use PDO;

class BackupController extends Controller
{
    public function download(Request $request): Response
    {
        $user = Session::get('user');
        $role = strtolower($user['role'] ?? '');
        if (!in_array($role, ['owner', 'admin'], true)) {
            Session::flash('error', 'Hanya Owner atau Admin yang berhak mengunduh cadangan database.');
            return $this->redirect('/admin/settings');
        }

        $db = Database::connect();
        $databaseName = config('database.connections.mysql.database', 'kost_management');
        $timestamp = date('Y-m-d_H-i-s');
        $filename = "backup_kost_zenerie_{$timestamp}.sql";

        // Generate SQL Dump in pure PHP
        $sql = "-- ========================================================\n";
        $sql .= "-- Kost Zenerie Management System Database Backup\n";
        $sql .= "-- Generated at: " . date('Y-m-d H:i:s') . "\n";
        $sql .= "-- Database: {$databaseName}\n";
        $sql .= "-- ========================================================\n\n";
        $sql .= "SET FOREIGN_KEY_CHECKS=0;\n";
        $sql .= "SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO';\n";
        $sql .= "SET NAMES utf8mb4;\n\n";

        // Get all tables
        $tables = $db->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN) ?: [];

        foreach ($tables as $table) {
            // Show create table
            $createRow = $db->query("SHOW CREATE TABLE `{$table}`")->fetch(PDO::FETCH_NUM);
            if (!empty($createRow[1])) {
                $sql .= "-- --------------------------------------------------------\n";
                $sql .= "-- Table structure for `{$table}`\n";
                $sql .= "-- --------------------------------------------------------\n";
                $sql .= "DROP TABLE IF EXISTS `{$table}`;\n";
                $sql .= $createRow[1] . ";\n\n";
            }

            // Get rows
            $rows = $db->query("SELECT * FROM `{$table}`")->fetchAll(PDO::FETCH_ASSOC) ?: [];
            if (!empty($rows)) {
                $sql .= "-- Dumping data for `{$table}` (" . count($rows) . " rows)\n";
                $columns = array_keys($rows[0]);
                $quotedColumns = array_map(fn($c) => "`{$c}`", $columns);
                $columnsList = implode(', ', $quotedColumns);

                $batchValues = [];
                foreach ($rows as $row) {
                    $vals = [];
                    foreach ($row as $v) {
                        if ($v === null) {
                            $vals[] = 'NULL';
                        } else {
                            $vals[] = $db->quote((string)$v);
                        }
                    }
                    $batchValues[] = '(' . implode(', ', $vals) . ')';
                }

                // Chunk in batches of 50 to avoid gigantic queries
                $chunks = array_chunk($batchValues, 50);
                foreach ($chunks as $chunk) {
                    $sql .= "INSERT INTO `{$table}` ({$columnsList}) VALUES\n" . implode(",\n", $chunk) . ";\n";
                }
                $sql .= "\n";
            }
        }

        $sql .= "SET FOREIGN_KEY_CHECKS=1;\n";
        $sql .= "-- Backup completed successfully.\n";

        header('Content-Type: application/sql; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Content-Length: ' . strlen($sql));
        header('Pragma: no-cache');
        header('Expires: 0');

        echo $sql;
        exit;
    }
}
