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

// Verificar si se ha pasado 'gender_id' en los parámetros GET
if (isset($_GET['gender_id'])) {
    $gender_id = $_GET['gender_id'];  // Obtener el 'gender_id' pasado desde la app
} else {
    echo json_encode(["status" => "error", "message" => "Gender ID not provided"]);
    exit();
}

// Realizar la consulta de productos filtrados por género
$sql = "SELECT p.name, p.price, pi.image_url, pi.is_primary, p.color_id, IFNULL(li.is_liked, 0) AS liked 
        FROM products p 
        INNER JOIN product_images pi ON p.id = pi.product_id
        LEFT JOIN product_likes li ON li.product_id = p.id AND li.user_id = ?
        WHERE pi.is_primary = 1 AND p.gender_id = ?
        GROUP BY p.name 
        ORDER BY p.id";

// Preparar la consulta
$stmt = $conn->prepare($sql);

if ($stmt === false) {
    echo json_encode(["status" => "error", "message" => "Error preparing the query"]);
    exit();
}

// Vincular los parámetros (user_id y gender_id)
$stmt->bind_param("ii", $user_id, $gender_id);  // Vincular los dos parámetros: uno para el user_id y otro para el gender_id

// Ejecutar la consulta
if ($stmt->execute()) {
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $resultados = [];
        
        while ($fila = $result->fetch_assoc()) {
            $resultados[] = $fila;
        }

        // Devolver los resultados en formato JSON
        echo json_encode($resultados);
    } else {
        echo json_encode(["status" => "error", "message" => "No products found"]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Error executing query"]);
}

// Cerrar conexión
$stmt->close();
$conn->close();
?>
