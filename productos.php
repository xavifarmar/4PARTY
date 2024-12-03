<?php 
require 'conexion.php';

$sql1 = "SELECT products.id, products.name, products.price product_images.image_url FROM products
INNER JOIN product_images ON (products.id = product_images.product_id) ";

$sql="  SELECT p.name, p.price, pi.image_url, pi.is_primary 
        FROM products p 
        INNER JOIN product_images pi ON p.id = pi.product_id
        ORDER BY p.id";

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
?>

