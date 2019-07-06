<?php

include 'functions.php';
include 'db.php';
# очистка таблицы
$stmt = $pdo->prepare("TRUNCATE TABLE `Restaurants`");
$stmt ->execute();
$stmt = $pdo->prepare("TRUNCATE TABLE `Cuizines`");
$stmt ->execute();
$stmt = $pdo->prepare("TRUNCATE TABLE `RC`");
$stmt ->execute();

$rests = [];
#$pages=getMaxPage(1);
for ($i=1, $pages=getMaxPage(1); $i<=$pages; $i++) {
    $rests = array_merge($rests, getRestsFromPage($i));
}


$cuisines = [];
foreach($rests as $rest) {
    $cuisines = array_merge($cuisines, $rest['cuisine']);
}
$cuisines = array_unique($cuisines);
print_r($cuisines);
$stmt = $pdo->prepare("
    INSERT INTO
        `Cuizines` (
            `name`
        ) VALUES (
            :name
        )    ");
        $cuisinesMap = [];
foreach ($cuisines as $cuisine) {
    $stmt->execute([
        ':name' => $cuisine
    ]);
    $cuisinesMap[$cuisine] = $pdo->lastInsertId();
}
print_r($cuisinesMap);
# Подготовка
$stmt = $pdo->prepare("
    INSERT INTO
        `Restaurants` (
            `name`,
            `link`,
            `price_min`,
            `price_max`,
            `worktime`,
            `address`
        ) VALUES (
            :name,
            :link,
            :pmin,
            :pmax,
            :worktime,
            :address
        )
");

#to RC
$stmtRC = $pdo->prepare("
    INSERT INTO
        `RC`(
            `id_r`,
            `id_c`
        ) VALUES (
            :id_rest,
            :id_cuisine
        )         
");


print_r($rests);
foreach($rests as $rest){
    # Выполнение
$stmt->execute([
    ':name' => $rest['name'],
    ':link' => $rest['link'],
    ':pmin' => $rest['price']['min'],
    ':pmax' => $rest['price']['max'],
    ':worktime' =>$rest['worktime'],
    ':address' => $rest['address'],
    ]);

    $idRestaurant = $pdo->lastInsertId();
    foreach ($rest['cuisine'] as $cuisine) {
        $stmtRC->execute([
            ':id_rest'=>$idRestaurant,
            ':id_cuisine'=>$cuisinesMap[$cuisine]
        ]);
    }
}
