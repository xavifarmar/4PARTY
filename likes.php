<?php 
require 'conexion.php';
session_start();

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];  // Obtener el ID de usuario de la sesión
} else {
    echo json_encode(["status" => "error", "message" => "No session active"]);
    exit();
}
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    getLikes();  // Llamar a la función para obtener los likes
} else {
    echo "Error en el metodo de solicitud";
}
/*if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Verificar si el parámetro product_name está en la solicitud
    if (isset($_POST['product_name'])) {
        $product_name = $_POST['product_name'];  // Recibir el nombre del producto
        // Aquí puedes realizar las operaciones con el nombre del producto
        addLike($product_name);  // Llamar a la función addLike
    } else {
        echo "Error: No se ha enviado el nombre del producto.";
    }
} else {
    getLikes();
}*/



function addLike($product_name){
    global $conn;  // Usar la variable de conexión global

    $sql = "SELECT id FROM products WHERE name = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt === false){
        echo json_encode(["status" => "error", "message" => "Error en la conexión"]);
        exit();
    }

    $stmt->bind_param('s', $product_name);  // Corregir bind_param
    if ($stmt->execute()){
        $result = $stmt->get_result();
        if ($result->num_rows > 0){
            $product = $result->fetch_assoc();
            $product_id = $product['id'];

            // Insertar el like en la base de datos
            $sql = "INSERT INTO product_likes (user_id, product_id, liked, created_at) VALUES (?, ?, ?, NOW())";
            $stmt = $conn->prepare($sql); 
            if ($stmt === false){
                echo json_encode(["status" => "error", "message" => "Error al preparar la consulta"]);
                exit();
            }

            $liked = true;  // El like es verdadero
            $stmt->bind_param("iii", $user_id, $product_id, $liked);  // Corregir bind_param

            if ($stmt->execute()){
                echo json_encode(["message" => "Producto agregado a favoritos"]);
            } else {
                echo json_encode(["status" => "error", "message" => "Error al agregar el producto a favoritos"]);
            }
        } else {
            echo json_encode(["status" => "error", "message" => "Producto no encontrado"]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "Falló la consulta a la base de datos"]);
    }
}

function removeLike(){
    global $conn;

    // Eliminar el like de la base de datos
    $sql = "DELETE FROM product_likes WHERE user_id = ? AND product_id = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt === false){
        echo json_encode(["status" => "error", "message" => "Error en la conexión"]);
        exit();
    }

    $stmt->bind_param("ii", $user_id, $product_id);  // Corregir bind_param

    if ($stmt->execute()){
        echo json_encode(["message" => "Like eliminado"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Error al eliminar el like"]);
    }
}

function getLikes(){
    global $conn;

    $sql = "SELECT pl.product_id, pi.image_url, p.name
            FROM product_likes pl
            JOIN products p ON pl.product_id = p.id
            JOIN product_images pi ON pi.product_id = pl.product_id
            WHERE pl.user_id = 3";

    $stmt = $conn->prepare($sql);

    if ($stmt === false){
        echo json_encode(["status" => "error", "message" => "Error en la conexión"]);
        exit();
    }

    //$stmt->bind_param("i", 3);  // Corregir bind_param

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
            echo json_encode(["status" => "success", "likes_items" => $likesItems]);  // Corregir la sintaxis
        } else {
            echo json_encode(["status" => "error", "message" => "No tienes productos en favoritos"]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "Falló la ejecución de la consulta"]);
    }
}
?>
