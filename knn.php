<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prediksi Kelulusan - K-NN</title>
</head>
<body>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" integrity="sha384-GJzZqFGwb1QTTN6wy59ffF1BuGJpLSa9DkKMp0DgiMDm4iYMj70gZWKYbI706tWS" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.1/dist/jquery.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-Fy6S3B9q64WdZWQUiU+q4/2Lc9npb8tCaSX9FK7E8HnRr0JSSv3+3yRg2pLg99Vv/xTb" crossorigin="anonymous"></script>
    <div class="container">
        <div class="row">
            <div class="col">
                <h1>Prediksi Kelulusan Mahasiswa (K-NN)</h1>
                <form action="?page=knn_prediksi" method="POST">
                    <div class="form-group">
                        <label for="ipk">IPK:</label>
                        <input type="number" step="0.01" class="form-control" id="ipk" name="ipk" required>
                    </div>
                    <div class="form-group">
                        <label for="jumlah_sks">Jumlah SKS:</label>
                        <input type="number" class="form-control" id="jumlah_sks" name="jumlah_sks" required>
                    </div>
                    <div class="form-group">
                        <label for="jumlah_semester">Jumlah Semester:</label>
                        <input type="number" class="form-control" id="jumlah_semester" name="jumlah_semester" required>
                    </div>
                    <div class="form-group">
                        <label for="k">Jumlah K terdekat:</label>
                        <input type="number" class="form-control" id="k" name="k" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Proses</button>
                </form>
            </div>
            <div class="col">
                <h1>Dataset Mahasiswa</h1>
                <table class="table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>IPK</th>
                            <th>Jumlah SKS</th>
                            <th>Jumlah Semester</th>
                            <th>Status Kelulusan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        include 'db.php';

                        $sql = "SELECT * FROM mahasiswa_kelulusan ORDER BY ipk DESC";
                        $result = $conn->query($sql);

                        if ($result->num_rows > 0) {
                            $no = 1;
                            while ($row = $result->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td>" . $no . "</td>";
                                echo "<td>" . $row['ipk'] . "</td>";
                                echo "<td>" . $row['jumlah_sks'] . "</td>";
                                echo "<td>" . $row['jumlah_semester'] . "</td>";
                                echo "<td>" . $row['status_kelulusan'] . "</td>";
                                echo "</tr>";
                                $no++;
                            }
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
