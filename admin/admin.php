<?php
include "../login/connect.php";
include dirname(__DIR__) . "/navbar.php";
session_start();

if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login/login-form.php");
    exit;
}

// อัปเดต payment_status ถ้ามีการ submit
if (isset($_POST['update_status'])) {
    $id_payment = $_POST['id_payment'];
    $new_status = $_POST['payment_status'];

    $stmt_update = $pdo->prepare("UPDATE Payment SET payment_status = ? WHERE id_payment = ?");
    $stmt_update->execute([$new_status, $id_payment]);

    // รีเฟรชหน้าเพื่อดูผล
    header("Location: admin.php");
    exit;
}
// ดึงข้อมูล payment ทั้งหมด
$stmt = $pdo->prepare("SELECT * FROM Payment ORDER BY id_payment ASC");
$stmt->execute();
$payments = $stmt->fetchAll();

// ดึงข้อมูล admin จาก session
$stmt = $pdo->prepare("SELECT * FROM Admin WHERE username_ad = ?");
$stmt->execute([$_SESSION['username']]);
$admin = $stmt->fetch();
?>
<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="admin.css">
</head>


<body>

    <header>
        <h1 id="dashboard-header">ระบบผู้ดแล</h1>
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
            <p><b> ชื่อ :</b> <?= $admin['firstname_ad'] ?></p>
            <p><b>นามสกุล :</b> <?= $admin['lastname_ad'] ?></p>
        </section>

        <!-- ===== Admin List Section ===== -->
        <section class="admin-list-section card">
            <h2>รายชื่อแอดมิน</h2>
            <table>
                <thead>
                    <tr>
                        <th>Username</th>
                        <th>Firstname</th>
                        <th>Lastname</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // ดึงข้อมูล Admin ทั้งหมด
                    $stmt_admins = $pdo->prepare("SELECT username_ad, firstname_ad, lastname_ad FROM Admin ORDER BY username_ad ASC");
                    $stmt_admins->execute();
                    $admins = $stmt_admins->fetchAll();

                    foreach ($admins as $adm) :
                    ?>
                        <tr>
                            <td><?= $adm['username_ad'] ?></td>
                            <td><?= $adm['firstname_ad'] ?></td>
                            <td><?= $adm['lastname_ad'] ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </section>

    </aside>
</body>

</html>