<?php
require '../config/database.php';

echo "<h1>Proses Impor Data Leksikon</h1>";

// Baca file JSON
$json_data = file_get_contents("dataEntri.json");
if ($json_data === false) {
    die("<p style='color: red;'>Gagal membaca file data.json. Pastikan file ada di folder yang sama.</p>");
}

// Decode JSON
$data = json_decode($json_data, true);
if ($data === null) {
    die("<p style='color: red;'>Gagal mem-parsing data JSON. Cek format JSON.</p>");
}

// (asumsikan semua data ini masuk sumber ID 1 - 15)
$sumber_id = rand(1, 15);
$stmt = $pdo->prepare("INSERT INTO entri (term, definition, sumber_id) VALUES (?, ?, ?)");

$berhasil = 0;
$gagal = 0;

foreach ($data as $item) {
    try {
        $stmt->execute([
            $item['term'],
            $item['definition'],
            $item['source'],
            $sumber_id
        ]);
        $berhasil++;
    } catch (PDOException $e) {
        echo "<p style='color: red;'>Gagal impor term: " . htmlspecialchars($item['term']) . " - " . $e->getMessage() . "</p>";
        $gagal++;
    }
}

echo "<h2>Impor Selesai!</h2>";
echo "<p style='color: green;'><strong>Berhasil:</strong> $berhasil entri</p>";
echo "<p style='color: red;'><strong>Gagal:</strong> $gagal entri</p>";
?>
