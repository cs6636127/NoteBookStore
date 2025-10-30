<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
include "../login/connect.php";

if(!isset($_SESSION["username"])) {
    header("Location: ../login/login-form.php");
    exit;
}

$username = $_SESSION["username"];
$address = $_GET["address"] ?? "";


try {
    $stmt = $pdo->prepare("DELETE FROM customer_address WHERE username_cus = ? AND address_cus = ?");
    $stmt->execute([$username, $address]);

    if ($stmt->rowCount() > 0) {
        echo "<script>alert('✅ ลบที่อยู่เรียบร้อยแล้ว'); window.location.href='add_delete.php';</script>";
    } else {
        echo "<script>alert('⚠️ ไม่พบข้อมูลที่อยู่ในระบบ'); window.location.href='add_delete.php';</script>";
    }

} catch (PDOException $e) {
    echo "<script>alert('เกิดข้อผิดพลาด: {$e->getMessage()}'); window.location.href='add_delete.php';</script>";
}
?>
