<?php
include "../login/connect.php";
session_start();
include dirname(__DIR__) . "/navbar.php";
?>
<html>

<head>
    <meta charset="utf-8">
    <link rel="stylesheet" href="checkout2.css">
</head>

<body>
    <div id="checkout-summary">

        <?php
        if (!isset($_POST['phone']) || !isset($_POST['cus_addr'])) {
            echo "<p style='color:red;'>ไม่พบข้อมุลที่กรอกหรือกรอกไม่ครบ</p>";
            echo "<a href='checkout.php'>ย้อนกลับ</a>";
            exit;
        }
        $addr =  $_POST['cus_addr'];
        $phone = $_POST['phone'];
        $total = 0;
        if (!isset($_SESSION["cart"]) || count($_SESSION["cart"]) == 0) {
            echo "<p style='color:red;'>ไม่มีสินค้าในตะกร้า</p>";
            exit;
        }
        echo "<table border='1' cellpadding='10'>";
        echo "<h1>สรุปสินค้า</h1>";
        echo "<tr><th>สินค้า</th><th>ราคา</th><th>จำนวน</th><th>ราคารวม</th></tr>";
        foreach ($_SESSION["cart"] as $id => $num_product) {
            $stmt = $pdo->prepare("SELECT * FROM Product WHERE id_product=?");
            $stmt->bindParam(1, $id);
            $stmt->execute();
            $row = $stmt->fetch();
            $sum = $row["price_product"] * $num_product;
            $total += $sum;
            echo "<tr>";
            echo "<td>" . $row["name_product"] . "</td>";
            echo "<td>" . $row["price_product"] . "</td>";
            echo "<td>" . $num_product . "</td>";
            echo "<td>" . $sum . "</td>";
            echo "</tr>";
        }

        echo "<div class='part-divider'>";
        echo "</table>";

        echo "<div class='part'>";
        echo "<div class='part-summary'>";

        echo "<b>ยอดรวม:</b> " . $total . " บาท<br>";
        echo "<b>ยอดสุทธิ:</b> " . $_SESSION["finalPrice"] . " บาท<br>";
        echo "</div>";

        echo "<div class='part-address'>";
        echo "<h3>ที่อยู่ในการจัดส่ง</h3>";
        echo "<h3>ที่อยู่:</h3><p>" . $addr . "</p>";
        echo "<h3>เบอร์โทร:</h3><p>" . $phone . "</p>";
        echo "</div>";

        echo "</div>";
        echo "</div>";
        ?>
        <div class="part-payment">
            <h3>ชำระเงิน</h3>
            <form method="post" action="confirm.php" enctype="multipart/form-data">
                <!--
        หลังกดยืนยัน
        ส่งไป order_detail (แยกของแต่ละชิ้น)
        ส่งไปorder(db) (รวมของ1ใบเสร็จ)
        ส่งไปpayment(db) สถานะdefault 'pending' --
        ส่งไป shipping(db) สถานะdefault 'pending' (ใส่ก่อนเปลี่ยนแล้วค่อยแจ้ง)
        ส่งไปnotification(db) textรายละเอียดคือ รายละเอียดorder+ที่อยู่+เบอร์+ราคา+สถานะการตรวจสอบชำระ
         -->
                <h4>แนบหลักฐานการโอนเงิน(สลิป)</h4>
                <img src="img/QR.jpg" alt="QR ตัวอย่าง">
                <br><br>
                <input type="file" name="imgFile" id="imgFile" required><br>
                <input type="hidden" name="total" value="<?= $total ?>">
                <input type="hidden" name="cus_addr" value="<?= $addr ?>">
                <input type="hidden" name="phone" value="<?= $phone ?>">
                <br><br>
                <div class="btn-container">
                    <a href="cart.php" class="btn-secondary">ย้อนกลับ</a>
                    <input type="submit" name="submit" value="ยืนยันการชำระเงิน">
                </div>
            </form>

        </div>
    </div>

</body>

</html>