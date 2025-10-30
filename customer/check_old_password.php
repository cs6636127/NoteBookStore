<?php
include "../login/connect.php";
session_start();

$old = $_GET["oldPassword"] ?? "";

$stmt = $pdo->prepare("SELECT password_cus FROM customer WHERE username_cus = ?");
$stmt->BindParam(1,$_SESSION["username"]);
$stmt->execute();
$row = $stmt->fetch();

if (!$row || $old !== $row["password_cus"]) {
    echo "notok"; 
} else {
    echo "okay"; 
}
?>
