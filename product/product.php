<?php
include dirname(__DIR__) . "/navbar.php";
try {
    $pdo = new PDO("mysql:host=localhost;dbname=168DB_49;charset=utf8", "168DB49", "gIHgCeBX");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    //echo "connected";

} catch (PDOException $e) {
    echo "error" . $e->getMessage();
}

?>

<?php
session_start();
//สร้างตะกร้าว่าง ถ้าไม่มี
if (!isset($_SESSION["cart"])) {
    $_SESSION["cart"] = array();
}

//ถ้ามีaction=addจากdetail.php
if (isset($_GET["action"]) && $_GET["action"] == "add") {
    $id_product = $_GET["id_product"];
    $num_product = $_GET["num_product"];

    //ถ้ามีสินค้าในตะกร้าแล้วให้บวกเพิ่ม
    if (isset($_SESSION["cart"][$id_product])) {
        $_SESSION["cart"][$id_product] += $num_product; //เพิ่มจำนวน
    } else {
        $_SESSION["cart"][$id_product] = $num_product; //เพิ่มสินค้าใหม่
    }

    //ถ้ากดลบสินค้าออก
    if (isset($_GET["action"]) && $_GET["action"] == "remove") {
        $id_product = $_GET["id_product"];
        unset($_SESSION["cart"][$id_product]);
    }
}
?>

<html>

<head>
    <meta charset="utf-8">
    <link rel="stylesheet" href="product.css">
</head>

<body>
    <script>
        function alertOut() {
            alert("สินค้าหมด");
        }

    function showResult(str) {
        var xhr = new XMLHttpRequest();

        if (str.length == 0) {
            xhr.open("GET", "search.php?q=");
        } else {
            xhr.open("GET", "search.php?q=" + encodeURIComponent(str));
        }

        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4 && xhr.status === 200) {
                document.getElementById("product-list").innerHTML = xhr.responseText;
            }
        };

        xhr.send();
    }

        function Filter(type) {
            var xhr = new XMLHttpRequest(); 
            xhr.open("GET", "filter.php?type=" + type); 
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4 && xhr.status === 200) { 
                    document.getElementById("product-list").innerHTML = xhr.responseText; 
                }
            };
            xhr.send(); 
        }
    </script>
    <header>
        <h1>สินค้า</h1>
    </header>

    <main id="product-section">
        <!-- แสดงสินค้าทั้งหมด -->
        <!-- <?php
                $type = isset($_GET['type']) ? $_GET['type'] : null;

                if ($type) {
                    $stmt = $pdo->prepare("SELECT * FROM Product WHERE id_type = ?");
                    $stmt->execute([$type]);
                } else {
                    $stmt = $pdo->prepare("SELECT * FROM Product");
                    $stmt->execute();
                }
                ?> -->
        <div class="searchbar">
            <form id="search-form">
                <input style="margin: 5px; border: 1px solid #ccc; border-radius: 4px; padding: 8px; width: 100%;" type="text" placeholder="ค้นหาสินค้า" onkeyup="showResult(this.value)">
            </form>
        </div>

        <fieldset class="filter-section" style="margin: 10px; padding: 13px; border: 1px solid #ccc; border-radius: 4px; ">
            <details id="type_notebook">
                <summary>สมุด</summary>
                <?php
                $notebooks = $pdo->prepare("SELECT * FROM product_type WHERE id_type != 1");
                $notebooks->execute();
                foreach ($notebooks->fetchAll() as $row) {
                    echo '<a style=" margin-left: 10px; color: black; text-decoration: underline; cursor:pointer" onclick="Filter(' . $row['id_type'] . ')">' . $row['name_type'] . '</a><br>';
                }
                ?>
            </details>

            <details id="type_planner">
                <summary>แพลนเนอร์</summary>
                <?php
                $planners = $pdo->prepare("SELECT * FROM product_type WHERE id_type = 1");
                $planners->execute();
                foreach ($planners->fetchAll() as $row) {
                    echo '<a style="margin-left: 10px; color: black; text-decoration: underline; cursor:pointer" onclick="Filter(' . $row['id_type'] . ')">' . $row['name_type'] . '</a><br>';
                }
                ?>
            </details>
            <a style="margin-left: 10px;color: red; text-decoration: none;" href="product.php">ล้างค่า</a>
        </fieldset>

        <!-- ส่วนที่จะแสดงผลการค้นหา -->
        <section id="product-list" class="product-list">
            <?php
            $type = isset($_GET['type']) ? $_GET['type'] : null;
            if ($type) {
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
                    <hr>
                <?php else: ?>
                    <article class="eachproduct soldout">
                        <figure class="product_pic_soldout">
                            <a onclick="alertOut()" class="product-link">
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
            <?php endif;
            endwhile; ?>
        </section>
    </main>
</body>


</html>