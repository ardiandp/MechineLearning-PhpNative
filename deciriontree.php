<?php

class DecisionTree {
    private $maxDepth;
    
    public function __construct($maxDepth = 5) {
        $this->maxDepth = $maxDepth;
    }

    // Fungsi untuk menghitung entropi dari dataset
    private function entropy($data) {
        $classCounts = array();
        $total = count($data);
        
        // Menghitung frekuensi setiap label kelas
        foreach ($data as $row) {
            $class = end($row);
            if (!isset($classCounts[$class])) {
                $classCounts[$class] = 0;
            }
            $classCounts[$class]++;
        }
        
        // Menghitung entropi
        $entropy = 0;
        foreach ($classCounts as $count) {
            $probability = $count / $total;
            $entropy -= $probability * log($probability, 2);
        }
        return $entropy;
    }

    // Fungsi untuk menghitung informasi gain dari atribut
    private function informationGain($data, $attributeIndex) {
        $totalEntropy = $this->entropy($data);
        $attributeValues = array();
        
        // Mengelompokkan data berdasarkan nilai atribut
        foreach ($data as $row) {
            $value = $row[$attributeIndex];
            if (!isset($attributeValues[$value])) {
                $attributeValues[$value] = array();
            }
            $attributeValues[$value][] = $row;
        }
        
        // Menghitung entropi tertimbang untuk setiap subset
        $weightedEntropy = 0;
        foreach ($attributeValues as $subset) {
            $subsetProbability = count($subset) / count($data);
            $weightedEntropy += $subsetProbability * $this->entropy($subset);
        }
        
        // Information gain adalah pengurangan entropi
        return $totalEntropy - $weightedEntropy;
    }

    // Fungsi untuk menemukan atribut terbaik untuk membagi data
    private function bestSplit($data) {
        $bestGain = -1;
        $bestAttribute = -1;
        
        // Mencoba semua atribut dan menghitung information gain
        $numAttributes = count($data[0]) - 1; // Mengabaikan kelas
        for ($i = 0; $i < $numAttributes; $i++) {
            $gain = $this->informationGain($data, $i);
            if ($gain > $bestGain) {
                $bestGain = $gain;
                $bestAttribute = $i;
            }
        }
        
        return $bestAttribute;
    }

    // Fungsi untuk membangun pohon keputusan secara rekursif
    private function buildTree($data, $depth) {
        $numRows = count($data);
        
        // Hentikan jika kedalaman maksimal tercapai atau data sudah murni
        if ($depth >= $this->maxDepth || $this->entropy($data) == 0) {
            $classCounts = array();
            foreach ($data as $row) {
                $class = end($row);
                if (!isset($classCounts[$class])) {
                    $classCounts[$class] = 0;
                }
                $classCounts[$class]++;
            }
            // Mengembalikan kelas mayoritas
            arsort($classCounts);
            return key($classCounts);
        }
        
        // Mencari atribut terbaik untuk membagi data
        $bestAttribute = $this->bestSplit($data);
        
        // Membagi dataset berdasarkan atribut terbaik
        $attributeValues = array();
        foreach ($data as $row) {
            $value = $row[$bestAttribute];
            if (!isset($attributeValues[$value])) {
                $attributeValues[$value] = array();
            }
            $attributeValues[$value][] = $row;
        }
        
        // Membangun pohon secara rekursif untuk setiap subset
        $subtrees = array();
        foreach ($attributeValues as $value => $subset) {
            $subtrees[$value] = $this->buildTree($subset, $depth + 1);
        }
        
        return [
            'attribute' => $bestAttribute,
            'subtrees' => $subtrees
        ];
    }

    // Fungsi untuk mengklasifikasikan sebuah titik data menggunakan pohon keputusan
    public function classify($tree, $dataPoint) {
        if (is_array($tree)) {
            // Menavigasi pohon berdasarkan nilai atribut
            $attributeValue = $dataPoint[$tree['attribute']];
            if (isset($tree['subtrees'][$attributeValue])) {
                return $this->classify($tree['subtrees'][$attributeValue], $dataPoint);
            } else {
                return null; // Kelas tidak diketahui jika tidak ada kecocokan
            }
        } else {
            // Jika ini adalah node daun, kembalikan label kelas
            return $tree;
        }
    }

    // Fungsi untuk melatih model
    public function train($data) {
        return $this->buildTree($data, 0);
    }
}

// Contoh dataset (Kolom terakhir adalah label kelas)
$data = [
    [1, 1, 1, 'A'],
    [1, 1, 0, 'B'],
    [1, 0, 1, 'A'],
    [1, 0, 0, 'B'],
    [0, 1, 1, 'B'],
    [0, 1, 0, 'A'],
    [0, 0, 1, 'A'],
    [0, 0, 0, 'B']
];

// Melatih pohon keputusan
$tree = (new DecisionTree())->train($data);

// Mengklasifikasikan titik data baru
$newDataPoint = [1, 0, 1]; // Contoh input
$predictedClass = (new DecisionTree())->classify($tree, $newDataPoint);

echo "Prediksi Kelas: $predictedClass\n";
?>
