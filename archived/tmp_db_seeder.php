<?php
require_once 'config/database.php';

try {
    $sql = file_get_contents('database/seed/seed_leksikon_safe_batch.sql');
    $pdo->exec($sql);
    echo "Database seeded successfully.";
} catch (PDOException $e) {
    die("Error seeding database: " . $e->getMessage());
}
?>