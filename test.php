<?php

$file=file_get_contents('code.txt');

$table='User';

$replace=[
    '~~Curd_model~~'=>'Mycustome_model',
    '~~title~~'=>'My Custome Title',

];

foreach($replace as $k=>$v){
    $file=str_replace($k,$v,$file);
}


file_put_contents('mycode.php',$file);