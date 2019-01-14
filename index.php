<?php
require_once('ArrayChecking.php');
$PACKAGES = [
    'A' => [
        'name' => 'A',
        'dependencies' => ['B', 'C'],
    ],
    'B' => [
        'name' => 'B',
        'dependencies' => [],
    ],
    'C' => [
        'name' => 'C',
        'dependencies' => ['B', 'D'],
    ],
    'D' => [
        'name' => 'D',
        'dependencies' => [],
    ]
];
try {
    $entity = new ArrayChecking();
    var_dump($entity->getAllPackageDependencies($PACKAGES, 'A'));
} catch (DependencyExclusion $e) {
    echo $e->getMessage();
} 
