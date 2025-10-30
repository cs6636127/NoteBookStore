<?php
include "../login/connect.php";
include dirname(__DIR__) . "/navbar.php";
session_start();

if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login/login-form.php");
    exit;
}

// อัปเดต shipping_status ถ้ามีการ submit
if (isset($_POST['update_shipping'])) {
    $id_shipping = $_POST['id_shipping'];
    $new_status = $_POST['shipping_status'];

    // 🔹 ดึง order_id จากตาราง Shipping
    $stmt = $pdo->prepare("SELECT id_order FROM Shipping WHERE id_shipping = ?");
    $stmt->execute([$id_shipping]);
    $shipping_data = $stmt->fetch();
    $id_order = $shipping_data['id_order'];

    // 🔹 ดึง username_cus จาก Orders
    $stmt = $pdo->prepare("SELECT username_cus FROM Orders WHERE id_order = ?");
    $stmt->execute([$id_order]);
    $order_data = $stmt->fetch();
    $username_cus = $order_data['username_cus'];

    // 🔹 อัปเดตสถานะใน Shipping
    $stmt_update = $pdo->prepare("UPDATE Shipping SET shipping_status = ? WHERE id_shipping = ?");
    $stmt_update->execute([$new_status, $id_shipping]);

    // 🔹 สร้าง Notification แจ้งลูกค้า
    $message = json_encode([
        "text" => "สถานะการจัดส่งของคำสั่งซื้อ #$id_order ถูกเปลี่ยนเป็น $new_status",
        "order_id" => $id_order,
        "shipping_status" => $new_status
    ], JSON_UNESCAPED_UNICODE);

    $noti_type = "Shipping";

    $stmt_noti = $pdo->prepare("INSERT INTO Notification (username_cus, id_order, message, noti_type) VALUES (?, ?, ?, ?)");
    $stmt_noti->execute([$username_cus, $id_order, $message, $noti_type]);

    // 🔹 รีเฟรชหน้า
    header("Location: admin-shipping.php");
    exit;
}


// ดึงข้อมูล shipping ทั้งหมด
$stmt = $pdo->prepare("SELECT * FROM Shipping ORDER BY id_shipping ASC");
$stmt->execute();
$shippings = $stmt->fetchAll();

// ดึงข้อมูล admin จาก session
$stmt = $pdo->prepare("SELECT * FROM Admin WHERE username_ad = ?");
$stmt->execute([$_SESSION['username']]);
$admin = $stmt->fetch();
?>
<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <title>Admin - Manage Shipping</title>
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

        <!-- ===== Shipping Section ===== -->
        <section class="shipping-section card">
            <h2>จัดการสถานะการจัดส่ง</h2>
            <table>
                <thead>
                    <tr>
                        <th>รหัสการจัดส่ง</th>
                        <th>รหัสคำสั่งซื้อ</th>
                        <th>สถานะการจัดส่ง</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($shippings as $ship) : ?>
                        <tr>
                            <td><?= $ship['id_shipping'] ?></td>
                            <td><?= $ship['id_order'] ?></td>
                            <td>
                                <form method="post" style="margin:0;">
                                    <input type="hidden" name="id_shipping" value="<?= $ship['id_shipping'] ?>">
                                    <select name="shipping_status">
                                        <option value="Pending" <?= $ship['shipping_status'] == 'Pending' ? 'selected' : '' ?>>Pending</option>
                                        <option value="Shipped" <?= $ship['shipping_status'] == 'Shipped' ? 'selected' : '' ?>>Shipped</option>
                                        <option value="Delivered" <?= $ship['shipping_status'] == 'Delivered' ? 'selected' : '' ?>>Delivered</option>
                                        <option value="Cancelled" <?= $ship['shipping_status'] == 'Cancelled' ? 'selected' : '' ?>>Cancelled</option>
                                    </select>
                                    <button type="submit" name="update_shipping">Update</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </section>
    </aside>

</body>

</html>