<?php 
require 'conexion.php';
<<<<<<< HEAD
start_session();

=======
>>>>>>> 64f54610e02c88de0e3dd10206c81de64a5eb491

$sql = "SELECT pl.product_id, pi.image_url, p.name
            FROM product_likes pl
            JOIN products p ON pl.product_id = p.id
            JOIN product_images pi ON pi.product_id = pl.product_id
            WHERE pl.user_id = ?";

    $stmt = $conn->prepare($sql);

    if ($stmt === false){
        echo json_encode(["status" => "error", "message" => "Error en la conexión"]);
        exit();
    }

    $stmt->bind_param("i", $user_id);  // Corregir bind_param

    // Crear lista de Likes
    
    if ($stmt->execute()){
        $result = $stmt->get_result();

        if($result->num_rows > 0){

            $likesItems = [];

            while ($row = $result->fetch_assoc()){
                $likesItems[] = [
                    'name' => $row['name'],
                    'image_url' => $row['image_url']
                ];
            }
<<<<<<< HEAD
            echo json_encode(array_values($likesItems));  // Corregir la sintaxis
=======
            echo json_encode(["status" => "success", "likes_items" => $likesItems]);  // Corregir la sintaxis
>>>>>>> 64f54610e02c88de0e3dd10206c81de64a5eb491
        } else {
            echo json_encode(["status" => "error", "message" => "No tienes productos en favoritos"]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "Falló la ejecución de la consulta"]);
    }

?>