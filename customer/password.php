<?php
//✅DOM rubrics ถึง bigBonus
include dirname(__DIR__) . "/navbar.php";
include "../login/connect.php";
?>
<html>

<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="customer.css">
    <script>
    "use strict";
    (function () {
     window.addEventListener("load", init); //part2 onload
     function init() {
     const inputOld = document.getElementById("oldPassword");
     inputOld.addEventListener("blur", checkOldPassword);
	 var xmlHttp;
    
     function checkOldPassword() {
        const input = document.getElementById("oldPassword");
        const oldMsg = document.getElementById("oldPwdMsg");
        if (oldMsg) oldMsg.remove(); // เคลียร์ข้อความเก่า
        xmlHttp = new XMLHttpRequest();
        xmlHttp.onreadystatechange = showOldPasswordStatus;

        const oldPwd = input.value;
        const url = "check_old_password.php?oldPassword=" + encodeURIComponent(oldPwd);
        xmlHttp.open("GET", url);
        xmlHttp.send();
     }

     function showOldPasswordStatus() {
        const input = document.getElementById("oldPassword");
        let msg = document.getElementById("oldPwdMsg");
         if (!msg) {
            msg = document.createElement("p"); //เพิ่ม Elements โดย getElementById() ผ่าน window load event
            msg.id = "oldPwdMsg";
            input.insertAdjacentElement("afterend", msg);
        }
    
        if (xmlHttp.readyState == 4 && xmlHttp.status == 200) {
            if (xmlHttp.responseText.trim() != "okay") {
                msg.textContent = "❌ รหัสผ่านเดิมไม่ถูกต้อง";
                msg.style.color = "red"; //เปลี่ยน Style ผ่าน getElementById()
            }
            else{
                msg.textContent = "✅ รหัสผ่านถูกต้อง";
                msg.style.color = "green";
            }
        }
     }

     const password = document.getElementById("password");
     const confirmPassword = document.getElementById("confirmPassword");


    password.addEventListener("blur", validatePasswordFields);
    confirmPassword.addEventListener("blur", validatePasswordFields);

function validatePasswordFields() {
    const passVal = password.value;
    const confirmVal = confirmPassword.value;

    // ลบข้อความเก่าก่อน
    document.querySelectorAll(".err-msg").forEach(e => e.remove());
    document.querySelectorAll(".scc-msg").forEach(e => e.remove());
    document.querySelectorAll(".scc2-msg").forEach(e => e.remove());
    // ถ้าไม่ได้พิมพ์อะไรเลยก็ไม่ต้องแสดง error
    if (!passVal && !confirmVal) return;
   
    const msg1 = document.createElement("p");
    msg1.className = "err-msg";
    const msg2 = document.createElement("p");
    msg2.className = "scc-msg";
    const msg3 = document.createElement("p");
    msg3.className = "scc2-msg";

    if (!checkL(passVal)) {
        msg1.textContent = "❌ รหัสผ่านต้องมีอย่างน้อย 8 ตัว";
        msg1.style.color = "red";
        password.insertAdjacentElement("afterend", msg1);
        return;
    }

   
    if (!checkPattern(passVal)) {
        msg1.textContent = "❌ รหัสผ่านต้องประกอบด้วยตัวพิมพ์เล็ก พิมพ์ใหญ่ ตัวเลข และห้ามมีช่องว่าง";
        msg1.style.color = "red";
        password.insertAdjacentElement("afterend", msg1);
        return;
    }

    msg2.textContent = "✅ รหัสผ่านถูกต้อง";
    msg2.style.color = "green";
    password.insertAdjacentElement("afterend", msg2);
    if (!confirmVal) return;

    if (!checkPwd(passVal, confirmVal)) {
        msg2.textContent = "❌ รหัสผ่านไม่ตรงกัน";
        msg2.style.color = "red";
        confirmPassword.insertAdjacentElement("afterend", msg2);
        return;
    }

    msg3.textContent = "✅ ยืนยันรหัสผ่านถูกต้อง";
    msg3.style.color = "green";
    confirmPassword.insertAdjacentElement("afterend", msg3);

    }
const form = document.getElementById("pwdForm");
form.addEventListener("submit", validateForm);
function validateForm(event) {
    event.preventDefault();
    validatePasswordFields(); // เรียกตรวจซ้ำอีกครั้งตอน submit 

    // ถ้ายังมี error อยู่ ให้หยุดส่งฟอร์ม
     if(document.querySelector(".err-msg")||document.querySelector("#oldPwdMsg")?.textContent.includes("ไม่ถูกต้อง")){
            alert("❌โปรดกรอกรหัสผ่านให้ถูกต้อง");
            return false;
     }
      event.target.submit();    
}

	function checkPwd(password,confirmPassword){
        if(password !== confirmPassword){
			return false 
		}
		return true
	}
	function checkL(password){
        if (password.length < 8){
			return false
		}
		return true
	} 
    function checkPattern(str)
    { 		
        var pattern = new RegExp("(?!.*\\s)(?=.*[a-z])(?=.*[A-Z])(?=.*\\d).{8,}");
        var match_test = pattern.test(str);
        return match_test;
    }
	
    }
    })();
	    
	</script>
</head>

<body>

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
            <form action="edit_password.php" id="pwdForm" method="post">
                <h2>เปลี่ยนรหัสผ่าน</h2>
                รหัสผ่านเดิม * <input type="password" name="oldPassword" id="oldPassword" value="" required><br> <!--มีrequired built-in-->
                รหัสผ่านใหม่ * <input type="password" name="password" id="password" value="" required><br>
                ยืนยันรหัสผ่านอีกครั้ง * <input type="password" name="confirmPassword" id="confirmPassword" value="" required><br>
                <input type="submit" name="Submit" value="แก้ไขสมาชิก">
            </form>
        </main>
    </div>
</body>
</html>