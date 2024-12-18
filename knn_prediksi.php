<?php
include 'db.php';

// Fungsi untuk menghitung jarak Euclidean antara dua vektor
function euclideanDistance($a, $b) {
    return sqrt(pow($a['ipk'] - $b['ipk'], 2) + pow($a['jumlah_sks'] - $b['jumlah_sks'], 2) + pow($a['jumlah_semester'] - $b['jumlah_semester'], 2));
}

// Fungsi untuk mencari k-nearest neighbors
function kNearestNeighbors($data, $input, $k) {
    usort($data, fn($a, $b) => euclideanDistance($a, $input) <=> euclideanDistance($b, $input));
    return array_slice($data, 0, $k);
}

// Fungsi untuk memprediksi kelas berdasarkan k-nearest neighbors
function predict($neighbors) {
    $counts = ['Tepat Waktu' => 0, 'Tidak Tepat Waktu' => 0];

    foreach ($neighbors as $neighbor) {
        $counts[$neighbor['status_kelulusan']]++;
    }

    return array_keys($counts, max($counts))[0];
}

// Ambil data dari database
$sql = "SELECT ipk, jumlah_sks, jumlah_semester, status_kelulusan FROM mahasiswa_kelulusan";
$result = $conn->query($sql);

$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

// Ambil input dari form
$input = [
    'ipk' => (float) $_POST['ipk'],
    'jumlah_sks' => (int) $_POST['jumlah_sks'],
    'jumlah_semester' => (int) $_POST['jumlah_semester']
];
$k = (int) $_POST['k'];

// Temukan k-nearest neighbors
$neighbors = kNearestNeighbors($data, $input, $k);

// Prediksi berdasarkan k-nearest neighbors
$prediction = predict($neighbors);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hasil Prediksi</title>
</head>
<head>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const modalHtml = `
                <div class="modal fade" id="predictionModal" tabindex="-1" aria-labelledby="predictionModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="predictionModalLabel">Hasil Prediksi</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <p>IPK: <?= htmlspecialchars($input['ipk']) ?></p>
                                <p>Jumlah SKS: <?= htmlspecialchars($input['jumlah_sks']) ?></p>
                                <p>Jumlah Semester: <?= htmlspecialchars($input['jumlah_semester']) ?></p>
                                <p>Jumlah K Terdekat: <?= htmlspecialchars($k) ?></p>
                                <h2>Prediksi: <?= $prediction ?></h2>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            document.body.insertAdjacentHTML('beforeend', modalHtml);
            const predictionModal = new bootstrap.Modal(document.getElementById('predictionModal'));
            predictionModal.show();
            document.getElementById('predictionModal').addEventListener('hidden.bs.modal', function () {
                window.history.back();
            });
        });
    </script>
</body>
</html>
