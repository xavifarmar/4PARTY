<?php 
require 'conexion.php';
session_start();

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];  // Obtener el ID de usuario de la sesión
} else {
    echo json_encode(["status" => "error", "message" => "No session active"]);
    exit();
}

// Determinar si la solicitud es GET o POST
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    getLikes($user_id);  // Llamar a la función para obtener los likes
} else if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Comprobar qué acción se debe tomar: "add" o "remove"
    if (isset($_POST['action'])) {
        $action = $_POST['action'];

        if ($action == 'addLike') {
            addLike($user_id);  // Llamar a la función para agregar un like
        } else if ($action == 'removeLike') {
            removeLike($user_id);  // Llamar a la función para eliminar un like
        } else {
            echo json_encode(["status" => "error", "message" => "Acción no válida"]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "Acción no especificada"]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Método de solicitud no soportado"]);
}
    



function addLike($user_id){

    $product_name = $_POST['product_name'];

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
            $sql = "INSERT INTO product_likes (user_id, product_id, is_liked, created_at) VALUES (?, ?, 1, NOW())";
            $stmt = $conn->prepare($sql); 
            if ($stmt === false){
                echo json_encode(["status" => "error", "message" => "Error al preparar la consulta"]);
                exit();
            }

            $liked = true;  // El like es verdadero
            $stmt->bind_param("ii", $user_id, $product_id);  // Corregir bind_param

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

function removeLike($user_id){
    $product_name = $_POST['product_name'];  // Obtener el nombre del producto desde la solicitud

    global $conn;

    // Obtener el ID del producto basado en su nombre
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

            // Eliminar el like de la base de datos
            $sql = "DELETE FROM product_likes WHERE user_id = ? AND product_id = ?";
            $stmt = $conn->prepare($sql);

            if ($stmt === false){
                echo json_encode(["status" => "error", "message" => "Error al preparar la consulta"]);
                exit();
            }

            // Bind de los parámetros para eliminar el like
            $stmt->bind_param("ii", $user_id, $product_id);

            if ($stmt->execute()){
                echo json_encode(["message" => "Like eliminado"]);
            } else {
                echo json_encode(["status" => "error", "message" => "Error al eliminar el like"]);
            }
        } else {
            echo json_encode(["status" => "error", "message" => "Producto no encontrado"]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "Falló la consulta a la base de datos"]);
    }
}

function getLikes($user_id){
    global $conn;

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
            echo json_encode(["status" => "success", "likes_items" => $likesItems]);  // Corregir la sintaxis
        } else {
            echo json_encode(["status" => "error", "message" => "No tienes productos en favoritos"]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "Falló la ejecución de la consulta"]);
    }
}
?>
