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
    $sql = "SELECT id FROM products WHERE name = ? AND color_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $product_name, $color_id);
    
    if ($stmt->execute()) {
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $product_id = $result->fetch_assoc()['id'];

            // Insertar el producto en el carrito
            $sql = "INSERT INTO shopping_cart_items (user_id, product_id, quantity, size, added_at) VALUES (?, ?, ?, NOW())";
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


/*function getCartProducts() {
    global $conn, $user_id;  // Asegúrate de que $conn esté disponible y $user_id

    $sql = "SELECT c.user_id, c.product_id, c.size, p.price, c.quantity, p.name, pi.image_url
            FROM shopping_cart_items c 
            JOIN products p ON c.product_id = p.id
            JOIN product_images pi ON pi.product_id = c.product_id
            WHERE c.user_id = ?";
    
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        echo json_encode(["status" => "error", "message" => "Error in SQL query preparation"]);
        return;
    }

    // Bind the user_id to the statement
    $stmt->bind_param("i", $user_id); 

    if ($stmt->execute()) {
        $result = $stmt->get_result();
        $cartItems = [];

        if ($result->num_rows > 0) {
            // Recoger todos los productos del carrito
            while ($row = $result->fetch_assoc()) {
                $cartItems[] = [
                    'product_name' => $row['name'],
                    'price' => $row['price'],
                    'size' => $row['size'],
                    'quantity' => $row['quantity'],
                    'image_url' => $row['image_url']
                ];
            }

            // Retornar los productos en formato JSON
            echo json_encode(["status" => "success", "cart_items" => $cartItems]);
        } else {
            echo json_encode(["status" => "error", "message" => "No products in cart"]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "Failed to execute query"]);
    }
}*/
?>
