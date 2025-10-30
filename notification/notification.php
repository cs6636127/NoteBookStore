<?php
include "../login/connect.php";
include dirname(__DIR__) . "/navbar.php";
?>
<html>

<head>
    <meta charset="utf-8">
    <link rel="stylesheet" href="notification.css">
</head>

<body>
    <?php
    if (!isset($_SESSION['username'])) {
        header("Location: ../login/login-form.php");
        exit;
    }

    $username = $_SESSION['username'];

    $stmt = $pdo->prepare("SELECT * FROM Notification WHERE username_cus = ? ORDER BY noti_datetime DESC");
    $stmt->bindParam(1, $username);
    $stmt->execute();
    
    ?>

    <main>
        <header>
            <h1>รายการแจ้งเตือนของคุณ</h1>
        </header>

        <section class="notifications">
            <?php if ($stmt->rowCount() == 0): ?>
                <p>ไม่มีการแจ้งเตือน</p>
            <?php else: ?>
                <?php while ($row = $stmt->fetch()):
                    $type = $row['noti_type'];
                    $message = json_decode($row['message'], true);
                ?>
                    <article class="notification-item">
                        <a href="notificationDetails.php?id_noti=<?= $row['id_noti'] ?>&id_order=<?= $row['id_order'] ?>">
                            <p><strong>หมายเลขคำสั่งซื้อ : </strong> #<?= $row['id_order'] ?></p>
                            <p><strong>ประเภท : </strong> <?= $type ?></p>
                            <?php
                            // กำหนดค่า default เป็น Pending
                            $status = $message['payment_status'] ?? 'Pending';
                            $status2 = $message['shipping_status'] ?? 'Pending';
                            if($status2 !== 'Pending'){
                                $status = "Paid";
                            }
                            // กำหนดสีตามสถานะ
                            $color = 'gray'; // default
                            if ($status === 'Paid') {
                                $color = '#2ba200ff'; // สีเขียว
                            } elseif ($status === 'Failed') {
                                $color = '#c80014ff'; // สีแดง
                            } elseif ($status === 'Pending') {
                                $color = '#777777ff'; // สีเทา
                            }
                            ?>
                            <p><strong>สถานะการชำระเงิน : </strong>
                                <span style="color: <?= $color ?>"><?= htmlspecialchars($status) ?></span>
                            </p>
                            <header>
                                <time datetime="<?= $row['noti_datetime'] ?>">
                                    วันที่แจ้ง : <?= $row['noti_datetime'] ?>
                                </time>
                            </header>
                        </a>
                    </article>
                <?php endwhile; ?>
            <?php endif; ?>
        </section>

    </main>
</body>

</html>