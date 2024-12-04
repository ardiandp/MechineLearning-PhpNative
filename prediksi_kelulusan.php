<?php

require 'database/config.php';

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

class DecisionTree {
    private $maxDepth;
    
/*************  ✨ Codeium Command ⭐  *************/
    /**
     * Constructor for DecisionTree class.
     *
     * @param int $maxDepth The maximum depth of the decision tree. Default is 5.
     */
/******  50353b55-c498-4e66-b095-055de4f47b6c  *******/    
    public function __construct($maxDepth = 5) {
        $this->maxDepth = $maxDepth;
    }

    // Fungsi untuk menghitung entropi dari dataset
    private function entropy($data) {
        $classCounts = array();
        $total = count($data);
        
        foreach ($data as $row) {
            $class = end($row);
            if (!isset($classCounts[$class])) {
                $classCounts[$class] = 0;
            }
            $classCounts[$class]++;
        }
        
        $entropy = 0;
        foreach ($classCounts as $count) {
            $probability = $count / $total;
            $entropy -= $probability * log($probability, 2);
        }
        return $entropy;
    }

    // Fungsi untuk menghitung information gain
    private function informationGain($data, $attributeIndex) {
        $totalEntropy = $this->entropy($data);
        $attributeValues = array();
        
        foreach ($data as $row) {
            $value = $row[$attributeIndex];
            if (!isset($attributeValues[$value])) {
                $attributeValues[$value] = array();
            }
            $attributeValues[$value][] = $row;
        }
        
        $weightedEntropy = 0;
        foreach ($attributeValues as $subset) {
            $subsetProbability = count($subset) / count($data);
            $weightedEntropy += $subsetProbability * $this->entropy($subset);
        }
        
        return $totalEntropy - $weightedEntropy;
    }

    // Fungsi untuk memilih atribut terbaik untuk membagi data
    private function bestSplit($data) {
        $bestGain = -1;
        $bestAttribute = -1;
        
        $numAttributes = count($data[0]) - 1;
        for ($i = 0; $i < $numAttributes; $i++) {
            $gain = $this->informationGain($data, $i);
            if ($gain > $bestGain) {
                $bestGain = $gain;
                $bestAttribute = $i;
            }
        }
        
        return $bestAttribute;
    }

    // Fungsi untuk membangun pohon secara rekursif
    private function buildTree($data, $depth) {
        $numRows = count($data);
        
        if ($depth >= $this->maxDepth || $this->entropy($data) == 0) {
            $classCounts = array();
            foreach ($data as $row) {
                $class = end($row);
                if (!isset($classCounts[$class])) {
                    $classCounts[$class] = 0;
                }
                $classCounts[$class]++;
            }
            arsort($classCounts);
            return key($classCounts);
        }
        
        $bestAttribute = $this->bestSplit($data);
        
        $attributeValues = array();
        foreach ($data as $row) {
            $value = $row[$bestAttribute];
            if (!isset($attributeValues[$value])) {
                $attributeValues[$value] = array();
            }
            $attributeValues[$value][] = $row;
        }
        
        $subtrees = array();
        foreach ($attributeValues as $value => $subset) {
            $subtrees[$value] = $this->buildTree($subset, $depth + 1);
        }
        
        return [
            'attribute' => $bestAttribute,
            'subtrees' => $subtrees
        ];
    }

    // Fungsi untuk mengklasifikasikan data baru
    public function classify($tree, $dataPoint) {
        if (is_array($tree)) {
            $attributeValue = $dataPoint[$tree['attribute']];
            if (isset($tree['subtrees'][$attributeValue])) {
                return $this->classify($tree['subtrees'][$attributeValue], $dataPoint);
            } else {
                return null;
            }
        } else {
            return $tree;
        }
    }

    // Fungsi untuk melatih model
    public function train($data) {
        return $this->buildTree($data, 0);
    }
}

