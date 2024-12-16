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

// Función para obtener los productos del carrito
function getCartItems($user_id, $conn) {
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
                    'color' => $row['color_id']
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
}

// Función para obtener el precio total del carrito
function getTotalPriceCart($user_id, $conn) {
    $sql = "SELECT SUM(p.price * c.quantity) AS total_price
            FROM shopping_cart_items c
            INNER JOIN products p ON c.product_id = p.id
            WHERE c.user_id = ?";

    // Preparar la consulta
    $stmt = $conn->prepare($sql);

    if ($stmt === false) {
        echo json_encode(["status" => "error", "message" => "Error in query preparation"]);
        return;
    }

    // Vincular el parámetro $user_id
    $stmt->bind_param("i", $user_id);  // Cambié "s" por "i" ya que $user_id es un entero

    // Ejecutar la consulta
    if ($stmt->execute()) {
        // Obtener el resultado
        $result = $stmt->get_result();

        // Comprobar si hay resultados
        if ($row = $result->fetch_assoc()) {
            // Obtener el precio total
            $total_price = $row['total_price'];

            // Si el total es NULL (no hay productos), devolver 0
            if ($total_price === null) {
                $total_price = 0;
            }

            // Retornar el total en formato JSON
            echo json_encode(["status" => "success", "total_price" => $total_price]);
        } else {
            echo json_encode(["status" => "error", "message" => "No products found"]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "Failed to execute query"]);
    }

    // Cerrar la declaración
    $stmt->close();
}



// Función para actualizar la cantidad de un producto en el carrito
function updateQuantity($user_id, $product_id, $new_quantity, $conn) {
    $sql = "UPDATE shopping_cart_items
            SET quantity = ?
            WHERE user_id = ? AND product_id = ?";

    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        echo json_encode(["status" => "error", "message" => "Error in query preparation"]);
        return;
    }

    // Vincular los parámetros
    $stmt->bind_param("iii", $new_quantity, $user_id, $product_id);

    // Ejecutar la consulta
    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Quantity updated"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Failed to update quantity"]);
    }

    $stmt->close();
}

// Llamada a las funciones dependiendo de la solicitud recibida
if (isset($_GET['getCartItems'])) {
    getCartItems($user_id, $conn);
} elseif (isset($_GET['getTotalPriceCart'])) {
    getTotalPriceCart($user_id, $conn);
} elseif (isset($_GET['updateQuantity']) && isset($_GET['product_id']) && isset($_GET['new_quantity'])) {
    $product_id = $_GET['product_id'];
    $new_quantity = $_GET['new_quantity'];
    updateQuantity($user_id, $product_id, $new_quantity, $conn);
} else {
    echo json_encode(["status" => "error", "message" => "Invalid request"]);
}
?>
