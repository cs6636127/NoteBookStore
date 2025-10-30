<?php
include "../login/connect.php";
include dirname(__DIR__) . "/navbar.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username_cus"]);
    $password = trim($_POST["password_cus"]);
    $firstname = trim($_POST["firstname_cus"]);
    $lastname = trim($_POST["lastname_cus"]);
    $tel = trim($_POST["tel_cus"]);
    $email = trim($_POST["email_cus"]);
    $address = trim($_POST["address_cus"]);

    if ($username == "" || $password == "" || $firstname == "" || $lastname == "") {
        echo "<script>alert('กรุณากรอกข้อมูลให้ครบทุกช่อง');</script>";
    } else {
        try {
            $check = $pdo->prepare("SELECT * FROM customer WHERE username_cus = ?");
            $check->execute([$username]);
            if ($check->rowCount() > 0) {
                echo "<script>alert('Username นี้มีผู้ใช้แล้ว!');</script>";
            } else {
                $stmt = $pdo->prepare("INSERT INTO customer (username_cus, password_cus, firstname_cus, lastname_cus)
                                       VALUES (?, ?, ?, ?)");
                $stmt->execute([$username, $password, $firstname, $lastname]);

                if (!empty($tel)) {
                    $pdo->prepare("INSERT INTO customer_tel (username_cus, tel_cus) VALUES (?, ?)")
                        ->execute([$username, $tel]);
                }
                if (!empty($email)) {
                    $pdo->prepare("INSERT INTO customer_email (username_cus, email_cus) VALUES (?, ?)")
                        ->execute([$username, $email]);
                }
                if (!empty($address)) {
                    $pdo->prepare("INSERT INTO customer_address (username_cus, address_cus) VALUES (?, ?)")
                        ->execute([$username, $address]);
                }

                echo "<script>alert('สมัครสมาชิกสำเร็จ!');window.location.href='../login/login-form.php';</script>";
            }
        } catch (PDOException $e) {
            echo "Database error: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<title>Register | NoteBookStore</title>
<link rel="stylesheet" href="register.css">
<script>
"use strict";
(function(){
window.addEventListener("load", init);
function init(){

const userInput = document.getElementById("username");
const userMsg = document.getElementById("usernameMsg");
userInput.addEventListener("blur", checkUsername);

function checkUsername(){
  if(userInput.value.trim()==="") return;
  const xhr = new XMLHttpRequest();
  xhr.onreadystatechange = function(){
    if(xhr.readyState===4 && xhr.status===200){
      if(xhr.responseText.trim()==="used"){
        setError(userInput, userMsg, "❌ Username นี้มีผู้ใช้แล้ว");
      } else {
        validateInput(userInput, userMsg, "❌ ต้องขึ้นต้นด้วยตัวอักษรและมีความยาว4–15 ตัว (a-z, A-Z, 0-9, _)");
      }
    }
  };
  xhr.open("GET","checkUsername.php?username="+encodeURIComponent(userInput.value));
  xhr.send();
}
//มีAjax2ที่
const emailInput = document.getElementById("email");
const emailMsg = document.getElementById("emailMsg");
emailInput.addEventListener("blur", checkEmail);

function checkEmail(){
  if(emailInput.value.trim()==="") return;
  const xhr = new XMLHttpRequest();
  xhr.onreadystatechange = function(){
    if(xhr.readyState===4 && xhr.status===200){
      if(xhr.responseText.trim()==="used"){
        setError(emailInput, emailMsg, "❌ อีเมลนี้มีผู้ใช้แล้ว");
      }
      else if(!emailInput.checkValidity()){
          setError(emailInput, emailMsg, "❌ รูปแบบอีเมลไม่ถูกต้อง โปรดใส่@และมีอักษรต่อท้าย เช่น example@mail.com");
      } 
      else {
          setSuccess(emailInput, emailMsg, "✅ ถูกต้อง");
      } 
    }
    }
  xhr.open("GET","checkEmail.php?email="+encodeURIComponent(emailInput.value));
  xhr.send();
}


const allInputs = document.querySelectorAll("#register-form input, #register-form textarea");
allInputs.forEach(inp => {
  inp.addEventListener("input", function(){
    const msg = document.getElementById(inp.id + "Msg");
    if(!msg) return;
    let customText = "";
    if(inp.id === "tel") customText = "❌ โปรดกรอกเฉพาะตัวเลข 10 หลักที่นำหน้าด้วย0";
    else if(inp.id === "password") customText = "❌ ต้องมีพิมพ์เล็ก พิมพ์ใหญ่ ตัวเลข อย่างน้อย 8 ตัว และห้ามเว้นวรรค";
    validateInput(inp, msg, customText);
  });
});

// ฟังก์ชันตรวจ
function validateInput(input, msg, errorText){
  const val = input.value.trim();
  const patternText = input.getAttribute("pattern");
  if(val === ""){
    input.style.border = "";
    msg.textContent = "";
    return;
  }
  if(patternText){
    const pattern = new RegExp(patternText);
    if(pattern.test(val)){
      setSuccess(input, msg, "✅ ถูกต้อง");
    } else {
      setError(input, msg, errorText);
    }
  } else {
    setSuccess(input, msg, "✅ ถูกต้อง");
  }
}


//dom style 2 จุด
function setError(input, msg, text){
  msg.textContent = text;
  msg.style.color = "red";
  input.style.border = "2px solid red";
}
function setSuccess(input, msg, text){
  msg.textContent = text;
  msg.style.color = "green";
  input.style.border = "2px solid green";
}

// ตรวจสอบก่อน submit
const form = document.getElementById("register-form");
form.addEventListener("submit", function(event){
  const msgs = document.querySelectorAll("#register-form p");
  for (let m of msgs){
    if(m.textContent.includes("❌")){
      alert("❌ โปรดตรวจสอบข้อมูลให้ถูกต้องก่อนส่ง");
      event.preventDefault();
      return;
    }
  }
});
}
})();
</script>
</head>

<body>
<div class="container">
<h2 id="register-title">สมัครสมาชิก</h2>

<form id="register-form" action="register.php" method="post">
  <label>ชื่อผู้ใช้ (Username)</label><br>
  <input type="text" id="username" name="username_cus" 
         pattern="^[A-Za-z][A-Za-z0-9_]{3,15}$"
         required>
  <p id="usernameMsg"></p>

  <label>รหัสผ่าน (Password)</label><br>
  <input type="password" id="password" name="password_cus"
         pattern="(?!.*\s)(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}"
         required>
  <p id="passwordMsg"></p>

  <label>ชื่อจริง (Firstname)</label><br>
  <input type="text" id="firstname" name="firstname_cus" required>
  <p id="firstnameMsg"></p>

  <label>นามสกุล (Lastname)</label><br>
  <input type="text" id="lastname" name="lastname_cus" required>
  <p id="lastnameMsg"></p>

  <label>เบอร์โทร (Tel)</label><br>
  <input type="text" id="tel" name="tel_cus" 
         pattern="^0[0-9]{9}$" required>
  <p id="telMsg"></p>

  <label>อีเมล (Email)</label><br>
  <input type="email" id="email" name="email_cus" required>
  <p id="emailMsg"></p>

  <label>ที่อยู่ (Address)</label><br>
  <textarea id="address" name="address_cus" rows="3" required></textarea>
  <p id="addressMsg"></p>

  <input type="submit" name="Submit" value="สมัครสมาชิก">
</form>
</div>
</body>
</html>

