<?php
require 'conexion.php';

// Suponiendo que el nombre del producto es enviado en una variable 'product_name' en la consulta
$product_name = $_GET['product_name'];  // O usa el método adecuado para obtener el parámetro de la URL

$sql = "SELECT p.name, p.price, pi.image_url, pi.is_primary, p.color_id
        FROM products p 
        INNER JOIN product_images pi ON p.id = pi.product_id
        WHERE p.name = ?";  // Usamos ? para un parámetro de consulta segura

$stmt = $conn->prepare($sql);

// Vinculamos el parámetro
$stmt->bind_param("s", $product_name);  // "s" es para String (nombre del producto)

$stmt->execute();

$result = $stmt->get_result();

// Verificar si se han encontrado productos
if ($result->num_rows > 0) {
    $product = null;
    $variations = [];  // Inicializamos el array de variaciones

    while ($fila = $result->fetch_assoc()) {
        if ($fila['is_primary'] == 1) {
            // Almacenamos el producto principal
            $product = [
                'name' => $fila['name'],
                'price' => $fila['price'],
                'image_url' => $fila['image_url']
            ];
        } else {
            // Almacenamos las variaciones
            $variations[] = [
                'color' => $fila['color_id'],
                'image_url' => $fila['image_url']
            ];
        }
    }

    // Devolvemos el producto y las variaciones en formato JSON
    echo json_encode(['product' => $product, 'variations' => $variations]);
} else {
    echo json_encode(["message" => "No se encontraron variaciones para este producto"]);
}

// Cerrar la consulta y la conexión
$stmt->close();
$conn->close();
?>
