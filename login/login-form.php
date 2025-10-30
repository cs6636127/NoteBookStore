<?php include dirname(__DIR__) . "/navbar.php";
?>
<html>

<head>
   <meta charset="utf-8">
   <link rel="stylesheet" href="style.css">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>

<body>
   <main>
      <section class="LoginBox">
         <form action="check-login.php" method="POST">
            <fieldset>
               <legend>เข้าสู่ระบบ</legend>

               <div class="form-group">
                  <label for="username"><strong>Username:</strong></label>
                  <input type="text" id="username" name="username" required>
               </div>

               <div class="form-group">
                  <label for="password"><strong>Password:</strong></label>
                  <input type="password" id="password" name="password" required>
               </div>

               <input type="submit" value="เข้าสู่ระบบ">
            </fieldset>
         </form>

         <footer class="login-footer">
            
            <a href="../register/register.php">สมัครสมาชิก</a>
         </footer>
      </section>
   </main>
</body>

</html>