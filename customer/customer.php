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
(function () {
  window.addEventListener("load", init);
  function init() {
    // dropdowns: address, tel, email
    let dropdowns = document.querySelectorAll("select"); 
    let textarea = document.getElementById("address");
    let tel = document.getElementById("tel_cus"); 
    let email = document.getElementById("email_cus");
    let addrOld = document.getElementById("address_old");
    let telOld = document.getElementById("tel_cus_old");
    let emailOld = document.getElementById("email_cus_old");

    for(let i = 0 ; i < dropdowns.length ; i++){
      if(i==0){
          addEvent(textarea, dropdowns[i], addrOld);
      }
      else if(i==1){
          addEvent(tel, dropdowns[i], telOld);
      }
      else if(i==2){
          addEvent(email, dropdowns[i], emailOld);
      }
    }

    // ฟังก์ชันเพิ่ม event เมื่อเปลี่ยน dropdown
    function addEvent(target, dropdown, hiddenOld){
      dropdown.addEventListener("change", function() {
        target.value = this.value;     // แสดงใน input/textarea
        hiddenOld.value = this.value;  // เก็บค่าเก่าไว้ส่งไป PHP
      });
    }

  const msg = document.createElement("p");
  const emailInput = document.getElementById("email_cus");
  emailInput.insertAdjacentElement("afterend", msg);
  emailInput.addEventListener("blur", checkEmail);

  function checkEmail(){
  if(emailInput.value.trim()==="") return;
  const xhr = new XMLHttpRequest();
  xhr.onreadystatechange = function(){
    msg.textContent = "";
    msg.style.color = "";
    if(xhr.readyState===4 && xhr.status===200){
      if(xhr.responseText.trim()==="used"){
         msg.textContent = "❌ อีเมลนี้มีผู้ใช้แล้ว";
         msg.style.color = "red";
      }
    }
    }
  xhr.open("GET","checkEmail.php?email="+encodeURIComponent(emailInput.value));
  xhr.send();
  }
 
  }
})();
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

  <!-- ขวา: ฟอร์ม -->
  <main class="form-container">
    <form action="edit_customer.php" method="post" enctype="multipart/form-data">
      <h2>แก้ไขข้อมูลสมาชิก</h2>
      <input type="file" name="imgFile" id="imgFile" required><br>
      <input type="hidden" name="username" value="<?= $row["username_cus"] ?>"><br>

      firstname: 
      <input type="text" name="firstname_cus" value="<?= $row["firstname_cus"] ?>"><br>
      lastname: 
      <input type="text" name="lastname_cus" value="<?= $row["lastname_cus"] ?>"><br>

      <!-- ที่อยู่ -->
      <label>ที่อยู่:</label><br>
      <select name="address_select" id="address_select">
        <option value="">-- เลือกที่อยู่--</option>
        <?php while($row4 = $stmt4->fetch()){ ?>
        <option value="<?= $row4['address_cus'] ?>"><?= $row4['address_cus'] ?></option>
        <?php } ?>
      </select>
      <textarea name="address_cus" rows="3" cols="40" id="address" required></textarea>
      <input type="hidden" name="address_old" id="address_old" value="">
      <br>

      <!-- เบอร์โทร -->
      <label>เบอร์โทร:</label><br>
      <select name="tel_cus_select" id="tel_cus_select">
        <option value="">-- เลือกเบอร์โทร--</option>
        <?php while($row2 = $stmt2->fetch()){ ?>
        <option value="<?= $row2['tel_cus'] ?>"><?= $row2['tel_cus'] ?></option>
        <?php } ?>
      </select>
      <input type="text" name="tel_cus" id="tel_cus" value="" pattern="^0[0-9]{9}$" title="โปรดกรอกเฉพาะตัวเลข 10 หลักที่นำหน้าด้วย 0" required>
      <input type="hidden" name="tel_cus_old" id="tel_cus_old" value="">
      <br>

      <!-- อีเมล -->
      <label>อีเมล:</label><br>
      <select name="email_cus_select" id="email_cus_select">
        <option value="">-- เลือกอีเมล--</option>
        <?php while($row3 = $stmt3->fetch()){ ?>
        <option value="<?= $row3['email_cus'] ?>"><?= $row3['email_cus'] ?></option>
        <?php } ?>
      </select>
      <input type="email" name="email_cus" id="email_cus" value="" required>
      <input type="hidden" name="email_cus_old" id="email_cus_old" value="" required>
      <br>

      <input type="submit" name="submit" value="แก้ไขสมาชิก">
    </form>
  </main>
</div>
</body>
</html>
