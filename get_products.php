<?php
require 'conexion.php';

// Consulta para obtener productos y sus imágenes
$sql = "SELECT p.id, p.name, p.price, pi.image_url, pi.is_primary 
        FROM products p 
        LEFT JOIN product_images pi ON p.id = pi.product_id
        ORDER BY p.id";
$result = $conn->query($sql);

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
                'images' => []
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
