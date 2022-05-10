<?php
require __DIR__ . '/vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();
//$credential = new Slince\Shopify\PublicAppCredential('Access Token');
// Or Private App
$credential = new Slince\Shopify\PrivateAppCredential($_ENV['APIKEYSHOP'], $_ENV['PASSAPISHOP'], '617f6659065b53e31eacb54a6686fd5e');
$rootShop = "https://".$_ENV["NAMESHOP"];
$client = new Slince\Shopify\Client($_ENV['NAMESHOP'], $credential, [
    'meta_cache_dir' => './tmp/log' // Metadata cache dir, required
]);
$productID = $_GET['productID'];
$imageID = $_GET['imageID'];
$image = $client->getProductImageManager()->find($productID,$imageID);

//print_r($product);
$product = $client->getProductManager()->find($productID);
// $pagination is instance of `Slince\Shopify\Common\CursorBasedPagination`

//print_r($currentProducts);
$i=1;
$servername = $_ENV["MYSQLSERVER"];
$username = $_ENV["MYSQLUSER"];
$password = $_ENV["MYSQLPASS"];
$db = $_ENV["MYSQLDB"];
$localApi = $_ENV["LOCALAPI"];

// Create connection
$conn = new PDO("mysql:host=".$servername.";dbname=".$db, $username, $password);


 // Get range data for the current page
 $sql = "SELECT * FROM `product_images` where `imageID`=".$imageID; 
 $sth = $conn->prepare($sql,array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
 $sth->setFetchMode(PDO:: FETCH_ASSOC);

 $sth->execute();
 $row = $sth->fetch();
 $newimage = $client->getProductImageManager()->create($productID,array ('src' => 'http://compress-images.com/node/optimalfile/'.$row['optimalfile']));
print_r($newimage);