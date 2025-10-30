<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include dirname(__DIR__) . "/navbar.php";
include "../login/connect.php";
if(!isset($_SESSION["username"])){
    header("Location: ../login/login-form.php");
    exit;
}
?>
<html>

<head>
<meta charset="UTF-8">
<link rel="stylesheet" href="customer.css">
<script>
"use strict";
window.onload = function() {
 //ลบ
  const deleteButtons = document.querySelectorAll(".btn-delete");
  deleteButtons.forEach(function(btn) {
    btn.addEventListener("click", function() {
      const url = btn.dataset.url;
      if (confirm("แน่ใจหรือไม่ว่าต้องการลบข้อมูลนี้?")) {
        window.location.href = url;
      }
    });
  });

};
</script>
</head>

<body>
<?php
try{
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  $stmt = $pdo->prepare("SELECT * FROM customer WHERE username_cus = ?");
  $stmt->bindParam(1, $_SESSION["username"]);
  $stmt->execute();
  $row = $stmt->fetch();

  $stmt2 = $pdo->prepare("SELECT * FROM customer_tel WHERE username_cus = ?");
  $stmt2->bindParam(1, $_SESSION["username"]);
  $stmt2->execute();
  
  $stmt3 = $pdo->prepare("SELECT * FROM customer_email WHERE username_cus = ?");
  $stmt3->bindParam(1, $_SESSION["username"]);
  $stmt3->execute();

  $stmt4 = $pdo->prepare("SELECT * FROM customer_address WHERE username_cus = ?");
  $stmt4->bindParam(1, $_SESSION["username"]);
  $stmt4->execute();

}catch(PDOException $e){
  echo "Database error: " . $e->getMessage();
}
?>

<div class="container">
  <!-- ซ้าย: เมนู -->
  <aside class="menu">
      <?php
      $basePath = "member_photo/" . $_SESSION["username"] . ".jpg"; 
      if (file_exists($basePath)) {
          $photoPath = $basePath . "?" . time(); // ถ้ามีรูปจริง และ time ป้องกันcache
      } else {
          $photoPath = "member_photo/default.jpg"; // ถ้าไม่มีใช้รูป default
      }
      ?>
     <img id="preview" src="<?= $photoPath ?>" alt="รูปโปรไฟล์ปัจจุบัน">
      <a href="customer.php">ข้อมูลทั่วไป</a>
      <a href="password.php">เปลี่ยนรหัสผ่าน</a>
      <a href="add_delete.php">จัดการข้อมูลการติดต่อ</a>
  </aside>

  <!-- ขวา:-->
   <main class="form-container form-wrapper">

    <!-- ที่อยู่ -->
    <h2>จัดการข้อมูลการติดต่อ</h2>
    <br>
    <section>
      <h3>ที่อยู่</h3>
      <br>
      <?php while($row4 = $stmt4->fetch()){ ?>
        <div class="address-item">
          <p><?= $row4['address_cus'] ?></p>
          <button type="button" class="btn-delete"
            data-url="delete_address.php?address=<?= urlencode($row4['address_cus']) ?>">ลบ</button>
        </div>
      <?php } ?>
      <form method="post" action="add_address.php">
        <textarea name="address_cus" rows="3" placeholder="เพิ่มที่อยู่ใหม่..." required></textarea>
        <input type="submit" value="เพิ่ม">
      </form>
    </section>


    <!-- เบอร์โทร -->
    <section>
      <h3>เบอร์โทรศัพท์</h3>
      <br>
      <?php while($row2 = $stmt2->fetch()){ ?>
        <div class="tel-item">
          <p><?= $row2['tel_cus'] ?></p>
          <button type="button" class="btn-delete"
            data-url="delete_tel.php?tel=<?= urlencode($row2['tel_cus']) ?>">ลบ</button>
        </div>
      <?php } ?>
      <form method="post" action="add_tel.php">
        <input type="text" name="tel_cus" pattern="^0[0-9]{9}$"
          title="โปรดกรอกเฉพาะตัวเลข 10 หลักที่นำหน้าด้วย 0"
          placeholder="เพิ่มเบอร์ใหม่..." required>
        <input type="submit" value="เพิ่ม">
      </form>
    </section>

    <!-- อีเมล -->
    <section>
      <h3>อีเมล</h3>
      <br>
      <?php while($row3 = $stmt3->fetch()){ ?>
        <div class="email-item">
          <p><?= $row3['email_cus'] ?></p>
          <button type="button" class="btn-delete"
            data-url="delete_email.php?email=<?= urlencode($row3['email_cus']) ?>">ลบ</button>
        </div>
      <?php } ?>
      <form method="post" action="add_email.php">
        <input type="email" name="email_cus" placeholder="เพิ่มอีเมลใหม่..." required>
        <input type="submit" value="เพิ่ม">
      </form>
    </section>

  </main>
</div>
</body>
</html>
