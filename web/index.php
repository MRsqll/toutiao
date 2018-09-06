<?php
if(isset($_GET['c'])){
    $className = $_GET['c'];
    $method = $_GET['m'];
}else{
    $className = 'news';
    $method = 'index';
}
include '../controller/index/'.$className.'.php';
$page = new $className();
$page->$method();