// Ambil data pelatihan dari database
$sql = "SELECT * FROM student_data";
$result = $conn->query($sql);

$data = [];
while ($row = $result->fetch_assoc()) {
    // Mengambil semua kolom kecuali status (label kelas) untuk data
    $data[] = array_values($row);
}

// Latih pohon keputusan
$tree = (new DecisionTree())->train($data);

// Ambil inputan pengguna untuk pengujian
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $ipk = $_POST['ipk'];
    $sks = $_POST['sks'];
    $keaktifan = $_POST['keaktifan'];
    $presensi = $_POST['presensi'];

    // Klasifikasikan input pengguna
    $newDataPoint = [$ipk, $sks, $keaktifan, $presensi];
    $predictedStatus = (new DecisionTree())->classify($tree, $newDataPoint);
    echo "Prediksi Status Kelulusan: $predictedStatus<br>";
}


?>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
<script src="https://cdn.jsdelivr.net/npm/jquery@3.6.1/dist/jquery.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.min.js"></script>

<style>
    .container {
        display: flex;
        flex-direction: row;
    }
    .column {
        flex: 1;
    }
    table {
        border-collapse: collapse;
        width: 100%;
    }
    th, td {
        text-align: left;
        padding: 8px;
    }
    tr:nth-child(even) {
        background-color: #f2f2f2;
    }
    th {
        background-color: #4CAF50;
        color: white;
    }
</style>

<div class="container">
    <div class="row"
    <div class="column">
        <!-- Formulir input untuk data pengujian kelulusan mahasiswa -->
        <form method="POST" class="needs-validation" novalidate>
            <div class="form-group">
                <label for="ipk">IPK:</label>
                <input type="number" step="0.01" class="form-control" id="ipk" name="ipk" required>
                <div class="invalid-feedback">IPK harus diisi!</div>
            </div>
            <div class="form-group">
                <label for="sks">SKS:</label>
                <input type="number" class="form-control" id="sks" name="sks" required>
                <div class="invalid-feedback">SKS harus diisi!</div>
            </div>
            <div class="form-group">
                <label for="keaktifan">Keaktifan (1-5):</label>
                <input type="number" class="form-control" id="keaktifan" name="keaktifan" min="1" max="5" required>
                <div class="invalid-feedback">Keaktifan harus diisi!</div>
            </div>
            <div class="form-group">
                <label for="presensi">Presensi (%):</label>
                <input type="number" class="form-control" id="presensi" name="presensi" min="0" max="100" required>
                <div class="invalid-feedback">Presensi harus diisi!</div>
            </div>
            <button type="submit" class="btn btn-primary">Prediksi Kelulusan</button>
        </form>
    </div>
    <div class="column">
</div>

<div class="container">
    <div class="column">
        <h2>Data Siswa</h2>
        <table class="table">
            <thead>
                <tr>
                    <th>IPK</th>
                    <th>SKS</th>
                    <th>Keaktifan</th>
                    <th>Presensi</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $sql = "SELECT * FROM student_data";
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $row['ipk'] . "</td>";
                        echo "<td>" . $row['sks'] . "</td>";
                        echo "<td>" . $row['keaktifan'] . "</td>";
                        echo "<td>" . $row['presensi'] . "</td>";
                        echo "<td>" . $row['status'] . "</td>";
                        echo "</tr>";
                    }
                }
                $conn->close();
                ?>
            </tbody>
        </table>
    </div>
</div>
</div>

<script>
    (function() {
        'use strict';
        window.addEventListener('load', function() {
            var forms = document.getElementsByClassName('needs-validation');
            var validation = Array.prototype.filter.call(forms, function(form) {
                form.addEventListener('submit', function(event) {
                    if (form.checkValidity() === false) {
                        event.preventDefault();
                        event.stopPropagation();
                    }
                    form.classList.add('was-validated');
                }, false);
            });
        }, false);
    })();
</script>

