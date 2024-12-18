<?php
require 'conexion.php';
session_start();

// Asegúrate de que el usuario esté autenticado
if (!isset($_SESSION['user_id'])) {
    echo json_encode(["error" => "Usuario no autenticado"]);
    exit();
}

$user_id = $_SESSION['user_id'];  // Asumimos que el ID del usuario está en la sesión

// Consulta para obtener productos y sus imágenes y sus likes
$sql = "SELECT p.id, p.name, p.price, pi.image_url, pi.is_primary, 
               IFNULL(li.is_liked, 0) AS liked
        FROM products p 
        LEFT JOIN product_images pi ON p.id = pi.product_id
        LEFT JOIN product_likes li ON li.product_id = p.id AND li.user_id = ?
        ORDER BY p.id";

$result = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);  // Vinculamos el ID del usuario a la consulta
$stmt->execute();
$result = $stmt->get_result();

// Verificar si hay productos
if ($result->num_rows > 0) {
    // Empezamos la estructura de respuesta JSON
    $products = [];

    // Recorremos los productos
    while($row = $result->fetch_assoc()) {
        $product_id = $row['id'];

        // Si el producto no está en el array, lo agregamos
        if (!isset($products[$product_id])) {
            $products[$product_id] = [
                'id' => $row['id'],
                'name' => $row['name'],
                'price' => $row['price'],
                'liked' => (int)$row['liked'],
                'images' => $row['image']
            ];
        }

        // Agregamos la imagen si está disponible
        if ($row['image_url']) {
            $products[$product_id]['images'][] = [
                'url' => $row['image_url'],
                'is_primary' => $row['is_primary']
            ];
        }
    }

    // Devolvemos los productos en formato JSON
    echo json_encode(array_values($products));
} else {
    // Si no hay productos, devolvemos un array vacío
    echo json_encode([]);
}

$conn->close();
?>
