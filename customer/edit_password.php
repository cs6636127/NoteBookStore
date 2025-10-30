<?php 
include "../login/connect.php";
session_start(); 
$stmt = $pdo->prepare("UPDATE customer SET password_cus=? WHERE username_cus=?"); 
$stmt->bindParam(1, $_POST["password"]);
$stmt->bindParam(2, $_SESSION["username"]);
$stmt->execute();
echo "<script>
    alert('เปลี่ยนรหัสผ่าน สำเร็จ✅');
    window.location.href = 'password.php';
</script>";
exit;
?>