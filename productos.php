<?php 
require 'conexion.php';

$sql1 = "SELECT products.id, products.name, products.price product_images.image_url FROM products
INNER JOIN product_images ON (products.id = product_images.product_id) ";

$sql="  SELECT p.name, p.price, pi.image_url, pi.is_primary 
        FROM products p 
        INNER JOIN product_images pi ON p.id = pi.product_id
        GROUP BY p.name ORDER BY p.id";
        

$stmt = $conn->prepare($sql);

if ($stmt == false){
    echo "Error al preparar laconsulta";
}

if ($stmt->execute()){

  $result = $stmt->get_result();

   if ($result->num_rows > 0) {
            $resultados = [];

                while ($fila = $result->fetch_assoc()) {
                $resultados[] = $fila;
                }
    }   else {
            echo "0 resultados";
    }
}

//Devolver el resultado en JSON
 echo json_encode($resultados);

//Cerrar conexion
$stmt->close();
$conn->close();

function getColours(){
    $sql = "SELECT p.name, p.price, pi.image_url, pi.is_primary, pi.color 
    FROM products p 
    INNER JOIN product_images pi ON p.id = pi.product_id 
    WHERE p.name = ?";

// Preparar la consulta
$stmt = $conn->prepare($sql);

if ($stmt === false) {
echo json_encode(["error" => "Error al preparar la consulta"]);
exit;
}

// Vincular parámetros (el nombre del producto)
$stmt->bind_param("s", $product_name);

// Ejecutar la consulta
if ($stmt->execute()) {
$result = $stmt->get_result();

// Verificar si se encontraron resultados
if ($result->num_rows > 0) {
    $resultados = [];
    
    while ($fila = $result->fetch_assoc()) {
        $resultados[] = $fila;
    }
    
    // Devolver los resultados en formato JSON
    echo json_encode($resultados);
} else {
    echo json_encode(["message" => "No se encontraron variaciones para este producto"]);
}
} else {
echo json_encode(["error" => "Error al ejecutar la consulta"]);
}

// Cerrar la consulta y la conexión
$stmt->close();
$conn->close();
}






?>

