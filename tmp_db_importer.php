<?php
// tmp_db_importer.php

if ($argc < 2) {
    die("Usage: php tmp_db_importer.php <path_to_sql_file.sql>\n");
}

$sqlFilePath = $argv[1];

if (!file_exists($sqlFilePath)) {
    die("Error: SQL file not found at " . $sqlFilePath . "\n");
}

// Use the existing database connection from the original project
require 'config/database.php'; // $pdo is available from here

try {
    echo "Reading SQL file: {$sqlFilePath}\n";
    $sql = file_get_contents($sqlFilePath);

    // 1. Remove comments first
    $sql = preg_replace('/--.*$/m', '', $sql);
    $sql = trim($sql);

    // 2. Add semicolons after CREATE TABLE blocks that end with ')' and a newline.
    $sql = preg_replace('/\)\\s*(\\r\\n|\\n|\\r)/m', ');$1', $sql);

    // 3. Split queries by semicolon, removing empty entries.
    $queries = preg_split('/;/', $sql, -1, PREG_SPLIT_NO_EMPTY);

    $queryCount = 0;
    foreach ($queries as $query) {
        $query = trim($query);
        if (!empty($query)) {
            $pdo->exec($query);
            $queryCount++;
        }
    }

    echo "Successfully executed {$queryCount} queries from {$sqlFilePath}\n";

} catch (PDOException $e) {
    die("Database error: " . $e->getMessage() . "\n");
} catch (Exception $e) {
    die("An error occurred: " . $e->getMessage() . "\n");
}