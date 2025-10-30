<?php
include "../login/connect.php";

$type = isset($_GET['type']) ? intval($_GET['type']) : 0;

if ($type > 0) {
    $stmt = $pdo->prepare("SELECT * FROM Product WHERE id_type = ?");
    $stmt->execute([$type]);
} else {
    $stmt = $pdo->query("SELECT * FROM Product");
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
<?php
    endif;
endwhile;
?>