<?php
// Database params
const DB_HOST_NAME = '127.0.0.1';
const DB_USERNAME = 'root';
const DB_PASSWORD = '';
const DB_NAME = '440_blog';
    
define('ROOT', realpath(dirname(__FILE__)));
define('LAYOUTS_PATH', ROOT.DIRECTORY_SEPARATOR.'layouts'.DIRECTORY_SEPARATOR);

// global const
define('APP_NAME', 'Comp 440 Blog');



// Database connect
try{
    global $db;
    $db = new \PDO('mysql:host='.DB_HOST_NAME.';dbname='.DB_NAME, DB_USERNAME, DB_PASSWORD, [
        \PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'
    ]);
    $db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
}catch(\PDOExeption $e){
    die('Failed to connect : '.$e->getMessage());
}



function ddd($element){
    echo '<pre>';
    print_r(var_dump($element));
    echo '</pre>';
    die();
}
