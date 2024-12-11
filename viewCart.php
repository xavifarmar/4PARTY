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

    $sql = "SELECT c.user_id, c.product_id, c.size, p.price, c.quantity, p.name, pi.image_url, col.id AS color_id
            FROM shopping_cart_items c 
            JOIN products p ON c.product_id = p.id
            JOIN product_images pi ON pi.product_id = c.product_id
            JOIN colors col ON col.id = p.color_id
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
                    'image_url' => $row['image_url'],
                    'color' => $row ['color_id']
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

?>