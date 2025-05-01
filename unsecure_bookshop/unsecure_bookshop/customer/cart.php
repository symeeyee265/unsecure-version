<?php
include '../db.php';
session_start();

// Check login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Remove item (no CSRF/token protection — intentionally vulnerable)
if (isset($_GET['delete'])) {
    $cart_id = $_GET['delete'];
    mysqli_query($conn, "DELETE FROM cart WHERE id = '$cart_id' AND user_id = '$user_id'");
    echo "<script>alert('Item removed!'); window.location='cart.php';</script>";
}

// Fetch cart items
$cart_items = mysqli_query($conn, "SELECT cart.*, books.title, books.price 
                                   FROM cart 
                                   JOIN books ON cart.book_id = books.id 
                                   WHERE cart.user_id = '$user_id'");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Your Cart</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <h2>🛒 Your Shopping Cart</h2>

    <table border="1" cellpadding="5" cellspacing="0">
        <tr>
            <th>Title</th>
            <th>Quantity</th>
            <th>Price (Each)</th>
            <th>Subtotal</th>
            <th>Action</th>
        </tr>

        <?php
        $total = 0;
        while ($row = mysqli_fetch_assoc($cart_items)) {
            $quantity = $row['quantity'];
            $price = $row['price'];
            $subtotal = $price * $quantity;
            $total += $subtotal;

            echo "<tr>";
            echo "<td>" . htmlspecialchars($row['title']) . "</td>";
            echo "<td>" . $quantity . "</td>";
            echo "<td>$" . number_format($price, 2) . "</td>";
            echo "<td>$" . number_format($subtotal, 2) . "</td>";
            echo "<td><a href='cart.php?delete=" . $row['id'] . "'>Remove</a></td>";
            echo "</tr>";
        }
        ?>
    </table>

    <h3>Total: $<?php echo number_format($total, 2); ?></h3>

    <?php if ($total > 0): ?>
    <form action="checkout.php" method="post">
        <button type="submit" name="checkout">Proceed to Checkout</button>
    </form>
    <?php else: ?>
        <p>Your cart is empty.</p>
    <?php endif; ?>

    <p><a href="explore.php">← Continue Shopping</a></p>
</body>
</html>
