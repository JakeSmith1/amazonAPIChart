<?php
class Product {
  function generateURL($ASIN) {
    //load config vars
    $configVars = include('config.php');

    $aws_access_key_id = $configVars['Access Key'];
    $aws_secret_key = $configVars['Secret Access Code'];
    $aws_associate_tag = $configVars['AssociateTag'];
    $endpoint = "webservices.amazon.com";
    $uri = "/onca/xml";
    //parameters for the request url
    $params = array(
      "Service" => "AWSECommerceService",
      "Operation" => "ItemLookup",
      "AWSAccessKeyId" => $aws_access_key_id,
      "AssociateTag" => $aws_associate_tag,
      "ItemId" => $ASIN,
      "IdType" => "ASIN",
      "ResponseGroup" => "ItemAttributes"
    );
    // Set current timestamp if not set
    if(!isset($params["Timestamp"])) {
      $params["Timestamp"] = gmdate('Y-m-d\TH:i:s\Z');
    }
    // Sort the parameters by key (must be in alphabetical order to generate the hashed signature)
    ksort($params);
    //urlencode the parameters
    $pairs = array();
    foreach ($params as $key => $value) {
      array_push($pairs, rawurlencode($key)."=".rawurlencode($value));
    }
    // Generate the query string
    $query_string = join("&", $pairs);
    // Generate the string to be signed
    $string_to_sign = "GET\n".$endpoint."\n".$uri."\n".$query_string;
    // Generate the signature required by the Product Advertising API
    $signature = base64_encode(hash_hmac("sha256", $string_to_sign, $aws_secret_key, true));
    // Generate the signed URL
    $request_url = 'http://'.$endpoint.$uri.'?'.$query_string.'&Signature='.rawurlencode($signature);
    return $request_url;
  }

  function getURL($url) {
    //send a get request to the request_url, store response, then parse the xml to return an object
    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($curl);
    $parsed_xml = simplexml_load_string($response);
    // // Close request
    curl_close($curl);

    //return values for option to insert into MySQL databse
    $ASIN = $parsed_xml->Items->Item->ASIN;
    $MPN = $parsed_xml->Items->Item->ItemAttributes->MPN;
    $Price = $parsed_xml->Items->Item->ItemAttributes->ListPrice->FormattedPrice;
    $Name = $parsed_xml->Items->Item->ItemAttributes->Title;
    $error = $parsed_xml->Items->Request->Errors->Error->Message;
    return array("error"=>$error,"ASIN"=>$ASIN, "MPN"=>$MPN, "Price"=>$Price, "Name"=>$Name);
  }
  function insertProduct($data) {
    //load config vars
    $configVars = include('config.php');

    //create connecction
    $mysqli = new mysqli($configVars['host'], $configVars['user'], $configVars['pass'], $configVars['db']);
    //check if connection valid
    if ($mysqli->connect_error) {
      die('Connect Error (' . $mysqli->connect_errno . ') '. $mysqli->connect_error);
    }

    //get and escapre product variables to insert into database
    $asin = $data["ASIN"];
    $asin = $mysqli->real_escape_string($asin);
    $mpn = $data["MPN"];
    $mpn = $mysqli->real_escape_string($mpn);
    $price = $data["Price"];
    $price = $mysqli->real_escape_string($price);
    $name = $data["Name"];
    $name = $mysqli->real_escape_string($name);

    $query = "INSERT INTO items (ASIN, Name, MPN, Price) VALUES ('$asin', '$name', '$mpn', '$price')";
    if($result = $mysqli->query($query)) {
      if($result == 1) {
        echo 'row inserted';
      }
    } else {
      echo "insertion failed";
    }
    //close db connection
    $mysqli->close();
  }
  function getProducts() {
    //load config vars
    $configVars = include('config.php');

    $mysqli = new mysqli($configVars['host'], $configVars['user'], $configVars['pass'], $configVars['db']);

    //check if connection valid
    if ($mysqli->connect_error) {
      die('Connect Error (' . $mysqli->connect_errno . ') '. $mysqli->connect_error);
    }

    $query = "SELECT * FROM items";
    if ($result = $mysqli->query($query)) {
      $json = $result->fetch_all(1);
      echo json_encode($json);
    }
  }
}

?>
