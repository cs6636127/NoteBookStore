<?php
include dirname(__DIR__) . "/navbar.php";

try {
    $pdo = new PDO("mysql:host=localhost;dbname=168DB_49;charset=utf8", "168DB49", "gIHgCeBX");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // echo "connected";
} catch (PDOException $e) {
    echo "error" . $e->getMessage();
}
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: ../login/login-form.php");
}
// ===== สร้างตะกร้าว่าง =====
if (!isset($_SESSION["cart"])) {
    $_SESSION["cart"] = array();
}
if (!isset($_SESSION["type"])) {
    $_SESSION["type"] = 0;
}
if (!isset($_SESSION["smallsize"])) {
    $_SESSION["smallsize"] = 0;
}
if (!isset($_SESSION["freeBigSize"])) {
    $_SESSION["freeBigSize"] = array();
}
if (!isset($_SESSION["freeSmallSize"])) {
    $_SESSION["freeSmallSize"] = array();
}
if (!isset($_SESSION["finalPrice"])){
    $_SESSION["finalPrice"] = 0;
}
if (!isset($_SESSION["id_promo"])){
    $_SESSION["id_promo"] = "";
}
// ===== ถ้ามี action=add จาก detail.php =====
if (isset($_GET["action"]) && $_GET["action"] == "add") {
    $id_product = $_GET["id_product"];
    $num_product = $_POST["num_product"];

    $stmt = $pdo->prepare("SELECT num_product FROM Product WHERE id_product = ?");
    $stmt->bindParam(1, $id_product);
    $stmt->execute();
    $row = $stmt->fetch();
    $stock = $row["num_product"];
    $size = $row["size_product"];

    // ถ้ามีสินค้าในตะกร้าแล้วให้บวกเพิ่ม
    if (isset($_SESSION["cart"][$id_product])) {
        if ($_SESSION["cart"][$id_product] + $num_product <= $stock) {
            $_SESSION["cart"][$id_product] += $num_product; // เพิ่มจำนวน
        } else {
            echo "จำนวนสินค้า " . $_GET['name_product'] . " มีไม่เพียงพอในจำนวนที่คุณเลือก เหลือสินค้าในคลังเพียง: " . $stock;
        }
    } else {
        $_SESSION["cart"][$id_product] = $num_product; // เพิ่มสินค้าใหม่
    }
}
// ===== ถ้ากดลบสินค้าออก =====
if (isset($_GET["action"]) && $_GET["action"] == "remove") {
    $id_product = $_GET["id_product"];
    unset($_SESSION["cart"][$id_product]);
}
// ===== ลดสินค้า =====
if (isset($_GET["action"]) && $_GET["action"] == "reduce") {
    $id_product = $_GET["id_product"];
    $_SESSION["cart"][$id_product]--;

    if ($_SESSION["cart"][$id_product] == 0) {
        unset($_SESSION["cart"][$id_product]);
    }
}
// ===== เพิ่มสินค้าทีละ 1 ที่หน้ารวม =====
if (isset($_GET["action"]) && $_GET["action"] == "increase") {
    $id_product = $_GET["id_product"];
    $stmt = $pdo->prepare("SELECT * FROM Product WHERE id_product=?");
    $stmt->bindParam(1, $id_product);
    $stmt->execute();
    $row = $stmt->fetch();
    if ($_SESSION["cart"][$id_product] < $row['num_product']) {
        $_SESSION["cart"][$id_product]++;
    }
}
?>

<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <title>ตะกร้าสินค้า</title>
    <link rel="stylesheet" href="cart.css">
<script>
    <?php
    $total = 0;
    foreach ($_SESSION["cart"] as $id => $num_product) {
        $stmt = $pdo->prepare("SELECT * FROM Product WHERE id_product=?");
        $stmt->bindParam(1, $id);
        $stmt->execute();
        $row = $stmt->fetch();
        $sum = $row["price_product"] * $num_product;
        $total += $sum;
    }
    $total_sum = $total; // แสดงราคาปกติ
    $_SESSION["finalPrice"] = $total; // เซ็ตค่าเริ่มต้น
    ?>
    async function selectPromotion() {
        const dropdown = document.getElementById("promotion").value;
        const totalSum = <?= json_encode($total_sum) ?>;  

        try {
            if (dropdown === "P001") {
                // ส่ง final price ก่อน
                const discountedPrice = totalSum - (totalSum * 0.1); // ลด 10%
                let resFinal = await fetch("save-final.php", {
                    method: "POST",
                    headers: { "Content-Type": "application/x-www-form-urlencoded" },
                    body: "finalprice=" + encodeURIComponent(discountedPrice)
                });

                if (resFinal.ok) {
                    // ส่ง promo หลังจากบันทึก final price
                    let resPromo = await fetch("save-promo.php", {
                        method: "POST",
                        headers: { "Content-Type": "application/x-www-form-urlencoded" },
                        body: "promo=" + encodeURIComponent(dropdown)
                    });

                    if (resPromo.ok) {
                        window.location.href = "checkout.php";
                    }
                }
            } else {
                // สำหรับโปรโมชั่นอื่น ส่ง promo เลย
                let resPromo = await fetch("save-promo.php", {
                    method: "POST",
                    headers: { "Content-Type": "application/x-www-form-urlencoded" },
                    body: "promo=" + encodeURIComponent(dropdown)
                });

                if (resPromo.ok) {
                    window.location.href = "checkout.php";
                }
            }
        } catch (error) {
            console.error("เกิดข้อผิดพลาด:", error);
        }
    }
