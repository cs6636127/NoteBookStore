<?php include "../login/connect.php" ?>
<?php 
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
try{
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$stmt = $pdo->prepare("UPDATE customer SET firstname_cus=?, lastname_cus=? WHERE username_cus=?"); 
$stmt->bindParam(1, $_POST["firstname_cus"]);
$stmt->bindParam(2, $_POST["lastname_cus"]);
$stmt->bindParam(3,$_POST["username"]);
$stmt->execute();

$stmt2 = $pdo->prepare("UPDATE customer_tel SET tel_cus=? WHERE username_cus=? AND tel_cus=?"); 
$stmt2->bindParam(1, $_POST["tel_cus"]);
$stmt2->bindParam(2, $_POST["username"]);
$stmt2->bindParam(3, $_POST["tel_cus_old"]);
$stmt2->execute();

$stmt3 = $pdo->prepare("UPDATE customer_address SET address_cus=? WHERE username_cus=? AND address_cus=?"); 
$stmt3->bindParam(1, $_POST["address_cus"]);
$stmt3->bindParam(2,$_POST["username"]);
$stmt3->bindParam(3, $_POST["address_old"]);
$stmt3->execute();

$stmt4 = $pdo->prepare("UPDATE customer_email SET email_cus=? WHERE username_cus=? AND email_cus=?"); 
$stmt4->bindParam(1, $_POST["email_cus"]);
$stmt4->bindParam(2,$_POST["username"]);
$stmt4->bindParam(3, $_POST["email_cus_old"]);
$stmt4->execute();
}catch(PDOException $e){
     echo "Database error: " . $e->getMessage();
}

$username = $_POST["username"];
//รูป
if(isset($_FILES["imgFile"]) && $_FILES["imgFile"]["error"] == 0 && !empty($_FILES["imgFile"]["tmp_name"])){
$t_dir = "member_photo/";
$upOk = 1;
$imgFileType = strtolower(pathinfo($_FILES["imgFile"]["name"],PATHINFO_EXTENSION));
$t_file = $t_dir.$username.".".$imgFileType ;

//เชคว่ามีไฟล์จริงไหม
if(isset($_POST["submit"])){
    $check = getimagesize($_FILES["imgFile"]["tmp_name"]);
    if($check !== false){
        $upOk = 1 ;
    }else{
        $upOk = 0;
         echo "ไม่ใช่ไฟล์รูปภาพ<br>";
    }
}

//เชคขนาดไฟล์
if($_FILES["imgFile"]["size"]>500000){
    echo "ขนาดไฟล์รูปภาพของคุณใหญ่เกินไป<br>";
    $upOk = 0;
}
//เชคประเภทไฟล์ jpg 
if($imgFileType != "jpg" ){
    echo "รูปไฟล์รูปภาพ นามสกุล .jpg เท่านั้น<br>";
    $upOk = 0;
}
if($upOk == 0){
    echo "ส่งรูปภาพไม่สำเร็จ<br>";
}
else{
    //ส่งรูปภาพเข้าโฟลเดอร์
    if(move_uploaded_file($_FILES["imgFile"]["tmp_name"],$t_file)){
        //popup 
        echo "<script>
                alert('อัปเดตข้อมูลสำเร็จ!✅');
                window.location.href='customer.php'; 
            </script>";
        exit;
    }
    else{
        echo "เกิดปัญหาขัดข้องระหว่างอัพโหลดรูปภาพ<br>";
    }
}
}

?>