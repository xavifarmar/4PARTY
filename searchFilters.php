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


    $sql="  SELECT p.name, p.price, pi.image_url, pi.is_primary, p.color_id, IFNULL(li.is_liked, 0) AS liked 
            FROM products p 
            INNER JOIN product_images pi ON p.id = pi.product_id
            LEFT JOIN product_likes li ON li.product_id = p.id AND li.user_id = ?
            WHERE pi.is_primary = 1 AND p.gender_id = 1
            GROUP BY p.name ORDER BY p.id" ;
            
    
    $stmt = $conn->prepare($sql);
    
    if ($stmt == false){
        echo "Error al preparar la consulta";
    }
    $stmt -> bind_param("i", $user_id);
    
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