</script>

</head>

<body>
    <h1 class="cart-title">ตะกร้าสินค้า</h1>

    <?php if (!empty($_SESSION["cart"])):
        $total = 0;
    ?>
        <!-- Table หลักสินค้า -->
        <table id="cartTable" border='1' cellpadding='10'>
            <tr>
                <th>สินค้า</th>
                <th>ราคา/หน่วย</th>
                <th>ลด</th>
                <th>จำนวน</th>
                <th>เพิ่ม</th>
                <th>ราคารวม</th>
                <th>ลบ</th>
            </tr>

            <?php foreach ($_SESSION["cart"] as $id => $num_product):
                $stmt = $pdo->prepare("SELECT * FROM Product WHERE id_product=?");
                $stmt->bindParam(1, $id);
                $stmt->execute();
                $row = $stmt->fetch();
                $sum = $row["price_product"] * $num_product;
                $total += $sum;
            ?>
                <tr class="cart-item">
                    <td><?= $row["name_product"] ?></td>
                    <td><?= $row["price_product"] ?></td>
                    <td><a href='cart.php?action=reduce&id_product=<?= $id ?>'>-</a></td>
                    <td><?= $num_product ?></td>
                    <td><a href='cart.php?action=increase&id_product=<?= $id ?>'>+</a></td>
                    <td><?= $sum ?></td>
                    <td><a href='cart.php?action=remove&id_product=<?= $id ?>'>ลบ</a></td>
                </tr>
            <?php endforeach; ?>

            <tr class="cart-total">
                <td colspan="3" align="right">รวมทั้งหมด</td>
                <td colspan="2"><?= $total ?> บาท</td>
            </tr>
        </table>
        <!-- Dropdown โปรโมชั่น -->
        <div class="promo-select">
            <label for="promotion" class="form-label">เลือกโปรโมชั่น </label>
            <select id="promotion">
                <?php
                $stmt = $pdo->prepare("SELECT * FROM Promotion WHERE start_date <= CURDATE() AND end_date >= CURDATE()");
                $stmt->execute();
                $promotions = $stmt->fetchAll();
                // $today_date = date("d");
                // $today_month = date("m");
                $today_date = 11;
                $today_month = 11;
                $paper = [
                    "A0" => 0,
                    "A1" => 1,
                    "A2" => 2,
                    "A3" => 3,
                    "A4" => 4,
                    "A5" => 5,
                    "A6" => 6,
                    "A7" => 7
                ];

                $hasLargePaper = false;
                $hasSmallPaperQty5 = false;

                $_SESSION["smallsize"] = 0;
                $_SESSION["type"] = 0;
                $total_sum = $total;
                $largeBookIds = [];
                $smallBookIds = [];
                $_SESSION["finalPrice"] = $total_sum;

                foreach ($_SESSION["cart"] as $id => $qty) {
                    $stmt = $pdo->prepare("SELECT size_product FROM Product WHERE id_product=?");
                    $stmt->bindParam(1, $id);
                    $stmt->execute();
                    $row = $stmt->fetch();

                    if (!$row) continue; // ป้องกันถ้าไม่มีสินค้า

                    $size = $row["size_product"];

                    if ($paper[$size] > $paper["A5"] && $qty >= 5) {
                        $hasSmallPaperQty5 = true;
                        $free = floor($qty / 5);
                        $_SESSION["smallsize"] += $free;
                        $smallBookIds[$id] = $free;
                    }
                    if ($paper[$size] <= $paper["A5"]) {
                        $hasLargePaper = true;
                        $largeBookIds[] = $id;
                    }
                }

                // ตรวจว่ามีคละอย่างน้อย 2 ชนิด
                $uniqueLargeBookIds = array_unique($largeBookIds);
                $promotionCount = floor(count($uniqueLargeBookIds) / 2);
                $_SESSION["type"] = $promotionCount; // จะได้ว่ามีจำนวนโปรโมชั่นกี่รอบ

                // เก็บ id ของสมุดเล็ก
                $uniqueSmallBookIds = array_unique($smallBookIds);
                $promotionSmallCount = floor(count($uniqueSmallBookIds));
                foreach ($promotions as $promo) {
                    if ($today_date == $today_month && $promo["id_promotion"] == "P001") {
                        echo "<option value='" . $promo["id_promotion"] . "'>";
                        echo $promo["name_promo"];
                        echo "</option>";
                    } elseif ($hasLargePaper && $promo["id_promotion"] == "P002" && $_SESSION["type"] >= 1) {
                        echo "<option value='" . $promo["id_promotion"] . "'>";
                        echo $promo["name_promo"];
                        echo "</option>";
                    } elseif ($hasSmallPaperQty5 && $promo["id_promotion"] == "P003") {
                        echo "<option value='" . $promo["id_promotion"] . "'>";
                        echo $promo["name_promo"];
                        echo "</option>";
                    }
                }
                ?>
                <option class="promo-option" value="none" selected>ไม่ใช้โปรโมชั่น</option>
            </select>
        </div>

        <!-- Table แยกโปรโมชั่น -->
        <table class="promo-table" border="1" cellpadding="10" id="promoTable"></table>
        <br>
        <div class="checkout-container">
            <a class="checkout-link" onclick="selectPromotion()" style='cursor: pointer';>สรุปและยืนยันคำสั่งซื้อ</a>
        </div>
        <div class="promo-table-container">
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const dropdown = document.getElementById('promotion');
                    
                    const tableSelect = document.getElementById('promoTable');
                    const totalSum = <?= $total_sum - ($total_sum * 0.1) ?>;

                    dropdown.addEventListener('change', function(e) {
                        const value = e.target.value;
                        if (value === 'P001') {
                            tableSelect.innerHTML = `
                            <tr>
                            <th>
                            </th>
                            <th>ราคารวม</th>
                            </tr>
                            <tr>
                                <td align='right'>ราคาสุทธิ</td>
                                <td>${totalSum} บาท</td>
                            </tr>
                        `;
                        } else if (value === 'P002') {
                            tableSelect.innerHTML = `
                            <tr><th>สินค้า(แถม)</th><th>ราคา</th><th>จำนวน</th></tr>
                            <?php
                            foreach ($_SESSION["cart"] as $id => $num_product) {
                                $stmt = $pdo->prepare("SELECT * FROM Product WHERE id_product=?");
                                $stmt->bindParam(1, $id);
                                $stmt->execute();
                                $row = $stmt->fetch();
                            }

                            for ($i = 0; $i < $_SESSION["type"]; $i++) {
                                $randomID = array_rand($_SESSION["cart"]);
                                $stmt = $pdo->prepare("SELECT name_product FROM Product WHERE id_product=?");
                                $stmt->bindParam(1, $randomID);
                                $stmt->execute();
                                $row = $stmt->fetch();

                                if ($row) {
                                    $_SESSION["freeBigSize"] = $randomId;
                                    echo "<tr><th>" . $row['name_product'] . "</th><th>0</th><th>1</th></tr>";
                                }
                            }
                            ?>
                            <tr><td>รวมทั้งหมด</td><td colspan='2'>0 บาท</td></tr>
                        `;
                        } else if (value === 'P003') {
                            tableSelect.innerHTML = `
                            <tr><th>สินค้า(แถม)</th><th>ราคา</th><th>จำนวน</th></tr>
                            <?php
                            foreach ($_SESSION["cart"] as $id => $num_product) {
                                $stmt = $pdo->prepare("SELECT * FROM Product WHERE id_product=?");
                                $stmt->bindParam(1, $id);
                                $stmt->execute();
                                $row = $stmt->fetch();
                            }

                            $uniqueSmallBookIds = array_keys($smallBookIds);

                            for ($i = 0; $i < $promotionSmallCount; $i++) {
                                $freeID = $uniqueSmallBookIds[$i];
                                $stmt = $pdo->prepare("SELECT name_product FROM Product WHERE id_product=?");
                                $stmt->bindParam(1, $freeID);
                                $stmt->execute();
                                $row = $stmt->fetch();

                                if ($row) {
                                    $_SESSION["freeSmallSize"][$freeID] = $smallBookIds[$freeID];
                                    echo "<tr><th>" . $row['name_product'] . "</th><th>0</th><th>{$smallBookIds[$freeID]}</th></tr>";
                                }
                            }
                            ?>
                            <tr><td>รวมทั้งหมด</td><td colspan='2'>0 บาท</td></tr>
                        `;
                        } else {
                            const clear = document.getElementById("promoTable");
                            clear.innerHTML = "";
                        }
                    });
                });
            </script>
        </div>
    <?php else: ?>
        <p>ยังไม่มีสินค้าในตะกร้า</p>
    <?php endif; ?>
</body>

</html>