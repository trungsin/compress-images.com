<?php
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);
require __DIR__ . '/vendor/autoload.php';

//$credential = new Slince\Shopify\PublicAppCredential('Access Token');
// Or Private App
$credential = new Slince\Shopify\PrivateAppCredential('6a6af91b3f4ad4b566e07198fefa500f', '7acede28590c06cf00fb666ab876fdb8', '617f6659065b53e31eacb54a6686fd5e');

$client = new Slince\Shopify\Client('testmagicexhalation.myshopify.com', $credential, [
    'meta_cache_dir' => './tmp' // Metadata cache dir, required
]);
?>
