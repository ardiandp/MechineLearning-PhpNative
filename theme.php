<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Mining App</title>
    <!-- Bootstrap CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome for Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            padding-top: 20px;
        }
        .navbar {
            background-color: #007bff;
        }
        .navbar-brand, .navbar-nav .nav-link {
            color: #fff;
        }
        .card {
            margin-bottom: 20px;
        }
        footer {
            text-align: center;
            padding: 10px;
            background-color: #007bff;
            color: #fff;
            position: fixed;
            bottom: 0;
            width: 100%;
        }
    </style>
</head>
<body>
    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="?page=beranda">Data Mining App</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item"><a class="nav-link" href="?page=decision_tree">C4.5</a></li>
                <li class="nav-item"><a class="nav-link" href="?page=prediksi_kelulusan">Prediksi Kelulusan</a></li>
                <li class="nav-item"><a class="nav-link" href="#knn">K-NN</a></li>
            </ul>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="#input">Input Data</a></li>
                    <li class="nav-item"><a class="nav-link" href="#results">Results</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <?php
    if (isset($_GET['page'])) {
        $page = $_GET['page'];
        if (file_exists($page . '.php')) {
            include $page . '.php';
        } else {
            echo "Halaman tidak ditemukan";
        }
    } else {
        include 'beranda.php';
    }
    ?>

    <!-- Footer -->
    <footer>
        &copy; 2024 Data Mining App. All Rights Reserved.
    </footer>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Custom Script -->
    <script>
        document.getElementById('processData').addEventListener('click', function() {
            const dataInput = document.getElementById('dataInput').value;
            if (!dataInput) {
                alert('Please enter data to process!');
                return;
            }
            // Simple processing simulation
            document.getElementById('resultText').textContent = "Data processed successfully!";
        });
    </script>
</body>
</html>
