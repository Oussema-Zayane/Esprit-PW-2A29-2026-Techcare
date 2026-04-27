<?php
require_once __DIR__ . '/../src/Model/FideliteModel.php';

$model = new FideliteModel();

// Tester getAll()
$fidelites = $model->getAll();
echo "<h2>Enregistrements de fidélité :</h2>";
foreach ($fidelites as $f) {
    echo "- " . $f['titre'] . " : " . $f['total'] . " points<br>";
}

// Tester getUserPoints()
$points = $model->getUserPoints(1);
echo "<h2>Points de l'utilisateur 1 : " . $points . "</h2>";
?>