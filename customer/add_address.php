<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include dirname(__DIR__) . "/navbar.php";
include "../login/connect.php";

if (!isset($_SESSION["username"])) {
    header("Location: ../login/login-form.php");
    exit;
}

$username = $_SESSION["username"];
$address = trim($_POST["address_cus"]);

// ✅ ตรวจว่ากรอกหรือยัง
if ($address === "") {
    echo "<script>alert('❌ กรุณากรอกที่อยู่ก่อน');history.back();</script>";
    exit;
}

try {
    $stmt = $pdo->prepare("INSERT INTO customer_address (username_cus, address_cus) VALUES (?, ?)");
    $stmt->execute([$username, $address]);

    echo "<script>
            alert('✅ เพิ่มที่อยู่เรียบร้อยแล้ว');
            window.location.href = 'add_delete.php';
          </script>";
    exit;
}
catch (PDOException $e) {
    // ✅ ดักกรณีข้อมูลซ้ำ (code 23000)
    if ($e->getCode() == 23000) {
        echo "<script>alert('⚠️ ที่อยู่นี้มีอยู่ในระบบแล้ว');window.location.href = 'add_delete.php';</script>";
    } 
    else{
        echo "<script>alert('เกิดข้อผิดพลาด: {$e->getMessage()}'); window.location.href = 'add_delete.php';</script>";
        exit;
    }
    exit;
}
?>
