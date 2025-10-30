<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
include "../login/connect.php";

// ตรวจสอบสิทธิ์ว่าเป็น admin
if (!isset($_SESSION["role"]) || $_SESSION["role"] !== "admin") {
    echo "<script>
        alert('🚫 คุณไม่มีสิทธิ์ในการเข้าถึงหน้านี้');
        window.location.href='../login/login-form.php';
    </script>";
    exit;
}

// รับค่า username ที่ต้องการลบ
$username = trim($_GET["username"] ?? "");

if (empty($username)) {
    echo "<script>
        alert('⚠️ ไม่พบชื่อผู้ใช้ที่ต้องการลบ');
        window.location.href='admin-customer.php';
    </script>";
    exit;
}

try {
    // เริ่ม transaction
    $pdo->beginTransaction();

    // ลบข้อมูลจาก customer_address
    $stmt1 = $pdo->prepare("DELETE FROM customer_address WHERE username_cus = :username");
    $stmt1->execute([':username' => $username]);

    // ลบข้อมูลจาก customer_email
    $stmt2 = $pdo->prepare("DELETE FROM customer_email WHERE username_cus = :username");
    $stmt2->execute([':username' => $username]);

    // ลบข้อมูลจาก customer_tel
    $stmt3 = $pdo->prepare("DELETE FROM customer_tel WHERE username_cus = :username");
    $stmt3->execute([':username' => $username]);

    // ลบข้อมูลจาก customer
    $stmt4 = $pdo->prepare("DELETE FROM customer WHERE username_cus = :username");
    $stmt4->execute([':username' => $username]);

    // commit transaction
    $pdo->commit();

    echo "<script>
        alert('✅ ลบข้อมูลลูกค้าทั้งหมดเรียบร้อยแล้ว');
        window.location.href='admin-customer.php';
    </script>";
} catch (PDOException $e) {
    // rollback ถ้าเกิด error
    $pdo->rollBack();

    echo "<script>
        alert('❌ เกิดข้อผิดพลาดในการลบข้อมูล: " . addslashes($e->getMessage()) . "');
        window.location.href='admin-customer.php';
    </script>";
}
