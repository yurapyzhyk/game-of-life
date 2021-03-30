<?php

require './src/Universe.php';

$defaultDimension = 25;
$minimumDimension = 5;
$dimension = $defaultDimension;

$silentMode = in_array('--silent', $argv);

if ($silentMode && count($argv) > 2) {
    $dimension = $argv[1];
}

if ($dimension < $minimumDimension) {
    throw new Exception("Specified dimension of $dimension is not allowable. Minimum - $minimumDimension");
}

$universe = new Universe($dimension);
$universe->initialize('glider');
$universe->start($silentMode);
