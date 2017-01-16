<?php
// ini_set('display_errors', 'On');
// error_reporting(E_ALL);
require_once 'searchClass.php';

if (isset($_GET['ASIN'])) {
  $searchASIN = htmlspecialchars($_GET['ASIN']);
  $search = new Product();
  $url = $search->generateURL($searchASIN);
  $result = $search->getURL($url);
  echo json_encode($result);
  exit;
} else {
  throw new Exception('No GET request with ASIN');
}
?>
