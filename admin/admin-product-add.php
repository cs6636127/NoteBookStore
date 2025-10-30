<?php
include "../login/connect.php";
session_start();

// ตรวจสอบสิทธิ์ admin
if (!isset($_SESSION["role"]) || $_SESSION["role"] !== "admin") {
    echo "<script>
        alert('🚫 คุณไม่มีสิทธิ์เข้าหน้านี้');
        window.location.href='../login/login-form.php';
    </script>";
    exit;
}

// ดึงประเภทสินค้า
$typeStmt = $pdo->query("SELECT * FROM product_type ORDER BY name_type ASC");
$types = $typeStmt->fetchAll(PDO::FETCH_ASSOC);

// ตรวจสอบฟอร์ม submit
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST['name_product']);
    $size = trim($_POST['size_product']);
    $detail = trim($_POST['detail_product']);
    $price = trim($_POST['price_product']);
    $num = trim($_POST['num_product']);
    $id_type = trim($_POST['id_type']);

    if (empty($name) || empty($size) || empty($price) || empty($num) || empty($id_type)) {
        $error = "⚠️ กรุณากรอกข้อมูลให้ครบทุกช่อง";
    } elseif (!isset($_FILES['product_image']) || $_FILES['product_image']['error'] !== 0) {
        $error = "⚠️ กรุณาเลือกไฟล์รูปสินค้า (jpg)";
    } else {
        try {
            // บันทึกสินค้าในฐานข้อมูล
            $stmt = $pdo->prepare("INSERT INTO Product (name_product, size_product, detail_product, price_product, num_product, id_type) 
                                   VALUES (:name, :size, :detail, :price, :num, :id_type)");
            $stmt->execute([
                ':name' => $name,
                ':size' => $size,
                ':detail' => $detail,
                ':price' => $price,
                ':num' => $num,
                ':id_type' => $id_type
            ]);

            // ดึง id_product ของสินค้าที่เพิ่งเพิ่ม
            $new_id = $pdo->lastInsertId();

            // อัปโหลดรูป
            $targetDir = "../product/product_photo/";
            if (!file_exists($targetDir)) {
                mkdir($targetDir, 0755, true);
            }

            $fileTmp  = $_FILES['product_image']['tmp_name'];
            $filePath = $targetDir . $new_id . ".jpg"; // ตั้งชื่อไฟล์เป็น id_product.jpg

            if (move_uploaded_file($fileTmp, $filePath)) {
                echo "<script>
                    alert('✅ เพิ่มสินค้าสำเร็จและอัปโหลดรูปเรียบร้อยแล้ว');
                    window.location.href='admin-product.php';
                </script>";
            } else {
                echo "<script>
                    alert('⚠️ เพิ่มสินค้าเรียบร้อย แต่ไม่สามารถอัปโหลดรูปได้');
                    window.location.href='admin-product.php';
                </script>";
            }
            exit;
        } catch (PDOException $e) {
            $error = "❌ เกิดข้อผิดพลาด: " . $e->getMessage();
            echo $filePath . "เช็ค path";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <title>เพิ่มสินค้าใหม่</title>
    <style>
        /* Reset เบื้องต้น */
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        /* Background */
        body {
            background-color: #f4f6f8;
            min-height: 100vh;
            padding-top: 70px;
            /* สำหรับ navbar fixed */
            display: flex;
            justify-content: center;
        }

        /* Container */
        .container {
            background-color: #fff;
            width: 100%;
            max-width: 500px;
            padding: 30px 40px;
            border-radius: 12px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
            margin-bottom: 50px;
        }

        /* Headings */
        .container h2 {
            text-align: center;
            margin-bottom: 25px;
            color: #333;
        }

        /* Error message */
        p {
            margin-bottom: 15px;
            font-weight: bold;
            text-align: center;
            color: red;
        }

        /* Form Styles */
        form {
            display: flex;
            flex-direction: column;
        }

        label {
            margin-bottom: 5px;
            font-weight: 500;
            color: #555;
        }

        input[type="text"],
        input[type="number"],
        textarea,
        select,
        input[type="file"] {
            padding: 10px 12px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 16px;
            transition: all 0.2s ease;
        }

        input[type="text"]:focus,
        input[type="number"]:focus,
        textarea:focus,
        select:focus,
        input[type="file"]:focus {
            border-color: #007bff;
            outline: none;
            box-shadow: 0 0 5px rgba(0, 123, 255, 0.3);
        }

        textarea {
            resize: vertical;
        }

        /* Submit / Cancel Buttons */
        .btn-group {
            display: flex;
            gap: 10px;
        }

        input[type="submit"],
        input[type="button"] {
            flex: 1;
            padding: 12px;
            font-size: 16px;
            font-weight: bold;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        input[type="submit"] {
            background-color: #000000ff;
            color: #fff;
        }

        input[type="submit"]:hover {
            background-color: #313131ff;
        }

        input[type="button"] {
            background-color: #888888ff;
            color: #fff;
        }

        input[type="button"]:hover {
            background-color: #555555ff;
        }

        /* Responsive */
        @media (max-width: 600px) {
            .container {
                padding: 20px;
                margin: 0 10px 50px 10px;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <h2>เพิ่มสินค้าใหม่</h2>

        <?php if (isset($error)) echo "<p>$error</p>"; ?>

        <form method="post" enctype="multipart/form-data">
            <label>ชื่อสินค้า</label>
            <input type="text" name="name_product" required>

            <label>ขนาด</label>
            <input type="text" name="size_product" required>

            <label>รายละเอียดสินค้า</label>
            <textarea name="detail_product" rows="4"></textarea>

            <label>ราคา</label>
            <input type="number" name="price_product" step="0.01" required>

            <label>จำนวนสินค้า</label>
            <input type="number" name="num_product" required>

            <label>ประเภทสินค้า</label>
            <select name="id_type" required>
                <option value="">-- เลือกประเภทสินค้า --</option>
                <?php foreach ($types as $type): ?>
                    <option value="<?= $type['id_type'] ?>"><?= htmlspecialchars($type['name_type']) ?></option>
                <?php endforeach; ?>
            </select>

            <label>รูปสินค้า (jpg)</label>
            <input type="file" name="product_image" accept=".jpg" required>

            <div class="btn-group">
                <input type="submit" value="เพิ่มสินค้า">
                <input type="button" value="ยกเลิก" onclick="window.location.href='admin-product.php'">
            </div>
        </form>
    </div>
</body>

</html>