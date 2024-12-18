<?php
include 'db.php'; // Menghubungkan ke database

function getData($pdo) {
    $stmt = $pdo->query("SELECT * FROM mahasiswa");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function naiveBayes($data, $input) {
    $total = count($data);
    $lulusCount = 0;
    $tidakLulusCount = 0;

    // Hitung jumlah lulus dan tidak lulus
    foreach ($data as $row) {
        if ($row['lulus'] == 'ya') {
            $lulusCount++;
        } else {
            $tidakLulusCount++;
        }
    }

    if ($total == 0) {
        throw new DivisionByZeroError('Division by zero');
    }

    $probLulus = $lulusCount / $total;
    $probTidakLulus = $tidakLulusCount / $total;

    // Hitung probabilitas untuk setiap fitur
    $probFiturLulus = $probFiturTidakLulus = 1;

    foreach ($data as $row) {
        if ($row['lulus'] == 'ya') {
            $probFiturLulus *= ($row['ipk'] == $input['ipk'] ? 1 : 0);
            $probFiturLulus *= ($row['kehadiran'] == $input['kehadiran'] ? 1 : 0);
            $probFiturLulus *= ($row['tugas'] == $input['tugas'] ? 1 : 0);
        } else {
            $probFiturTidakLulus *= ($row['ipk'] == $input['ipk'] ? 1 : 0);
            $probFiturTidakLulus *= ($row['kehadiran'] == $input['kehadiran'] ? 1 : 0);
            $probFiturTidakLulus *= ($row['tugas'] == $input['tugas'] ? 1 : 0);
        }
    }

    // Hitung probabilitas akhir
    $probabilitasLulus = $probLulus * $probFiturLulus;
    $probabilitasTidakLulus = $probTidakLulus * $probFiturTidakLulus;

    // Lakukan normalisasi
    $totalProbabilitas = $probabilitasLulus + $probabilitasTidakLulus;
    if ($totalProbabilitas == 0) {
        throw new DivisionByZeroError('Division by zero');
    }
    $probabilitasLulus /= $totalProbabilitas;
    $probabilitasTidakLulus /= $totalProbabilitas;

    return $probabilitasLulus > $probabilitasTidakLulus ? 'Lulus' : 'Tidak Lulus';
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $input = [
        'ipk' => $_POST['ipk'],
        'kehadiran' => $_POST['kehadiran'],
        'tugas' => $_POST['tugas']
    ];

    try {
        $data = getData($pdo);
        $hasil = naiveBayes($data, $input);
        echo "Prediksi kelulusan: " . $hasil;
    } catch (DivisionByZeroError $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prediksi Kelulusan Mahasiswa</title>
    <link rel="stylesheet" href="style.css"> <!-- Jika Anda menggunakan CSS -->
    <style>
        .container {
            display: flex;
        }
        .column {
            flex: 1;
            padding: 10px;
        }
    </style>
</head>
<body>
    <h1>Prediksi Kelulusan Mahasiswa</h1>
    <div class="container">
        <div class="column">
            <h2>Input Data</h2>
            <form method="post">
                <label for="ipk">IPK:</label>
                <input type="text" name="ipk" required>
                <br>
                <label for="kehadiran">Kehadiran:</label>
                <input type="text" name="kehadiran" required>
                <br>
                <label for="tugas">Tugas:</label>
                <input type="text" name="tugas" required>
                <br>
                <input type="submit" value="Prediksi">
            </form>
        </div>
        <div class="column">
            <h2>Data Mahasiswa</h2>
            <table border="1">
                <tr>
                    <th>Nama</th>
                    <th>IPK</th>
                    <th>Kehadiran</th>
                    <th>Tugas</th>
                    <th>Lulus</th>
                </tr>
                <?php
                $dataMahasiswa = getData($pdo);
                foreach ($dataMahasiswa as $mahasiswa) {
                    echo "<tr>";
                    echo "<td>{$mahasiswa['nama']}</td>";
                    echo "<td>{$mahasiswa['ipk']}</td>";
                    echo "<td>{$mahasiswa['kehadiran']}</td>";
                    echo "<td>{$mahasiswa['tugas']}</td>";
                    echo "<td>{$mahasiswa['lulus']}</td>";
                    echo "</tr>";
                }
                ?>
            </table>
        </div>
    </div>
</body>
</html>

