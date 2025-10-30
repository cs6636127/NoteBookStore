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
$email = trim($_POST["email_cus"]);

//ตรวจรูปแบบอีเมล
//if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
   // echo "<script>alert('❌ รูปแบบอีเมลไม่ถูกต้อง');</script>";
    //exit;
//}

try {
    $stmt = $pdo->prepare("SELECT username_cus FROM customer_email WHERE email_cus = ?");
    $stmt->bindParam(1, $email);
    $stmt->execute();
    $row = $stmt->fetch();

    if ($row && $row["username_cus"] !== $username) {
        echo "<script>alert('⚠️ อีเมลนี้ถูกใช้โดยผู้ใช้อื่นแล้ว'); history.back();</script>";
        exit;
    }

    $stmt = $pdo->prepare("INSERT INTO customer_email (username_cus, email_cus) VALUES (?, ?)");
    $stmt->execute([$username, $email]);

    echo "<script>
            alert('✅ เพิ่มอีเมลใหม่เรียบร้อยแล้ว');
            window.location.href = 'add_delete.php';
          </script>";
    exit;

} catch (PDOException $e) {
    // ดัก error duplicatekey(23000)
    if ($e->getCode() == 23000) {
        echo "<script>alert('⚠️ อีเมลนี้มีอยู่ในระบบแล้ว');window.location.href = 'add_delete.php';</script>";
    } 
    else{
        echo "<script>alert('เกิดข้อผิดพลาด: {$e->getMessage()}');window.location.href = 'add_delete.php';</script>";

    }
    exit;
}
?>
