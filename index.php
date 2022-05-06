<?php
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);
require __DIR__ . '/vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();
//$credential = new Slince\Shopify\PublicAppCredential('Access Token');
// Or Private App
$credential = new Slince\Shopify\PrivateAppCredential($_ENV['APIKEYSHOP'], $_ENV['PASSAPISHOP'], '617f6659065b53e31eacb54a6686fd5e');

$client = new Slince\Shopify\Client($_ENV['NAMESHOP'], $credential, [
    'meta_cache_dir' => './tmp/log' // Metadata cache dir, required
]);
$products = $client->getProductManager()->findALL();
//print_r($product);
$pagination = $client->getProductManager()->paginate([
    // // filter your product
     'limit' => 3,
    // 'created_at_min' => '2015-04-25T16:15:47-04:00'
]);
// $pagination is instance of `Slince\Shopify\Common\CursorBasedPagination`

//$currentProducts = $pagination->current(); //current page
//print_r($pagination);
// while ($pagination->hasNext()) {
//     $currentProducts = $pagination->current();
//     print_r($currentProducts);
//     echo "----<br>";
//     $nextProducts = $pagination->next();
// }
//echo $client->getProductManager()->count();
$productCount =$client->getProductManager()->count();
foreach($products as $product){
    echo $product->id;
    echo "----<br>";

}

?>

