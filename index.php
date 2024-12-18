<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prediksi Kelulusan Mahasiswa</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
</head>
<body>
    <div class="container">
        <div class="row">
            <div class="col-md-6">
                <h1 class="mb-4">Prediksi Kelulusan Mahasiswa</h1>
                <form action="prediksi.php" method="POST" class="needs-validation" novalidate>
                    <div class="form-group">
                        <label for="ipk">IPK:</label>
                        <input type="number" step="0.01" id="ipk" name="ipk" class="form-control" required>
                        <div class="invalid-feedback">Silakan masukkan IPK yang valid.</div>
                    </div>
                    <div class="form-group">
                        <label for="jumlah_sks">Jumlah SKS:</label>
                        <input type="number" id="jumlah_sks" name="jumlah_sks" class="form-control" required>
                        <div class="invalid-feedback">Silakan masukkan jumlah SKS yang valid.</div>
                    </div>
                    <div class="form-group">
                        <label for="jumlah_semester">Jumlah Semester:</label>
                        <input type="number" id="jumlah_semester" name="jumlah_semester" class="form-control" required>
                        <div class="invalid-feedback">Silakan masukkan jumlah semester yang valid.</div>
                    </div>
                    <button type="submit" class="btn btn-primary mt-3">Proses</button>
                </form>
            </div>
            <div class="col-md-6">
                <h2>Data Mahasiswa</h2>
                <?php
                include 'db.php';

                $sql = "SELECT * FROM mahasiswa_kelulusan";
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    echo "<table class='table table-striped'>";
                    echo "<tr><th>Nama</th><th>IPK</th><th>Jumlah SKS</th><th>Jumlah Semester</th><th>Status Kelulusan</th></tr>";
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                       
                        echo "<td>" . $row["nama"] . "</td>";
                        echo "<td>" . $row["ipk"] . "</td>";
                        echo "<td>" . $row["jumlah_sks"] . "</td>";
                        echo "<td>" . $row["jumlah_semester"] . "</td>";
                        echo "<td>" . $row["status_kelulusan"] . "</td>";
                        echo "</tr>";
                    }
                    echo "</table>";
                } else {
                    echo "Tidak ada data";
                }

                $conn->close();
                ?>

