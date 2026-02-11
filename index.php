<?php
session_start();

/* ---------- CART INITIALIZATION ---------- */
if (!isset($_SESSION["cart"])) {
    $_SESSION["cart"] = [];
}

/* ---------- ADD TO CART ---------- */
if (isset($_POST["add"])) {
    $id = $_POST["id"];
    $title = $_POST["title"];
    $price = $_POST["price"];

    if (isset($_SESSION["cart"][$id])) {
        $_SESSION["cart"][$id]["qty"]++;
    } else {
        $_SESSION["cart"][$id] = [
            "title" => $title,
            "price" => $price,
            "qty" => 1
        ];
    }
}

/* ---------- REMOVE ---------- */
if (isset($_POST["remove"])) {
    unset($_SESSION["cart"][$_POST["remove"]]);
}

/* ---------- PLACE ORDER ---------- */
$showBill = false;
if (isset($_POST["placeorder"])) {
    $showBill = true;
}
?>

<!DOCTYPE html>
<html>
<head>
<title>PHP E-Commerce</title>
<meta name="viewport" content="width=device-width, initial-scale=1">

<style>
body{margin:0;background:#f4f6f9;font-family:Segoe UI;}
nav{background:#1f2937;color:white;padding:12px 20px;display:flex;justify-content:space-between}
nav input{padding:7px;width:250px;border-radius:5px;border:none}
.container{display:grid;grid-template-columns:3fr 1fr;gap:15px;padding:20px}
.products{display:grid;grid-template-columns:repeat(auto-fill,minmax(220px,1fr));gap:15px}
.card{background:white;padding:10px;border-radius:10px;box-shadow:0 2px 6px #0002}
.card img{height:150px;object-fit:contain}
.price{color:green;font-weight:bold}
button{padding:6px;border:none;border-radius:5px;background:#10b981;color:white}
.cart{background:white;padding:15px;border-radius:10px}
.cart-item{display:flex;justify-content:space-between;margin-bottom:5px}
.place{width:100%;background:#2563eb;margin-top:10px}
.bill{background:white;padding:20px;width:350px;margin:auto}
.modal{position:fixed;inset:0;background:#0007;display:flex;align-items:center;justify-content:center}
</style>
</head>

<body>

<nav>
<h2>🛒 ShopEasy PHP</h2>

<form method="get">
<input type="text" name="search" placeholder="Search">
</form>

<div>Cart (<?php echo count($_SESSION["cart"]); ?>)</div>
</nav>

<div class="container">

<!-- PRODUCTS -->
<div class="products">

<?php
$data = json_decode(file_get_contents("https://dummyjson.com/products"), true);
$products = $data["products"];

$search = $_GET["search"] ?? "";

foreach ($products as $p) {
    if ($search && stripos($p["title"], $search) === false) continue;
?>
<div class="card">
<img src="<?php echo $p["thumbnail"]; ?>">
<h4><?php echo $p["title"]; ?></h4>
<div class="price">$<?php echo $p["price"]; ?></div>

<form method="post">
<input type="hidden" name="id" value="<?php echo $p["id"]; ?>">
<input type="hidden" name="title" value="<?php echo $p["title"]; ?>">
<input type="hidden" name="price" value="<?php echo $p["price"]; ?>">
<button name="add">Add to Cart</button>
</form>

</div>
<?php } ?>

</div>

<!-- CART -->
<div class="cart">
<h3>Cart</h3>

<?php
$total = 0;
foreach ($_SESSION["cart"] as $id=>$item) {
    $subtotal = $item["price"]*$item["qty"];
    $total += $subtotal;
?>
<div class="cart-item">
<?php echo $item["title"]; ?> x<?php echo $item["qty"]; ?>
<form method="post">
<button name="remove" value="<?php echo $id; ?>">x</button>
</form>
</div>
<?php } ?>

<h3>Total: $<?php echo $total; ?></h3>

<form method="post">
<button class="place" name="placeorder">Place Order</button>
</form>

</div>
</div>

<!-- BILL -->
<?php if($showBill): ?>
<div class="modal">
<div class="bill">
<h3>Invoice</h3>

<?php
$tax = $total * 0.10;
$grand = $total + $tax;

foreach ($_SESSION["cart"] as $item){
 echo "<p>{$item['title']} x{$item['qty']} - $".($item['price']*$item['qty'])."</p>";
}
?>

<hr>
<p>Subtotal: $<?php echo $total; ?></p>
<p>Tax(10%): $<?php echo number_format($tax,2); ?></p>
<h3>Grand Total: $<?php echo number_format($grand,2); ?></h3>

<button onclick="window.print()">Print</button>

<?php $_SESSION["cart"] = []; ?>

</div>
</div>
<?php endif; ?>

</body>
</html>
