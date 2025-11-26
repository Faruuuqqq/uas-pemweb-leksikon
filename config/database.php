<?php
session_start();

define("BASE_PATH", "/uts-pemweb-leksikon");

$host = "127.0.0.1";
$dbname = "leksikon";
$username = "root";
$password = "mysql";

try {
  $pdo = new PDO(
    "mysql:host=$host;port=3306;dbname=$dbname;charset=utf8mb4",
    $username,
    $password,
  );

  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
  die("Koneksi database gagal: " . $e->getMessage());
}
?>
