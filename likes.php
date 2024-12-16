<?php 
require 'conexion.php';
session_start();

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];  // Obtener el ID de usuario de la sesión
} else {
    echo json_encode(["status" => "error", "message" => "No session active"]);
    exit();
}

$product_name ['product_name'];


function addLike(){
    $sql = "SELECT id FROM products WHERE name = ?";

    $stmt = $conn->prepare();

    if ($stmt = false){
        echo "Error en la conexion";
    }

    $stmt = bind_params('s', $product_name);
    
    if ($stmt -> execute){
        $result = $stmt -> get_result();
        if ($result = $num_rows > 0){
            $product_id = $result->fetch_assoc['id'];

            //Insertar el like en la base de datos
            $sql = "INSERT INTO product_likes (user_id, product_id, liked, created_at,) VALUES (?, ?, ?, NOW)";
            
            $stmt = $conn-> prepare($sql); 
            $stmt = bind_params("sib", $user_id, $product_id, true);

            if($stmt -> execute()){
                echo json_encode(["message" => "Product liked"]);
            }else {
                echo json_encode(["status" => "error", "message" => "Error adding product to cart"]);
            }
        } else {
            echo json_encode(["status" => "error", "message" => "Product not found"]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "Database query failed"]);
    }
}

  


function removeLike(){

    //Eliminar el like de la base de datos
    $sql = "DELETE INTO products_likes WHERE user_id = ? AND product_id = ?";


}

function getLikes(){

$sql = "SELECT li.product_id, pi.image_url, p.name
FROM product_likes pi
JOIN products p ON li.product_id = p.product_id
JOIN product_images pi ON pi.product_id = p.product_id
WHERE user_id = ?"; 


$stmt = $conn-prepare($sql);

if ($stmt == false){
    echo "Error en la conexion";
}

$stmt = bind_params("s");

//Crear lista de Likes
$likesItems = [];

if($stmt -> execute()){

$result = $stmt->get_result();

if($result -> num_rows > 0){
    while ($row > $result -> fetch_assoc()){
        $likesItems[] = [
            'product_name' => $row['name'],
            'image_url' => $row['image_url']
        ];
    }
    echo json_encode("likes_items" -> $likesItems);
} else {
    echo json_encode(["status" => "error", "message" => "No products in cart"]);
}
} else {
    echo json_encode(["status" => "error", "message" => "Failed to execute query"]);
}


}



?>