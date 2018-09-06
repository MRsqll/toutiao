<?php
if(isset($_GET['c'])){
    $className = $_GET['c'];
    $method = $_GET['m'];
}else{
    $className = 'admin';
    $method = 'index';
}
include '../controller/admin/'.$className.'.php';
$adm = new $className();
$adm->$method();

