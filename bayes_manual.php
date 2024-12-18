<?php
include 'db.php';

// Fungsi untuk menghitung probabilitas
function calculateProbability($value, $mean, $stdDev) {
    $exponent = exp(-pow($value - $mean, 2) / (2 * pow($stdDev, 2)));
    return (1 / (sqrt(2 * pi()) * $stdDev)) * $exponent;
}

// Ambil data dari database
$sql = "SELECT ipk, jumlah_sks, jumlah_semester, status_kelulusan FROM mahasiswa_kelulusan";
$result = $conn->query($sql);

$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

// Pisahkan data berdasarkan kelas
$tepatWaktu = array_filter($data, fn($d) => $d['status_kelulusan'] === 'Tepat Waktu');
$tidakTepatWaktu = array_filter($data, fn($d) => $d['status_kelulusan'] === 'Tidak Tepat Waktu');

// Hitung mean dan standar deviasi untuk setiap fitur
function stats($data, $key) {
    $values = array_column($data, $key);
    $mean = array_sum($values) / count($values);
    $stdDev = sqrt(array_sum(array_map(fn($v) => pow($v - $mean, 2), $values)) / count($values));
    return ['mean' => $mean, 'stdDev' => $stdDev];
}

$features = ['ipk', 'jumlah_sks', 'jumlah_semester'];
$statsTepat = [];
$statsTidakTepat = [];

foreach ($features as $feature) {
    $statsTepat[$feature] = stats($tepatWaktu, $feature);
    $statsTidakTepat[$feature] = stats($tidakTepatWaktu, $feature);
}

// Input data baru
$input = [
    'ipk' => 3.9,
    'jumlah_sks' => 128,
    'jumlah_semester' => 10
];

// Hitung probabilitas untuk setiap kelas
$probTepat = count($tepatWaktu) / count($data);
$probTidakTepat = count($tidakTepatWaktu) / count($data);

foreach ($features as $feature) {
    $probTepat *= calculateProbability($input[$feature], $statsTepat[$feature]['mean'], $statsTepat[$feature]['stdDev']);
    $probTidakTepat *= calculateProbability($input[$feature], $statsTidakTepat[$feature]['mean'], $statsTidakTepat[$feature]['stdDev']);
}

// Tentukan kelas dengan probabilitas tertinggi
if ($probTepat > $probTidakTepat) {
    $prediction = "Tepat Waktu";
} else {
    $prediction = "Tidak Tepat Waktu";
}

echo "Hasil Prediksi: $prediction";
?>
