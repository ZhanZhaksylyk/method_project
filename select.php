<?php

include 'db.php';

$stmt = $pdo->prepare("
SELECT
    `Restaurants`.*,
    `Cuizines`.`name` AS `cuisine-name`
FROM
    `Restaurants`
LEFT JOIN
    `RC`
    ON `RC`.`id_r` = `Restaurants`.`id`    
LEFT JOIN
    `Cuizines`
    on `Cuizines`.`id` = `RC`.`id_r`    
    WHERE
        `price_max` <= :max
    AND
        `price_min` >= :min
    AND `Cuizines`.`id` = :id_c
");

$stmt->execute([

    ':max' => 6000,
    ':min' => 2000,
    ':id_c' => 3,
]);

$result = $stmt->fetchAll();

print_r($result);