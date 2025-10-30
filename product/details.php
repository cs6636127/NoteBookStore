<?php
session_start();
include dirname(__DIR__) . "/navbar.php";
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = array();
}
try {
    $pdo = new PDO("mysql:host=localhost;dbname=168DB_49;charset=utf8", "168DB49", "gIHgCeBX");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    //echo "connected";

} catch (PDOException $e) {
    echo "error" . $e->getMessage();
}

?>

<html>

<head>
    <meta charset="utf-8">
    <link rel="stylesheet" href="product_detail.css">
</head>
<!-- <h1>มีสินค้าจำนวน <?= sizeof($_SESSION['cart']) ?> ชิ้น ในตะกร้า (เทส)</h1> -->
<?php
$stmt = $pdo->prepare("SELECT * FROM Product WHERE id_product = ?");
$stmt->bindParam(1, $_GET["id_product"]);
$stmt->execute();
$row = $stmt->fetch();
?>

<body>
    <h2 class="detail-name">รายละเอียดสินค้า</h2>
    <article class="product-card">
        <figure class="product-image">
            <img src="product_photo/<?= $row["id_product"] ?>.jpg?<?= time() ?>"
                alt="<?= $row["name_product"] ?>">
        </figure>

        <section class="product-info">
            <header>
                <h2><?= $row["name_product"] ?></h2>
            </header>
            <p><strong>รายละเอียดสินค้า : </strong> <?= $row["detail_product"] ?></p>
            <p><strong>ราคาขาย : </strong> <?= $row["price_product"] ?> บาท</p>

            <form action="cart.php?id_product=<?= $row["id_product"] ?>&action=add" method="post">
                <label for="num_product_<?= $row["id_product"] ?>">จำนวน :</label>
                <input type="number"
                    id="num_product_<?= $row["id_product"] ?>"
                    name="num_product"
                    value="1"
                    min="1"
                    max="<?= $row["num_product"] ?>">
                <button type="submit" name="submit" value="buy">หยิบใส่ตะกร้า</button>
            </form>
        </section>
    </article>
</body>

</html>