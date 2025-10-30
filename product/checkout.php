<?php
include "../login/connect.php";
session_start();
include dirname(__DIR__) . "/navbar.php";
?>
<html>

<head>
    <meta charset="utf-8">
    <link rel="stylesheet" href="checkout.css">
</head>

<body>
    <div class="page-container">

        <div class="checkout-wrapper">

            <form method="post" action="checkout2.php">
                <!--address-->
                <?php
                if (!isset($_SESSION['username'])) {
                    header("Location: ../login/login-form.php");
                }
                ?>
                <h3>เลือกที่อยู่</h3>
                <article>
                    <?php
                    $stmt = $pdo->prepare("SELECT * FROM customer_address where username_cus = ?");
                    $stmt->bindParam(1, $_SESSION['username']);
                    $stmt->execute();
                    while ($row = $stmt->fetch()) :
                    ?>
                        <div class="address">
                            <input type="radio" name="cus_addr" value="<?= $row['address_cus'] ?>" required>
                            <p><?= $row['address_cus'] ?></p>
                        </div>
                    <?php endwhile ?>
                </article>

                <!--phone-->
                <br>
                <h3>เลือกเบอร์โทร</h3>
                <div class="phone-select">
                    <select name="phone" required>
                        <option value="">-- เลือกเบอร์โทร --</option>
                        <?php
                        $stmt = $pdo->prepare("SELECT * FROM customer_tel where username_cus = ? ");
                        $stmt->bindParam(1, $_SESSION['username']);
                        $stmt->execute();
                        while ($row = $stmt->fetch()) :
                        ?>
                            <option value="<?= $row['tel_cus'] ?>"><?= $row['tel_cus'] ?></option>
                        <?php endwhile ?>
                    </select>
                </div>

                <br><br>
                <div class="form-actions">

                    <div class="submit-container">
                        <input type="submit" name="submit" value="ยืนยัน">
                    </div>
                    <div class="back-button">
                        <button>
                            <a href="cart.php">ย้อนกลับ</a>
                        </button>
                    </div>
                </div>
            </form>
        </div>
        <br>

    </div>


</body>

</html>