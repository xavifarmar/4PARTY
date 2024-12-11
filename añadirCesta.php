<?php  
require 'conexion.php';

// Verificar qué usuario está autenticado
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
} else {
    echo "No se ha encontrado una sesión activa"; 
    exit(); // Salir si no hay sesión activa
}

function addToCart() {
    // Recibir parámetros de la solicitud GET
    $p_name = $_GET['product_name'];
    $color_id = $_GET['color_id'];
    $size = $_GET['size']; 

    // Verificar si el producto y color están disponibles
    if ($p_name && $color_id) {
        // Preparar la consulta para obtener el product_id
        $sql = "SELECT product_id FROM products WHERE name = ? AND color_id = ?";
        $stmt = $conn->prepare($sql);

        if ($stmt === false) {
            echo "Error en la consulta"; 
            return;
        }

        $stmt->bind_param("ss", $p_name, $color_id);
        
        if ($stmt->execute()) {
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                // Obtener el product_id del producto encontrado
                $product_id = $result->fetch_assoc()['product_id'];
            } else {
                echo "Producto no encontrado"; 
                return;
            }
        } else {
            echo "Error al ejecutar la consulta para obtener el producto"; 
            return;
        }

        // Preparar la consulta para insertar en shopping_cart_items
        $sql = "INSERT INTO shopping_cart_items (user_id, product_id, quantity, added_at) VALUES (?, ?, ?, NOW())";
        $stmt = $conn->prepare($sql);

        if ($stmt === false) {
            echo "Error en la preparación de la consulta para insertar en el carrito";
            return;
        }

        // Ejecutar la inserción con los parámetros recibidos
        $stmt->bind_param("iis", $user_id, $product_id, $size);  // 'iis' para int, int, string

        if ($stmt->execute()) {
            echo "Producto añadido al carrito";
        } else {
            echo "Error al añadir el producto al carrito.";
        }
    } else {
        echo "No se ha encontrado el producto"; 
    }
}

// Llamar a la función para agregar al carrito
addToCart();

/*function get_carrito(){


    "SELECT c.product_id, c.quantity, c.user_id, p.name, p.price, p.color_id, 
    FROM shopping_cart_items INNER JOIN products p ON c.product_id = p.product_id
    WHERE p.user_id = ? OR p.name = ? "





    //Coger de product_images la imagen tambien.

}*/
    


?>