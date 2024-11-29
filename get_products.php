<?php
// Conexión a la base de datos
require 'conexion.php';

// Consulta para obtener todos los productos
$query = "SELECT id, name, description, price, stock, category_id, color_id, gender_id, clothing_type_id, view_count, like_count, created_at FROM products";
$result = $conn->query($query);

// Verifica si hay productos1
if ($result->num_rows > 0) {
    // Array para almacenar los productos
    $products = [];

    // Recorre los productos y los agrega al array
    while ($row = $result->fetch_assoc()) {
        $products[] = [
            'id' => $row['id'],
            'name' => $row['name'],
            'description' => $row['description'],
            'price' => $row['price'],
            'stock' => $row['stock'],
            'category_id' => $row['category_id'],
            'color_id' => $row['color_id'],
            'gender_id' => $row['gender_id'],
            'clothing_type_id' => $row['clothing_type_id'],
            'view_count' => $row['view_count'],
            'like_count' => $row['like_count'],
            'created_at' => $row['created_at']
        ];
    }

    // Retorna la respuesta en formato JSON
    echo json_encode($products);
} else {
    echo json_encode([]);
}

$conn->close();
?>
