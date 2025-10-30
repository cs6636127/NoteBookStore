<?php
include "../login/connect.php";
include dirname(__DIR__) . "/navbar.php";
session_start();

if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login/login-form.php");
    exit;
}

// ดึงข้อมูล customer ทั้งหมด
$stmt = $pdo->prepare("SELECT * FROM customer ORDER BY username_cus ASC");
$stmt->execute();
$customers = $stmt->fetchAll();

// ดึงข้อมูล admin จาก session
$stmt = $pdo->prepare("SELECT * FROM Admin WHERE username_ad = ?");
$stmt->execute([$_SESSION['username']]);
$admin = $stmt->fetch();
?>
<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <title>Admin - Manage Customers</title>
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

        <!-- ===== Customer Section ===== -->
        <section class="customer-section card">
            <h2>จัดการผู้ใช้</h2>
            <table>
                <thead>
                    <tr>
                        <th>Username</th>
                        <th>Firstname</th>
                        <th>Lastname</th>
                        <th>การจัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($customers as $cus) : ?>
                        <tr>
                            <td><?= $cus['username_cus'] ?></td>
                            <td><?= $cus['firstname_cus'] ?></td>
                            <td><?= $cus['lastname_cus'] ?></td>
                            <td>
                                <a href="delete-customer.php?username=<?= $cus['username_cus'] ?>" onclick="return confirm('ยืนยันการลบผู้ใช้นี้?')">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </section>
    </aside>

</body>

</html>