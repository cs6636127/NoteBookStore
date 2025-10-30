<?php
include "../login/connect.php";
$email = $_GET["email"] ?? "";
$stmt = $pdo->prepare("SELECT * FROM customer_email WHERE email_cus = ?");
$stmt->execute([$email]);
echo $stmt->rowCount() > 0 ? "used" : "ok";
?>
