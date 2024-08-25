<?php
$list = get_declared_classes();
foreach ($list as $v){
    if(strpos($v, 'MongoDB') !== false){
        echo $v . '<br>';
    }
}