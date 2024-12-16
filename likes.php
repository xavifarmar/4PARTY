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

    //Insertar el like en la base de datos
    $sql = "INSERT INTO product_likes (user_id, product_id, liked, created_at,) VALUES (?, ?, ?, NOW)";


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