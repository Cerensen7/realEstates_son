<?php

include 'functions.php';

error_reporting(0);
$pdo = new PDO("mysql:host=localhost;dbname=rba", 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

if (!$_SESSION['user_id']){
    exit();
}