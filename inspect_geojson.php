<?php

$filePath = __DIR__ . '/database/data/amenities_samarinda.geojson';
if (!file_exists($filePath)) {
    echo "File not found\n";
    exit;
}

$geoJson = json_decode(file_get_contents($filePath), true);
$features = $geoJson['features'] ?? [];

$amenityTypes = [];
$shopTypes = [];
$otherTypes = [];

foreach ($features as $feature) {
    $props = $feature['properties'] ?? [];
    if (isset($props['amenity'])) {
        $amenityTypes[$props['amenity']] = ($amenityTypes[$props['amenity']] ?? 0) + 1;
    }
    if (isset($props['shop'])) {
        $shopTypes[$props['shop']] = ($shopTypes[$props['shop']] ?? 0) + 1;
    }
    foreach ($props as $key => $val) {
        if (!in_array($key, ['name', 'amenity', 'shop', 'id', 'type', '@id'])) {
            $otherTypes[$key] = ($otherTypes[$key] ?? 0) + 1;
        }
    }
}

echo "AMENITY TYPES:\n";
arsort($amenityTypes);
print_r(array_slice($amenityTypes, 0, 20));

echo "\nSHOP TYPES:\n";
arsort($shopTypes);
print_r(array_slice($shopTypes, 0, 20));

echo "\nOTHER POTENTIAL CLASSIFIERS:\n";
arsort($otherTypes);
print_r(array_slice($otherTypes, 0, 10));
