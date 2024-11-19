<?php
require 'get_Products.php'; 

$host = 'localhost';
$dbname = 'test';
$user = 'postgres';
$password = '1111';

$products = new CProducts($host, $dbname, $user, $password);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $productId = intval($_POST['product_id']);
    $quantity = intval($_POST['quantity']);
    
    $result = $products->updateProductQuantity($productId, $quantity);
    
    echo json_encode(['success' => $result]);
}
?>