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

// Obtener los parámetros enviados
if (isset($_POST['product_name']) && isset($_POST['quantity'])) {
    $product_name = $_POST['product_name'];
    $quantity = $_POST['quantity'];
    
    // Validar la cantidad
    if ($quantity < 1) {
        echo json_encode(["status" => "error", "message" => "Cantidad inválida"]);
        exit();
    }

    // Buscar el product_id a partir del nombre del producto
    $sql = "SELECT id FROM products WHERE name = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt === false) {
        echo json_encode(["status" => "error", "message" => "Error preparing the query"]);
        exit();
    }

    $stmt->bind_param("s", $product_name);
    
    if ($stmt->execute()) {
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $product_id = $row['id']; // Obtener el id del producto

            // Ahora actualizamos la cantidad en la base de datos
            $sql_update = "UPDATE cart SET quantity = ? WHERE user_id = ? AND product_id = ?";
            $stmt_update = $conn->prepare($sql_update);

            if ($stmt_update === false) {
                echo json_encode(["status" => "error", "message" => "Error preparing the update query"]);
                exit();
            }

            $stmt_update->bind_param("iii", $quantity, $user_id, $product_id);

            if ($stmt_update->execute()) {
                echo json_encode(["status" => "success", "message" => "Cantidad actualizada"]);
            } else {
                echo json_encode(["status" => "error", "message" => "Error al actualizar la cantidad"]);
            }

            $stmt_update->close();
        } else {
            echo json_encode(["status" => "error", "message" => "Producto no encontrado"]);
        }

        $result->free();
    } else {
        echo json_encode(["status" => "error", "message" => "Error al buscar el producto"]);
    }

    $stmt->close();
} else {
    echo json_encode(["status" => "error", "message" => "Parametros incorrectos"]);
}

$conn->close();
?>
