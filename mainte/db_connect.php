<?php

const DB_HOST = 'mysql:dbname=kadai1;charset=utf8;host=localhost';
const DB_USER = "admin";
const DB_PASSWORD = "password";


// 例外処理
try{
    $pdo = new PDO(DB_HOST,DB_USER,DB_PASSWORD,[
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_EMULATE_PREPARES => false,
        ]
    );
    echo 'DB接続成功';
}catch(PDOException $e){
    echo 'DB接続エラー：'.$e->getMessage();
    exit();
}

?>