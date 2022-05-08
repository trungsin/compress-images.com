<html>
    <head>
    <title> Optimze image for Store Shopify </title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
</head>
<body>
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
     'limit' => 50,
    // 'created_at_min' => '2015-04-25T16:15:47-04:00'
]);
// $pagination is instance of `Slince\Shopify\Common\CursorBasedPagination`

$pagination->current(); //current page
//print_r($currentProducts);
$i=1;
$servername = $_ENV["MYSQLSERVER"];
$username = $_ENV["MYSQLUSER"];
$password = $_ENV["MYSQLPASS"];
$db = $_ENV["MYSQLDB"];

// Create connection
$conn = new mysqli($servername, $username, $password,$db);

// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}
echo "Connected successfully";
$products = $pagination->current();
function checkProductExist($productID_){
    global $conn;
    $sql = "SELECT * FROM `Products` WHERE `ProductID`='$productID_'";
    $result = $conn->query($sql); 
    if ($result->num_rows > 0)       
        return true;
    return false;
}
function checkImageExist($imageID_){
    global $conn;
    $sql = "SELECT * FROM `product_images` WHERE `imageID`='$imageID_'";
    $result = $conn->query($sql); 
    if ($result->num_rows > 0)       
        return true;
    return false;
}
function createProduct($productID_, $title_){
    global $conn;
    $sql = "INSERT INTO `Products`(`productID`,`title`) VALUES('$productID_','$title_')";
    $result = $conn->query($sql); 

    return $result;
}
function createImage($productID_,$originalfile_, $optimalfile_, $alttitle_, $_imageID){
    global $conn;
    $sql = "INSERT INTO `Products`(`productID`,`title`) VALUES('$productID_','$title_')";
    $result = $conn->query($sql); 

    return $result;
}
while ($pagination->hasNext()) {
    foreach($products as $product){
        // print_r($product);
        //print_r($product->getImages());
        $productID = $product->getId();
        if(checkProductExist($productID))
            continue;
        createProduct($productID,'title');
        $images = $product->getImages();
        foreach($images as $image){
            // echo $image->getSrc()."<br>";
            $imageID = $image->getId();
            if (checkImageExist($imageID))
                continue;

            $url = $image->getSrc();
            $img = './node/originalfiles/';
            $filename = substr(basename($url),0,strpos(basename($url),"?v="));
            echo $filename;
            createImage($productID, basename($url), $filename, $image->getAlt(), $imageID);
            file_put_contents($img.$filename, file_get_contents($url));
        }
         echo "----<br>";
     
     }
    echo ($i++)."----------<br>";
    $products = $pagination->next();
}
echo $client->getProductManager()->count();
$productCount =$client->getProductManager()->count();


?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js" integrity="sha384-7+zCNj/IqJ95wo16oMtfsKbZ9ccEh31eOz1HGyDuCQ6wgnyJNSYdrPa03rtR1zdB" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js" integrity="sha384-QJHtvGhmr9XOIpI6YVutG+2QOK9T+ZnN4kzFN1RtK3zEFEIsxhlmWl5/YESvpZ13" crossorigin="anonymous"></script>
</body>
</html>
