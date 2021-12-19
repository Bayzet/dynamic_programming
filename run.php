<?php

declare(strict_types=1);

$memoryStart = memory_get_usage();

function getmicrotime()
{
    list($usec, $sec) = explode(" ",microtime());
    return ((float)$usec + (float)$sec);
}

$time = getmicrotime();

require __DIR__ . '/source/Backpack.php';
require __DIR__ . '/source/Product.php';
require __DIR__ . '/source/DynamicProgramming.php';

use App\DynamicProgramming;

$option = [
    'dataset' => $argv[1] ?? die("Передайте вторым параметром датасет из папки 'dataset' в формате '%name%.json'" . PHP_EOL),
    'full_size' => $argv[2] ?? die("Передайте третьим параметром размер контейнера, например 100" . PHP_EOL),
    'min_object_size' => $argv[3] ?? die("Передайте четвёртым параметром минимальный размер контейнера, например 1" . PHP_EOL),
];

$datasetFile = @file_get_contents(__DIR__ . "/dataset/". $option['dataset']);
if (!$datasetFile) {
    die("Датасет '" . __DIR__ . "/dataset/" . $option['dataset'] . "' не найден" . PHP_EOL);
}

$dataset = json_decode($datasetFile, true);

$dynamicProgramming = new DynamicProgramming((int) $option['full_size'], (int) $option['min_object_size'], $dataset, $option['dataset']);
$bestBackpack = $dynamicProgramming->process();
$forSave = $bestBackpack;

$memoryEnd = memory_get_usage();

echo PHP_EOL."Лучший набор портфеля с размером " . $dynamicProgramming->getFullSize() . PHP_EOL;

$fullBackpack = array_pop($bestBackpack);
foreach ($fullBackpack->getProducts() as $product) {
    echo "Название: " . $product->getName() . " Размер: " . $product->getSize() . " Ценность: " . $product->getValue() . " у.е." . PHP_EOL;
}
echo "Итого: " . $fullBackpack->getFullValue() . " у.е." . PHP_EOL . PHP_EOL;

echo "Затрачено: " . (getmicrotime() - $time) . " секунд" . PHP_EOL;
echo "Выделено памяти: " . (($memoryEnd - $memoryStart) / 1024) . " Килобайт" . PHP_EOL;
echo "Выбрано среди товаров в количестве: " . count($dataset) . " шт. " . PHP_EOL;

$dynamicProgramming->saveResult($forSave);
foreach ($dataset as &$data) {
    $data['processed'] = true;
}

file_put_contents(__DIR__ . "/dataset/". $option['dataset'], json_encode($dataset));


