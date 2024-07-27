<?php
$DataMap = array(
    "c14" =>   array(88227178, 88227167, 88227166, 88227164, 89627130, 89627062, 89627114, 89627112, 89627111, 89624194, 89627126, 89627113),
    "a2" =>    array(88227927, 88227928, 88227165),
    "acar" =>  array(88227163, 89626666),
    "b7" =>    array(88227942, 89623528),
    "b10" =>   array(88227929, 88227943),
    "c8" =>    array(86060206, 86060232, 86062777, 86062778, 86062829, 86060241, 86060202, 86062776, 86060208, 86060236),
    "c7" =>    array(88232072, 88232071, 88232178, 88232176),
    "c12" =>   array(88232173, 88232070, 88232177, 88232179),
    "c13" =>   array(88232174, 88232175, 88232073, 88232172),
    "c8-1" =>  array(88230806, 88230807, 88230461, 88230805, 88230704, 88230810, 88230812, 88230815, 88230816, 88230686),
);

$DataIdToPile = array();
foreach ($DataMap as $pile => $value) {
    foreach ($value as $id) {
        $DataIdToPile[$id] = $pile;
    }
}

$DataTotalId = array();
foreach ($DataMap as $pile => $value) {
    foreach ($value as $id) {
        $DataTotalId[] = $id;
    }
}

$DataTotalPile = array();
foreach ($DataMap as $pile => $value) {
    $DataTotalPile[] = $pile;
}
?>