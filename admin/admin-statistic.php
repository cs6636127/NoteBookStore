<?php
include "../login/connect.php";
include dirname(__DIR__) . "/navbar.php";
session_start();

if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login/login-form.php");
    exit;
}

// ดึงข้อมูล admin จาก session
$stmt = $pdo->prepare("SELECT * FROM Admin WHERE username_ad = ?");
$stmt->execute([$_SESSION['username']]);
$admin = $stmt->fetch();
?>

<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <title>Admin - Statistic System</title>
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
            <p><b> ชื่อ :</b> <?= $admin['firstname_ad'] ?></p>
            <p><b>นามสกุล :</b> <?= $admin['lastname_ad'] ?></p>
        </section>

        <section class="sales-report">
            <!-- ตารางที่ 1 -->
            <div class="statistic-info">
                <h2>
                    ยอดขายรายวัน/คน
                </h2>
                <table>
                    <thead>
                        <tr>
                            <th>วันที่</th>
                            <th>Username</th>
                            <th>ราคารวม</th>
                            <th>ราคาสุทธิ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $stmt_perday = $pdo->prepare("
                    SELECT order_date, username_cus, 
                           SUM(total_price) AS total, 
                           SUM(final_price) AS total_price
                    FROM Orders
                    GROUP BY order_date, username_cus
                ");
                        $stmt_perday->execute();
                        $perday = $stmt_perday->fetchAll();
                        foreach ($perday as $pd) :
                        ?>
                            <tr
                                onmouseover="this.style.backgroundColor='#e8f0ff'"
                                onmouseout="this.style.backgroundColor='#f9fafc'">
                                <td><?= $pd['order_date'] ?></td>
                                <td><?= $pd['username_cus'] ?></td>
                                <td><?= $pd['total'] ?></td>
                                <td><?= $pd['total_price'] ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- ตารางที่ 2 -->
            <div class="statistic-info">
                <h2>
                    ยอดขายรวมต่อวัน
                </h2>
                <table>
                    <thead>
                        <tr>
                            <th>วันที่</th>
                            <th>ราคารวม</th>
                            <th>ราคาสุทธิ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $stmt_sumperday = $pdo->prepare("
                    SELECT order_date, 
                           SUM(total_price) AS total, 
                           SUM(final_price) AS total_price
                    FROM Orders
                    GROUP BY order_date
                ");
                        $stmt_sumperday->execute();
                        $sumperday = $stmt_sumperday->fetchAll();
                        foreach ($sumperday as $spd) :
                        ?>
                            <tr
                                onmouseover="this.style.backgroundColor='#e8f0ff'"
                                onmouseout="this.style.backgroundColor='#f9fafc'">
                                <td><?= $spd['order_date'] ?></td>
                                <td><?= $spd['total'] ?></td>
                                <td><?= $spd['total_price'] ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </section>
        <section class="product-sales">
            <!-- ตารางที่ 3 -->
            <div class="statistic-info">
                <h2>
                    ยอดขายรวมต่อเดือน
                </h2>
                <table>
                    <thead>
                        <tr>
                            <th>วันที่</th>
                            <th>ราคารวม</th>
                            <th>ราคาสุทธิ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $stmt_sumpermonth = $pdo->prepare("
                    SELECT 
                        DATE_FORMAT(order_date, '%Y-%m') AS month,
                        SUM(total_price) AS total,
                        SUM(final_price) AS total_price
                    FROM Orders
                    GROUP BY DATE_FORMAT(order_date, '%Y-%m')
                    ORDER BY month;
                ");
                        $stmt_sumpermonth->execute();
                        $sumpermonth = $stmt_sumpermonth->fetchAll();
                        foreach ($sumpermonth as $spm) :
                        ?>
                            <tr
                                onmouseover="this.style.backgroundColor='#e8f0ff'"
                                onmouseout="this.style.backgroundColor='#f9fafc'">
                                <td><?= $spm['month'] ?></td>
                                <td><?= $spm['total'] ?></td>
                                <td><?= $spm['total_price'] ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </section>
        <section class="product-sales">
            <div class="statistic-info">
                <h2>รายงานภาพรวมรายสัปดาห์/เดือน</h2>
                <table class="week-month">
                    <thead>
                        <tr>
                            <th>เดือนที่สั่งซื้อ</th>
                            <th>สัปดาห์ที่สั่งซื้อ</th>
                            <th>ชื่อสินค้า</th>
                            <th>ประเภทสินค้า</th>
                            <th>ราคาสินค้า</th>
                            <th>จำนวนสินค้าคงคลัง</th>
                            <th>จำนวนสินค้าคงเหลือ</th>
                            <th>จำนวนที่ขายได้</th>
                            <th>รายได้รวม</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php
                        $stmt_static = $pdo->prepare("
                        SELECT 
                            DATE_FORMAT(Orders.order_date, '%Y-%m') AS order_month,   -- เดือน
                            WEEK(Orders.order_date, 1) AS order_week,                -- สัปดาห์ของเดือน
                            Product.name_product,
                            product_type.name_type,
                            Product.price_product,
                            Product.num_product,
                            (Product.num_product - SUM(Order_detail.qty)) AS current_numproduct,
                            SUM(Order_detail.qty) AS sell_qty,
                            (SUM(Order_detail.qty) * Product.price_product) AS total_income
                        FROM Product
                        JOIN product_type ON product_type.id_type = Product.id_type
                        JOIN Order_detail ON Product.id_product = Order_detail.id_product
                        JOIN Orders ON Orders.id_order = Order_detail.id_order
                        GROUP BY order_month, order_week, Product.id_product
                        ORDER BY order_month, order_week, Product.name_product");
                        $stmt_static->execute();
                        $static = $stmt_static->fetchAll();

                        foreach ($static as $st) :
                        ?>
                            <tr onmouseover="this.style.backgroundColor='#e8f0ff'" onmouseout="this.style.backgroundColor='#f9fafc'">
                                <td><?= $st['order_month'] ?></td>
                                <td><?= $st['order_week'] ?></td>
                                <td><?= $st['name_product'] ?></td>
                                <td><?= $st['name_type'] ?></td>
                                <td><?= $st['price_product'] ?></td>
                                <td><?= $st['num_product'] ?></td>
                                <td><?= $st['current_numproduct'] ?></td>
                                <td><?= $st['sell_qty'] ?></td>
                                <td><?= $st['total_income'] ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </section>


        <section class="product-sales">

            <!-- ตารางที่ 1 -->
            <div class="statistic-info">
                <h2>
                    วัน-เวลา-จำนวน-ประเภทของสินค้า
                </h2>

                <table>
                    <thead>
                        <tr>
                            <th>วันที่ถูกสั่ง</th>
                            <th>เวลาที่ถูกสั่ง</th>
                            <th>ปริมาณการขายทั้งหมด</th>
                            <th>ชื่อสินค้า</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $stmt_detail = $pdo->prepare("
                    SELECT Orders.order_date, Orders.order_time, 
                           SUM(Order_detail.qty) as total_sellqty, Product.name_product
                    FROM Order_detail
                    JOIN Orders ON Orders.id_order = Order_detail.id_order
                    JOIN Product ON Product.id_product = Order_detail.id_product
                    GROUP BY Product.name_product
                    ORDER BY Orders.order_date,Orders.order_time
                ");
                        $stmt_detail->execute();
                        $detail = $stmt_detail->fetchAll();
                        foreach ($detail as $dt) :
                        ?>
                            <tr
                                onmouseover="this.style.backgroundColor='#e8f0ff'"
                                onmouseout="this.style.backgroundColor='#f9fafc'">
                                <td><?= $dt['order_date'] ?></td>
                                <td><?= $dt['order_time'] ?></td>
                                <td><?= $dt['total_sellqty'] ?></td>
                                <td><?= $dt['name_product'] ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- ตารางที่ 2 -->
            <div class="statistic-info">
                <h2>
                    สินค้าที่ขายดีที่สุด
                </h2>

                <table>
                    <thead>
                        <tr>
                            <th>ชื่อสินค้า</th>
                            <th>ราคาสินค้า</th>
                            <th>จำนวนที่ขายได้</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $stmt_bestsell = $pdo->prepare('
                    SELECT Product.name_product, Product.price_product, 
                           SUM(Order_detail.qty) as sell_qty
                    FROM Product
                    JOIN Order_detail ON Order_detail.id_product = Product.id_product
                    GROUP BY Product.name_product, Product.price_product
                    ORDER BY sell_qty DESC;
                ');
                        $stmt_bestsell->execute();
                        $bestsell = $stmt_bestsell->fetchAll();
                        foreach ($bestsell as $bs) :
                        ?>
                            <tr
                                onmouseover="this.style.backgroundColor='#e8f0ff'"
                                onmouseout="this.style.backgroundColor='#f9fafc'">
                                <td><?= $bs['name_product'] ?></td>
                                <td><?= $bs['price_product'] ?></td>
                                <td><?= $bs['sell_qty'] ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </aside>
</body>

</html>