<?php
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
ini_set('max_execution_time', '1800'); //300 seconds = 5 minutes

error_reporting(E_ALL);
require __DIR__ . '/vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();
$servername = $_ENV["MYSQLSERVER"];
$username = $_ENV["MYSQLUSER"];
$password = $_ENV["MYSQLPASS"];
$db = $_ENV["MYSQLDB"];
$localApi = $_ENV["LOCALAPI"];

// Create connection
$conn = new mysqli($servername, $username, $password,$db);

// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}
$image = $_GET['image'];
$imageID = $_GET['ID'];
$curl = curl_init();
$url = "http://".$localApi."/optimze/".$image;

// Optional Authentication:
//    curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
//    curl_setopt($curl, CURLOPT_USERPWD, "username:password");

curl_setopt($curl, CURLOPT_URL, $url);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

$result = json_decode(curl_exec($curl));
print_r($result);
$sql = "UPDATE `product_images` SET `optimalfile` = '".basename($result['path_out_new'])."', `originalsize` = ".$result['size_in'].", `optimalsize`=".$result['size_output'].", `percent`='".$result['percent']."%', `timeoptimal`=1 WHERE imageID='".$imageID."'";
$conn->query($sql); 

curl_close($curl);

header('Content-Type: application/json; charset=utf-8');
echo json_encode($result);
//die;
?>
