<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
include "../login/connect.php";

if (!isset($_SESSION["username"])) {
    header("Location: ../login/login-form.php");
    exit;
}

$username = $_SESSION["username"];
$email = $_GET["email"] ?? "";

try {
    $stmt = $pdo->prepare("DELETE FROM customer_email WHERE username_cus = ? AND email_cus = ?");
    $stmt->execute([$username, $email]);

    if ($stmt->rowCount() > 0) {
        echo "<script>alert('✅ ลบอีเมลเรียบร้อยแล้ว'); window.location.href='add_delete.php';</script>";
    } else {
        echo "<script>alert('⚠️ ไม่พบอีเมลในระบบ'); window.location.href='add_delete.php';</script>";
    }

} catch (PDOException $e) {
    echo "<script>alert('เกิดข้อผิดพลาด: {$e->getMessage()}'); window.location.href='add_delete.php';</script>";
}
?>
