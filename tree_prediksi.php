<?php
include 'db.php';

// Fungsi untuk menghitung entropy
function calculateEntropy($data) {
    $total = count($data);
    $counts = array_count_values(array_column($data, 'status_kelulusan'));
    $entropy = 0;

    foreach ($counts as $count) {
        $probability = $count / $total;
        $entropy -= $probability * log($probability, 2);
    }

    return $entropy;
}

// Fungsi untuk membagi data berdasarkan atribut tertentu
function splitData($data, $attribute, $threshold) {
    $left = [];
    $right = [];

    foreach ($data as $row) {
        if ($row[$attribute] <= $threshold) {
            $left[] = $row;
        } else {
            $right[] = $row;
        }
    }

    return [$left, $right];
}

// Fungsi untuk mencari atribut terbaik untuk split
function findBestSplit($data, $attributes) {
    $baseEntropy = calculateEntropy($data);
    $bestGain = 0;
    $bestAttribute = null;
    $bestThreshold = null;

    foreach ($attributes as $attribute) {
        $values = array_column($data, $attribute);
        $uniqueValues = array_unique($values);

        foreach ($uniqueValues as $threshold) {
            [$left, $right] = splitData($data, $attribute, $threshold);

            if (count($left) > 0 && count($right) > 0) {
                $leftWeight = count($left) / count($data);
                $rightWeight = count($right) / count($data);

                $gain = $baseEntropy - ($leftWeight * calculateEntropy($left) + $rightWeight * calculateEntropy($right));

                if ($gain > $bestGain) {
                    $bestGain = $gain;
                    $bestAttribute = $attribute;
                    $bestThreshold = $threshold;
                }
            }
        }
    }

    return [$bestAttribute, $bestThreshold];
}

// Fungsi untuk membangun decision tree secara rekursif
function buildTree($data, $attributes) {
    $statuses = array_column($data, 'status_kelulusan');

    if (count(array_unique($statuses)) === 1) {
        return ['label' => $statuses[0]];
    }

    if (empty($attributes)) {
        return ['label' => array_count_values($statuses)];
    }

    [$bestAttribute, $bestThreshold] = findBestSplit($data, $attributes);

    if ($bestAttribute === null) {
        return ['label' => array_count_values($statuses)];
    }

    [$left, $right] = splitData($data, $bestAttribute, $bestThreshold);

    $attributes = array_filter($attributes, fn($attr) => $attr !== $bestAttribute);

    return [
        'attribute' => $bestAttribute,
        'threshold' => $bestThreshold,
        'left' => buildTree($left, $attributes),
        'right' => buildTree($right, $attributes),
    ];
}

// Fungsi untuk memprediksi data baru
function predict($tree, $data) {
    if (isset($tree['label'])) {
        return $tree['label'];
    }

    if ($data[$tree['attribute']] <= $tree['threshold']) {
        return predict($tree['left'], $data);
    } else {
        return predict($tree['right'], $data);
    }
}

// Ambil data dari database
$sql = "SELECT ipk, jumlah_sks, jumlah_semester, status_kelulusan FROM mahasiswa_kelulusan";
$result = $conn->query($sql);

$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

// Atribut untuk tree
$attributes = ['ipk', 'jumlah_sks', 'jumlah_semester'];

// Bangun decision tree
$tree = buildTree($data, $attributes);

// Ambil input dari form
$input = [
    'ipk' => (float) $_POST['ipk'],
    'jumlah_sks' => (int) $_POST['jumlah_sks'],
    'jumlah_semester' => (int) $_POST['jumlah_semester']
];

// Prediksi data baru
$prediction = predict($tree, $input);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hasil Prediksi</title>
</head>
<body>
    <script>
        alert("Hasil Prediksi:\nIPK: <?= htmlspecialchars($input['ipk']) ?>\nJumlah SKS: <?= htmlspecialchars($input['jumlah_sks']) ?>\nJumlah Semester: <?= htmlspecialchars($input['jumlah_semester']) ?>\nPrediksi: <?= $prediction ?>");
        window.history.back();
    </script>
</body>
</html>
