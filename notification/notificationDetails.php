<?php
include "../login/connect.php";
session_start();
include dirname(__DIR__) . "/navbar.php";

if (!isset($_SESSION['username'])) {
    header("Location: ../login/login-form.php");
    exit;
}

$username = $_SESSION['username'];

$id_noti = $_GET['id_noti'] ;
$id_order = $_GET['id_order'] ;

// ดึงข้อมูลคำสั่งซื้อ
$stmt = $pdo->prepare("SELECT * FROM Orders WHERE id_order = ?");
$stmt->execute([$id_order]);
$order = $stmt->fetch();

// ดึงข้อมูล Payment status จาก text_noti
$stmt2= $pdo->prepare("SELECT * FROM Notification WHERE id_noti = ?");
$stmt2->execute([$id_noti]);
$noti = $stmt2->fetch();
$message = [];
if ($noti && isset($noti['message'])) {
    $message = json_decode($noti['message'], true);
}
$payment_status = $message['payment_status'] ?? 'Pending';
// ดึงข้อมูล Payment จาก text (snapshot)
//$stmt_payment = $pdo->prepare("SELECT * FROM Payment WHERE id_order = ?");
//$stmt_payment->execute([$id_order]);
//$payment = $stmt_payment->fetch();
//$payment_status = $payment['payment_status'] ?? 'Pending';

// ดึงข้อมูล Shipping จาก text (snapshot)
$shipping_status = $message['shipping_status'] ?? 'Pending';
if($shipping_status !== 'Pending'){
    $payment_status = 'Paid';
}
//Shippingstatus
//$stmt_shipping = $pdo->prepare("SELECT * FROM Shipping WHERE id_order = ?");
//$stmt_shipping->execute([$id_order]);
//$shipping = $stmt_shipping->fetch();

// กำหนดสีตามสถานะ Payment
switch (strtolower($payment_status)) {
    case 'ชำระแล้ว':
    case 'paid':
        $payment_color = '#28a745';
        break;
    case 'รอชำระ':
    case 'pending':
        $payment_color = '#007bff';
        break;
    case 'ไม่สำเร็จ':
    case 'failed':
        $payment_color = '#dc3545';
        break;
    default:
        $payment_color = '#6c757d';
        break;
}

// กำหนดสีตามสถานะ Shipping
switch (strtolower($shipping_status)) {
    case 'จัดส่งแล้ว':
    case 'shipped':
        $shipping_color = '#28a745';
        break;
    case 'กำลังจัดส่ง':
    case 'delivered':
        $shipping_color = '#ffc107';
        break;
    case 'รอจัดส่ง':
    case 'pending':
        $shipping_color = '#007bff';
        break;
    case 'ยกเลิก':
    case 'cancelled':
        $shipping_color = '#dc3545';
        break;
    default:
        $shipping_color = '#6c757d';
        break;
}
?>
<html>

<head>
    <meta charset="utf-8">
    <link rel="stylesheet" href="notification.css">
    <title>รายละเอียดการแจ้งเตือน</title>
</head>

<body>
    <main id="notification-detail">
        <article class="notification-card">
            <header>
                <h1>รายละเอียดคำสั่งซื้อ #<?= htmlspecialchars($id_order) ?></h1>
            </header>

            <section class="notification-info">
                <?php
                $timeThai = date("H:i:s", strtotime($order['order_time']) + 7 * 3600);
                ?>
                <p><strong>วันที่สั่งซื้อ : </strong> <?= htmlspecialchars($order['order_date']) ?> เวลา <?= $timeThai ?></p>
                <p><strong>สถานะการชำระเงิน : </strong>
                    <span style="color: <?= $payment_color ?>; font-weight: bold;"><?= htmlspecialchars($payment_status) ?></span>
                </p>
                <p><strong>สถานะการจัดส่ง : </strong>
                    <span style="color: <?= $shipping_color ?>; font-weight: bold;"><?= htmlspecialchars($shipping_status) ?></span>
                </p>
            </section>

            <section class="order-info">
                <h2>รายการสินค้า</h2>
                <table class="order-table">
                    <thead>
                        <tr>
                            <th>สินค้า</th>
                            <th>ราคา</th>
                            <th>จำนวน</th>
                            <th>ราคารวม</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $stmt_detail = $pdo->prepare("
                        SELECT p.name_product, p.price_product, od.qty
                        FROM Order_detail od
                        JOIN Product p ON od.id_product = p.id_product
                        WHERE od.id_order = ?
                    ");
                        $stmt_detail->execute([$id_order]);
                        $total_sum = 0;
                        while ($item = $stmt_detail->fetch()):
                            $sum = $item['price_product'] * $item['qty'];
                            $total_sum += $sum;
                        ?>
                            <tr>
                                <td><?= htmlspecialchars($item['name_product']) ?></td>
                                <td><?= htmlspecialchars($item['price_product']) ?></td>
                                <td><?= htmlspecialchars($item['qty']) ?></td>
                                <td><?= htmlspecialchars($sum) ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </section>
            <div id="price-summary">
                <p id="total-price"><strong>ยอดรวม : </strong> <?= htmlspecialchars($order['total_price']) ?> บาท</p>
                <p id="final-price"><strong>ยอดสุทธิ : </strong> <?= htmlspecialchars($order['final_price']) ?> บาท</p>
            </div>
            <footer>
                <a href="notification.php" class="back-link">ย้อนกลับ</a>
            </footer>
        </article>
    </main>
</body>

</html>