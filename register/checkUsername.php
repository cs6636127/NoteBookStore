<?php
include "../login/connect.php";
$username = $_GET["username"] ?? "";
$stmt = $pdo->prepare("SELECT * FROM customer WHERE username_cus = ?");
$stmt->execute([$username]);
echo $stmt->rowCount() > 0 ? "used" : "ok";
?>
