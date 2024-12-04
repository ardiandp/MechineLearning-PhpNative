<?php

require 'database/config.php';

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

class DecisionTree {
    private $maxDepth;
    
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
$sql = "SELECT * FROM training_data";
$result = $conn->query($sql);

$data = [];
while ($row = $result->fetch_assoc()) {
    // Mengambil semua kolom kecuali kelas untuk data
    $data[] = array_values($row);
}

// Latih pohon keputusan
$tree = (new DecisionTree())->train($data);

// Ambil inputan pengguna untuk pengujian
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $feature1 = $_POST['feature1'];
    $feature2 = $_POST['feature2'];
    $feature3 = $_POST['feature3'];

    // Klasifikasikan input pengguna
    $newDataPoint = [$feature1, $feature2, $feature3];
    $predictedClass = (new DecisionTree())->classify($tree, $newDataPoint);
    //echo "Prediksi Kelas: $predictedClass<br>";
}


?>

<style>
    .container {
        display: flex;
        flex-direction: row;
    }
    .column {
        flex: 1;
    }
</style>

<div class="container">
    <div class="column" style="border: 1px solid #ccc; border-radius: 5px; padding: 10px; margin: 10px;">
        <!-- Formulir input untuk data pengujian -->
        <form method="POST" style="display: flex; flex-direction: column; align-items: center;">
            <label style="margin-bottom: 10px;">Fitur 1:</label>
            <input type="text" name="feature1" required style="width: 100%; padding: 10px; margin-bottom: 10px; border: 1px solid #ccc; border-radius: 5px;">
            <label style="margin-bottom: 10px;">Fitur 2:</label>
            <input type="text" name="feature2" required style="width: 100%; padding: 10px; margin-bottom: 10px; border: 1px solid #ccc; border-radius: 5px;">
            <label style="margin-bottom: 10px;">Fitur 3:</label>
            <input type="text" name="feature3" required style="width: 100%; padding: 10px; margin-bottom: 10px; border: 1px solid #ccc; border-radius: 5px;">
            <input type="submit" value="Klasifikasikan" style="width: 100%; padding: 10px; background-color: #4CAF50; color: white; border: none; border-radius: 5px; cursor: pointer;">
        </form>
    </div>
    <div class="column">
        <link rel="stylesheet" href="https://cdn.datatables.net/1.10.22/css/jquery.dataTables.min.css">
        <script src="https://code.jquery.com/jquery-3.5.1.js"></script>
        <script src="https://cdn.datatables.net/1.10.22/js/jquery.dataTables.min.js"></script>
        <table id="table" class="display">
            <thead>
                <tr>
                    <th>Fitur 1</th>
                    <th>Fitur 2</th>
                    <th>Fitur 3</th>
                    <th>Kelas</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $sql = "SELECT * FROM training_data";
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $row['feature1'] . "</td>";
                        echo "<td>" . $row['feature2'] . "</td>";
                        echo "<td>" . $row['feature3'] . "</td>";
                        echo "<td>" . $row['class'] . "</td>";
                        echo "</tr>";
                    }
                }
                $conn->close();
                ?>
            </tbody>
        </table>
        <script>
            $(document).ready(function() {
                $('#table').DataTable();
            } );
        </script>
    </div>

    <div class="column" style="border: 1px solid #ccc; border-radius: 5px; padding: 10px; margin: 10px;">
        <!-- Hasil klasifikasi -->
        <?php
        if (isset($predictedClass)) {
            echo "Prediksi Kelas: $predictedClass<br>";
        }
        ?>
    </div>
</div>
