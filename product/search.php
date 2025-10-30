<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include "../login/connect.php";

$q = isset($_GET["q"]) ? trim($_GET["q"]) : "";

if ($q == "") {
    $stmt = $pdo->query("SELECT * FROM Product");
} else {
    $stmt = $pdo->prepare("
        SELECT * FROM Product
        WHERE name_product LIKE ? 
        OR size_product LIKE ? 
        OR detail_product LIKE ?
    ");
    $like = "%$q%";
    $stmt->execute([$like, $like, $like]);
}

while ($row = $stmt->fetch()):
    if ($row["num_product"] > 0):
?>
        <article class="eachproduct">
            <figure class="product_pic">
                <a href="details.php?id_product=<?= $row["id_product"] ?>" class="product-link">
                    <img src="product_photo/<?= $row["id_product"] ?>.jpg?<?= time() ?>" alt="<?= $row["name_product"] ?>">
                </a>
            </figure>
            <div class="product_detail">
                <h3><?= $row["name_product"] ?></h3>
                <p class="price"><?= $row["price_product"] ?> บาท</p>
            </div>
        </article>
        <hr>
    <?php else: ?>
        <article class="eachproduct soldout">
            <figure class="product_pic_soldout">
                <a onclick="alert('สินค้าหมด')" class="product-link">
                    <img src="product_photo/<?= $row["id_product"] ?>.jpg?<?= time() ?>" alt="<?= $row["name_product"] ?>">
                </a>
            </figure>
            <div class="product_detail_soldout">
                <h4>สินค้าหมด</h4>
                <h3><?= $row["name_product"] ?></h3>
                <p class="price"><?= $row["price_product"] ?> บาท</p>
            </div>
        </article>
        <hr>
<?php
    endif;
endwhile;
?>