<?php
try {
$pdo = new PDO("mysql:host=localhost;dbname=168DB_49;charset=utf8","168DB49","gIHgCeBX");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    //echo "connected";

} catch (PDOException$e){
    echo "error".$e->getMessage();
}

?>