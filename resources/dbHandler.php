<?php
// ini_set('display_errors', 'On');
// error_reporting(E_ALL);
require_once 'searchClass.php';

$products = new Product();

//handle get request to retrieve all product data
if (isset($_GET['products'])) {
  $data = $products->getProducts();
  echo $data;
  exit;
}

//handle post request to insert product into database
$data = json_decode(file_get_contents("php://input"), true);

if(!$data) {
  throw new Exception('No Data in POST');
}

$products->insertProduct($data);

?>
