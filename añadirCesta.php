<?php 
require 'conexion.php';


function addToCart(){

    $p_name = $_GET['product_name'];
    $color_id = $_GET['color_id'];
    $size = $_GET['size']; 

    if($p_name, $color_id){

        "SELECT product_id FROM products WHERE name = ?";
    } 
    else{
        echo "No se ha encontrado el producto"
    }

    //Ver que usuario es. 
    if (isset($_SESSION['user_id'])){
    $user_id = $SESSION['user_id']; 
    } else {
    echo "No se ha encontrado una session activa"; 
    }

     
    "INSERT INTO shopping_cart_items (user_id, product_id, quantity, added_at) VALUES (?, ?,  ?, NOW())"
    
    ($user_id, $product_id, $size)




}

function get_carrito(){


    "SELECT c.product_id, c.quantity, c.user_id, p.name, p.price, p.color_id, 
    FROM shopping_cart_items INNER JOIN products p ON c.product_id = p.product_id
    WHERE p.user_id = ? OR p.name = ? "





    //Coger de product_images la imagen tambien.

}
    


?>