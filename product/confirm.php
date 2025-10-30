<?php 
 include "../login/connect.php";
 session_start();
 include dirname(__DIR__) . "/navbar.php";
?>
<html>
    <head>
    <meta charset="utf-8">
    <style>
    *{
        box-sizing:border-box;
        margin: 0;
        padding: 0;
    }
    body {
    font-size: 14px;
    margin: 0px;
    }  
    
    /* แท็บเล็ต / เดสก์ท็อป */
    @media (min-width: 768px) {
    body {
        font-size: 16px;
    }
    }
    </style>
</head>
    <body>
       <?php 
        if(!isset($_POST['phone']) || !isset($_POST['cus_addr'])){
            echo "<p style='color:red;'>ไม่พบข้อมุลที่กรอกหรือกรอกไม่ครบ</p>";
            echo "<a href='checkout.php'>ย้อนกลับ</a>";
            exit;
        }
        $id_promo = $_SESSION["id_promo"] ?? null;
        
        // ถ้าไม่ได้เลือกโปรโมชั่นเลย ให้เป็น null
        if (empty($id_promo) || $id_promo === "none" || strtolower($id_promo) === "null") {
            $id_promo = null;
        }

        $addr =  $_POST['cus_addr'];
        $phone = $_POST['phone'];
        $total = $_POST['total'] ;
        $username = $_SESSION['username'];
        $date = date("Y-m-d");
        $time = date("H:i:s");
        if(!isset($_SESSION["cart"]) || count($_SESSION["cart"]) == 0){
            echo "<p style='color:red;'>ไม่มีสินค้าในตะกร้า</p>";
            exit;
        }
        $total_qty = array_sum($_SESSION["cart"]); 
       
        try{
            
            $pdo->beginTransaction();
            //ส่งไปorder(db) (รวมของ1ใบเสร็จ) //insert into
            $stmt = $pdo->prepare("
            INSERT INTO Orders (
                username_cus, order_date, order_time, total_qty, total_price, final_price
            ,id_promotion) VALUES (?, ?, ?, ?, ?, ?,?)
            ");
            $stmt->execute([
            $username,
            $date,
            $time,
            $total_qty,
            $total,
            $_SESSION["finalPrice"],
            $id_promo
        ]);
            $order_id = $pdo->lastInsertId();
            //ส่งไปOrder_details แยกของแต่ละชิ้น
            foreach($_SESSION["cart"] as $id => $num_product) {
            $stmt = $pdo->prepare("SELECT price_product,num_product FROM Product WHERE id_product = ?");
            $stmt->bindParam(1,$id);
            $stmt->execute();
            $row = $stmt->fetch();
            // ตรวจสต็อกก่อน
            if ($row["num_product"] < $num_product) {
                throw new Exception("สินค้า ID #$id มีจำนวนไม่เพียงพอในคลัง");
            }
            // ลดจำนวนสินค้าในสต็อก
            $new_stock = $row["num_product"] - $num_product;
            $update = $pdo->prepare("UPDATE Product SET num_product = ? WHERE id_product = ?");
            $update->bindParam(1, $new_stock);
            $update->bindParam(2, $id);
            $update->execute();

            $stmt2 = $pdo->prepare("INSERT INTO Order_detail (id_order, id_product, qty)
                                    VALUES (?, ?, ?)");
            $stmt2->bindParam(1,$order_id);
            $stmt2->bindParam(2,$id);
            $stmt2->bindParam(3,$num_product);
            $stmt2->execute();
        }
            //ส่งไปpayment(db) สถานะdefault 'pending' -- //insert into 
            $method = "QR";
            $status = "Pending";
            $stmt = $pdo->prepare("
            INSERT INTO Payment (id_order, payment_method, payment_status)
            VALUES (?, ?, ?)
        ");
            $stmt->bindParam(1,$order_id);
            $stmt->bindParam(2,$method);
            $stmt->bindParam(3,$status);
            $stmt->execute();
        // ส่งไป shipping(db) สถานะdefault 'pending' (ใส่ก่อนเปลี่ยนแล้วค่อยแจ้ง) //insert into
            $ship_status = "Pending";
            $stmt = $pdo->prepare("
            INSERT INTO Shipping (id_order, shipping_status)
            VALUES (?, ?)
            ");
            $stmt->bindParam(1,$order_id);
            $stmt->bindParam(2, $ship_status);
            $stmt->execute();
            //ส่งไปnotification(db) textรายละเอียดคือ รายละเอียดorder+ที่อยู่+เบอร์+ราคา+สถานะการตรวจสอบชำระ //insert into
            $message = json_encode([
            "address" => $addr,
            "phone" => $phone,
            "total_price" => $total,
            "final_price"=>  $_SESSION["finalPrice"],
            "Paymentstatus" => "Pending",
            "Shippingstatus" => "Pending"
        ], JSON_UNESCAPED_UNICODE);
            $noti_type = "Order";
            $stmt = $pdo->prepare("
                INSERT INTO Notification (username_cus, id_order, message, noti_type)
                VALUES (?, ?, ?, ?)
            ");
            $stmt->bindParam(1,$username);
            $stmt->bindParam(2,$order_id);
            $stmt->bindParam(3, $message);
            $stmt->bindParam(4, $noti_type);
            $stmt->execute();
            //เพือ
            $pdo->commit();

        }catch (Exception $e) {
            //เพื่อ
            $pdo->rollBack();
            echo "<p style='color:red;'>เกิดข้อผิดพลาด: ".$e->getMessage()."</p>";
        }
        //รูป
        if(isset($_FILES["imgFile"]) && $_FILES["imgFile"]["error"] == 0 && !empty($_FILES["imgFile"]["tmp_name"])){
        $t_dir = "../admin/prove/";
        $upOk = 1;
        $imgFileType = strtolower(pathinfo($_FILES["imgFile"]["name"],PATHINFO_EXTENSION));
        $t_file = $t_dir.$order_id.".".$imgFileType ;

        //เชคว่ามีไฟล์จริงไหม
            $check = getimagesize($_FILES["imgFile"]["tmp_name"]);
            if($check !== false){
                $upOk = 1 ;
            }else{
                $upOk = 0;
                echo "ไม่ใช่ไฟล์รูปภาพ<br>";
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
                   
            }
            else{
                echo $t_file . " " . $upOk." " ;
                echo realpath($t_dir)." และ ".is_writable($t_dir);
                echo "เกิดปัญหาขัดข้องระหว่างอัพโหลดรูปภาพ<br>";
            }
        }
        }
        //ตรงtextรายละเอียดแปลงเปนjsonก่อน จะได้มีระเบียบ เวลาหน้าnotification ดึงมาใช้ จะได้เขียนstructure การเขียนแจ้งลุกค้าง่ายๆ
        //เช่น สำหรับประเภท order จะมี ที่อยู่ ,เบอร์,ราคารวม,รายละเอียดorderแยก(แบ่งตามสินค้า),สถานะการตรวจสอบ 
        unset($_SESSION["cart"]);
        unset( $_SESSION["finalPrice"]);
        unset($_SESSION["id_promo"]);
        //destroy cart Array finalPrice 
        echo "<script>
        alert('✅ สั่งซื้อเรียบร้อยแล้ว!\\nหมายเลขคำสั่งซื้อ: #" . $order_id ."');
        window.location.href = '../product/product.php';
        </script>";
        ?>
    </body>
</html>