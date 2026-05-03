<?php
$types = [
    ['nom_type' => 'mensuel', 'prix_base' => 29.90, 'duree_jours' => 30],
    ['nom_type' => 'annuel', 'prix_base' => 299.00, 'duree_jours' => 365]
];
$typesArray = [];
if (!empty($types)) {
    foreach ($types as $type) {
        $typesArray[htmlspecialchars($type['nom_type'])] = [
            'prix_base'   => $type['prix_base'],
            'duree_jours' => $type['duree_jours']
        ];
    }
}
echo "OUTPUT:\n";
echo json_encode($typesArray);
?>
