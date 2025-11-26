<?php
// Memasukkan file konfigurasi database sekali saja (require_once)
// __DIR__ . '/../' menghasilkan path absolut ke folder induk dari folder 'includes'
require_once __DIR__ . '/../config/database.php';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> 
    <title>Leksikon</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="<?php echo BASE_PATH; ?>/assets/css/style.css">

    <style>
        body {
            background-color: #f4f7f6;
            background-image: linear-gradient(180deg, #e6e9f0 0%, #eef1f5 100%);
            min-height: 100vh;
        }
    </style>
</head>
<body class="bg-light"> 

<!-- Navbar Utama -->
<nav class="navbar navbar-expand-lg navbar-light shadow-sm navbar-glass sticky-top">
  <div class="container">
    <!-- Brand/Logo Navbar, link ke halaman utama -->
    <a class="navbar-brand" href="<?php echo BASE_PATH; ?>/index.php">Sastra Jawa - Leksikon</a>
    <div class="ms-auto d-flex align-items-center"> <!-- ms-auto: dorong elemen ke kanan -->
        <!-- Link ke halaman Manajemen Entri (CRUD) -->
        <a href="<?php echo BASE_PATH; ?>/admin/index.php" class="btn btn-outline-dark btn-sm me-3">Manajemen Entri (CRUD)</a>
    </div>
  </div>
</nav>

<!-- Kontainer Utama Halaman -->
<div class="container mt-4 page-fade-in">
