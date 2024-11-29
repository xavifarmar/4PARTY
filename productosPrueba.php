<?php
require 'conexion.php';

// Consultar los productos
$sql = "SELECT p.id, p.name, p.description, p.price, p.stock, pi.image_url, pi.is_primary 
        FROM products p 
        LEFT JOIN product_images pi ON p.id = pi.product_id
        ORDER BY p.id";
$result = $conn->query($sql);

// Verificar si hay productos
if ($result->num_rows > 0) {
    // Empezamos la estructura HTML
    echo "<!DOCTYPE html>
    <html lang='es'>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <title>Productos</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                background-color: #f4f4f4;
                margin: 0;
                padding: 0;
            }
            .product-container {
                display: flex;
                flex-wrap: wrap;
                justify-content: center;
                gap: 20px;
                padding: 20px;
            }
            .product {
                background: white;
                border-radius: 10px;
                box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
                width: 300px;
                padding: 20px;
                text-align: center;
            }
            .product img {
                max-width: 100%;
                border-radius: 10px;
                height: auto;
            }
            .product h3 {
                color: #333;
            }
            .product p {
                color: #777;
            }
            .product .price {
                font-size: 1.2em;
                color: #007BFF;
            }
            .product .stock {
                color: #28a745;
            }
            .product .description {
                color: #555;
            }
        </style>
    </head>
    <body>
        <h1 style='text-align: center; margin-top: 20px;'>Nuestros Productos</h1>
        <div class='product-container'>";

    // Crear un array para almacenar los productos por separado y sus imágenes
    $products = [];
    
    // Recorrer los productos
    while($row = $result->fetch_assoc()) {
        $product_id = $row['id'];

        // Si el producto no está en el array, lo agregamos
        if (!isset($products[$product_id])) {
            $products[$product_id] = [
                'name' => $row['name'],
                'description' => $row['description'],
                'price' => $row['price'],
                'stock' => $row['stock'],
                'images' => []
            ];
        }

        // Si la imagen no es nula, la agregamos
        if ($row['image_url']) {
            $products[$product_id]['images'][] = [
                'url' => $row['image_url'],
                'is_primary' => $row['is_primary']
            ];
        }
    }

    // Ahora mostramos los productos con sus imágenes
    foreach ($products as $product) {
        $primary_image = null;
        
        // Seleccionamos la imagen principal (si existe)
        foreach ($product['images'] as $image) {
            if ($image['is_primary'] == 1) {
                $primary_image = $image['url'];
                break;
            }
        }

        // Si no hay imagen principal, seleccionamos la primera imagen
        if (!$primary_image && count($product['images']) > 0) {
            $primary_image = $product['images'][0]['url'];
        }

        // Mostrar producto
        echo "
        <div class='product'>
            <img src='$primary_image' alt='{$product['name']}'>
            <h3>{$product['name']}</h3>
            <p class='description'>{$product['description']}</p>
            <p class='price'>$ {$product['price']}</p>
            <p class='stock'>Stock: {$product['stock']}</p>
        </div>";
    }

    echo "</div></body></html>";

} else {
    echo "No se encontraron productos.";
}

// Cerrar la conexión
$conn->close();
?>
