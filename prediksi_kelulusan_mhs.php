<?php

// Koneksi ke database MySQL
$servername = "localhost";
$username = "root"; // Ganti dengan username database Anda
$password = ""; // Ganti dengan password database Anda
$dbname = "decision_tree";

$conn = new mysqli($servername, $username, $password, $dbname);

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

$conn->close();
?>

<!-- Formulir input untuk data pengujian kelulusan mahasiswa -->
<form method="POST">
    IPK: <input type="number" step="0.01" name="ipk" required><br>
    SKS: <input type="number" name="sks" required><br>
    Keaktifan (1-5): <input type="number" name="keaktifan" min="1" max="5" required><br>
    Presensi (%): <input type="number" name="presensi" min="0" max="100" required><br>
    <input type="submit" value="Prediksi Kelulusan">
</form>
