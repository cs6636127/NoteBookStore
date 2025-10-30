<?php
include "connect.php";

session_start();
include dirname(__DIR__) . "/navbar.php";
$username = $_POST["username"];
$password = $_POST["password"];

$stmt = $pdo->prepare("SELECT * FROM customer WHERE username_cus = ? AND password_cus = ?");
$stmt->bindParam(1, $username);
$stmt->bindParam(2, $password);
$stmt->execute();
$row = $stmt->fetch();

// หาก customer username และ password ตรงกัน จะมีข้อมูลในตัวแปร $row
if (!empty($row)) {

  $_SESSION["username"] = $row["username_cus"];
  $_SESSION["role"] = "customer";
  header("Location: ../product/product.php");
  exit;
}
//หากไม่เจอ จะไปค้น admin ต่อ
$stmt = $pdo->prepare("SELECT * FROM Admin WHERE username_ad = ? AND password_ad = ?");
$stmt->bindParam(1, $username);
$stmt->bindParam(2, $password);
$stmt->execute();
$row = $stmt->fetch();
if (!empty($row)) {

  $_SESSION["username"] = $row["username_ad"];
  $_SESSION["role"] = "admin";
  header("Location: ../admin/admin.php");
  exit;
}
//ถ้าไม่เจอทั้งสอง
echo "ไม่สำเร็จ ชื่อหรือรหัสผ่านไม่ถูกต้อง ";
echo "<a href='login-form.php' style='color:red'>คลิ๊กเพื่อเข้าสู่ระบบอีกครัง</a>";
