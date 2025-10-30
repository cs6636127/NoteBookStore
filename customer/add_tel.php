<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include dirname(__DIR__) . "/navbar.php";
include "../login/connect.php";

if(!isset($_SESSION["username"])) {
    header("Location: ../login/login-form.php");
    exit;
}
    $username = $_SESSION["username"];
    $tel = trim($_POST["tel_cus"]);
    try{
    $stmt = $pdo->prepare("INSERT INTO customer_tel (username_cus, tel_cus) VALUES (?, ?)");
    $stmt->execute([$username, $tel]);
    echo "<script>
            alert('✅ เพิ่มเบอร์โทรศัพท์เรียบร้อยแล้ว');
            window.location.href = 'add_delete.php';
          </script>";
          exit;
    }
    catch (PDOException $e) {
    if ($e->getCode() == 23000) {
        echo "<script>alert('⚠️ เบอร์นี้มีอยู่ในระบบแล้ว');window.location.href = 'add_delete.php';</script>";
        exit;
    }
    else{
        echo "<script>alert('เกิดข้อผิดพลาด: {$e->getMessage()}');window.location.href = 'add_delete.php';</script>";
        exit;

    }

   }

?>