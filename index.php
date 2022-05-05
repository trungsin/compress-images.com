<?php
require __DIR__ . '/vendor/autoload.php';

$credential = new Slince\Shopify\PublicAppCredential('Access Token');
// Or Private App
$credential = new Slince\Shopify\PrivateAppCredential('API KEY', 'PASSWORD', 'SHARED SECRET');

$client = new Slince\Shopify\Client('your-store.myshopify.com', $credential, [
    'meta_cache_dir' => './tmp' // Metadata cache dir, required
]);
?>
