<?php
include "../login/connect.php";
include dirname(__DIR__) . "/navbar.php";
session_start();

if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login/login-form.php");
    exit;
}

// ดึงข้อมูล product ทั้งหมด
$stmt = $pdo->prepare("SELECT * FROM Product ORDER BY id_product ASC");
$stmt->execute();
$products = $stmt->fetchAll();

// ดึงข้อมูล admin จาก session
$stmt = $pdo->prepare("SELECT * FROM Admin WHERE username_ad = ?");
$stmt->execute([$_SESSION['username']]);
$admin = $stmt->fetch();
?>
<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <title>Admin - Manage Products</title>
    <link rel="stylesheet" href="admin.css">
</head>

<body>

    <header>
        <h1 id="dashboard-header">ระบบผู้ดูแล</h1>
    </header>

    <aside class="sidebar">
        <ul class="menu-panel">
            <a class="nav-link" href="admin.php">แอดมิน</a>
            <a class="nav-link" href="admin-statistic.php">รายงานทางสถิติ</a>
            <a class="nav-link" href="admin-customer.php">จัดการผู้ใช้</a>
            <a class="nav-link" href="admin-product.php">จัดการสินค้า</a>
            <a class="nav-link" href="admin-payment.php">จัดการสถานะการชำระเงิน</a>
            <a class="nav-link" href="admin-shipping.php">จัดการสถานะการจัดส่ง</a>
        </ul>
    </aside>

    <aside class="spacer">
        <section class="admin-info">
            <h3>ข้อมูลแอดมิน</h3>
            <p><b>Username :</b> <?= $admin['username_ad'] ?></p>
            <p><b>ชื่อ :</b> <?= $admin['firstname_ad'] ?></p>
            <p><b>นามสกุล :</b> <?= $admin['lastname_ad'] ?></p>
        </section>

        <a href="admin-product-add.php" class="btn-add-product">เพิ่มสินค้า</a>

        <!-- ===== Product Section ===== -->
        <section class="product-section card">
            <h2>จัดการสินค้า</h2>
            <table>
                <thead>
                    <tr>
                        <th>รหัสสินค้า</th>
                        <th>ชื่อสินค้า</th>
                        <th>ขนาด</th>
                        <th>รายละเอียด</th>
                        <th>ราคา</th>
                        <th>จำนวน</th>
                        <th>ประเภทสินค้า</th>
                        <th>การจัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($products as $prod) : ?>
                        <tr>
                            <td><?= $prod['id_product'] ?></td>
                            <td><?= $prod['name_product'] ?></td>
                            <td><?= $prod['size_product'] ?></td>
                            <td><?= $prod['detail_product'] ?></td>
                            <td><?= $prod['price_product'] ?></td>
                            <td><?= $prod['num_product'] ?></td>
                            <td><?= $prod['id_type'] ?></td>
                            <td>
                                <a href="delete-product.php?id_product=<?= $prod['id_product'] ?>"
                                    onclick="return confirm('คุณแน่ใจว่าจะลบสินค้านี้หรือไม่?');">ลบ</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </section>
    </aside>

</body>

</html>