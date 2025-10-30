<?php
include "../login/connect.php";
session_start();;

$stmt = $pdo->prepare("SELECT username_cus FROM customer_email WHERE email_cus = ?");
$stmt->bindParam(1,$_GET["email"]);
$stmt->execute();
$row = $stmt->fetch();

// ถ้ามีอีเมลนี้ในระบบ และไม่ใช่ของเรา (เชคซ้ำ)
if ($row && $row["username_cus"] !== $_SESSION["username"]) {
    echo "used";
} else {
    echo "ok";
}
?>
