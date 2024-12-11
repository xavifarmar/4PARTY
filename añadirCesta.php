<?php
require 'conexion.php';
session_start();

// Verificar que el usuario esté autenticado
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];  // Obtener el ID de usuario de la sesión
} else {
    echo json_encode(["status" => "error", "message" => "No session active"]);
    exit();
}

// Recibir los datos enviados desde la app
$product_name = $_POST['product_name'];
$color_id = $_POST['color_id'];
$size = $_POST['size'];

if ($product_name && $color_id && $size) {
    // Buscar el producto por nombre y color
    $sql = "SELECT product_id FROM products WHERE name = ? AND color_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $product_name, $color_id);
    
    if ($stmt->execute()) {
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $product_id = $result->fetch_assoc()['product_id'];

            // Insertar el producto en el carrito
            $sql = "INSERT INTO shopping_cart_items (user_id, product_id, quantity, added_at) VALUES (?, ?, ?, NOW())";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("iis", $user_id, $product_id, $size);
            if ($stmt->execute()) {
                echo json_encode(["status" => "success", "message" => "Product added to cart"]);
            } else {
                echo json_encode(["status" => "error", "message" => "Error adding product to cart"]);
            }
        } else {
            echo json_encode(["status" => "error", "message" => "Product not found"]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "Database query failed"]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Missing parameters"]);
}
?>
