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

// Ambil input dari form
$input = [
    'ipk' => (float) $_POST['ipk'],
    'jumlah_sks' => (int) $_POST['jumlah_sks'],
    'jumlah_semester' => (int) $_POST['jumlah_semester']
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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hasil Prediksi</title>
</head>
<head>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</head>
<body>
    <div class="modal fade show" id="resultModal" tabindex="-1" aria-labelledby="resultModalLabel" aria-modal="true" role="dialog" style="display: block;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="resultModalLabel">Hasil Prediksi</h5>
                </div>
                <div class="modal-body">
                    <p>IPK: <span class="fw-bold"><?= htmlspecialchars($input['ipk']) ?></span></p>
                    <p>Jumlah SKS: <span class="fw-bold"><?= htmlspecialchars($input['jumlah_sks']) ?></span></p>
                    <p>Jumlah Semester: <span class="fw-bold"><?= htmlspecialchars($input['jumlah_semester']) ?></span></p>
                    <h2>Prediksi: <span class="text-primary"><?= $prediction ?></span></h2>
                </div>
                <div class="modal-footer">
                    <a href="javascript:window.history.back();" class="btn btn-primary">Kembali</a>
                </div>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var modal = new bootstrap.Modal(document.getElementById('resultModal'));
            modal.show();

            setTimeout(function() {
                window.history.go(-1);
            }, 3000); // Redirect after 3 seconds
        });
    </script>
</body>
</html>
