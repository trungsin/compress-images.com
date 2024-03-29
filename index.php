

<?php
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
ini_set('max_execution_time', '1800'); //300 seconds = 5 minutes

error_reporting(E_ALL);
require __DIR__ . '/vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();
$rootShop = "https://".$_ENV["NAMESHOP"];
$i=1;
$servername = $_ENV["MYSQLSERVER"];
$username = $_ENV["MYSQLUSER"];
$password = $_ENV["MYSQLPASS"];
$db = $_ENV["MYSQLDB"];
$localApi = $_ENV["LOCALAPI"];
$nameStore = $_ENV["NAMESTORE"];

// Create connection
$conn = new PDO("mysql:host=".$servername.";dbname=".$db, $username, $password);


include("./inc/function.php");
$func ="";
if(isset($_GET['func']))
    $func = $_GET['func'];
if($func == 'saved'){ 
    $sql = "SELECT count(*) FROM `Products`"; 
    $count = $conn->query($sql)->fetchColumn(); 

    // Initialize a Data Pagination with previous count number
    $paginationdb = new \yidas\data\Pagination([
        'totalCount' => $count,
        'pergpage' => 20,

    ]);
    include("./inc/leftbar.php");
    include("./inc/image_saved.php");
    include("./inc/footer.php");
} elseif($func == "request"){ //read data from shopify
    //$credential = new Slince\Shopify\PublicAppCredential('Access Token');
    // Or Private App
    $credential = new Slince\Shopify\PrivateAppCredential($_ENV['APIKEYSHOP'], $_ENV['PASSAPISHOP'], $_ENV['SHAREDSECRET']);
    
    $client = new Slince\Shopify\Client($_ENV['NAMESHOP'], $credential, [
        'meta_cache_dir' => './tmp/log' // Metadata cache dir, required
    ]);
    $products = $client->getProductManager()->findALL();

    //print_r($product);
    $pagination = $client->getProductManager()->paginate([
        // // filter your product
         'limit' => 250,
        // 'created_at_min' => '2015-04-25T16:15:47-04:00'
    ]);
    // $pagination is instance of `Slince\Shopify\Common\CursorBasedPagination`

    $pagination->current(); //current page
    //print_r($currentProducts);
    include("./inc/request_shopfify.php");
} elseif($func == "optimze") { //optimage image
    $result = "a";
    while($result !== "null"){
        $result= Optimaze(false);
        echo $result.'<br>';
            
    }
    
} elseif($func == "tinify") { //optimage image
    $result = "a";
    while($result !== "null"){
        $result= Optimaze(true);
        echo $result.'<br>';
            
    }
    
} elseif($func =="odertracking"){
    //$credential = new Slince\Shopify\PublicAppCredential('Access Token');
    // Or Private App
    if(isset($_GET['orderid'])){
        $orderID = $_GET['orderid'];
        $credential = new Slince\Shopify\PrivateAppCredential($_ENV['APIKEYSHOP'], $_ENV['PASSAPISHOP'], $_ENV['SHAREDSECRET']);
        
        $client = new Slince\Shopify\Client($_ENV['NAMESHOP'], $credential, [
            'meta_cache_dir' => './tmp/log' // Metadata cache dir, required
        ]);
        //$order = $client->getOrderManager()->find($orderID);
        $strOrder = '#'.$orderID;
        $query = array('name' => $strOrder, "status" => "any",);
        $orders = $client->getOrderManager()->findAll($query);//.$orderID.".1"]);
        $data = array();
        if(count($orders) > 0){
            $order = $orders[0];
            $data['email'] = $order->getEmail();
            $data['status'] = $order->getFinancialStatus();
            $data['name'] = $order->getBillingAddress()->getName();
            $data['orderStatusUrl'] = $order->getOrderStatusUrl();
            $fulfills = $order->getFulfillments();
            if(count($fulfills) > 0){
                $data['tracking'] = true;
                $data['trackingCompany'] = $fulfills[0]->getTrackingCompany();
                $data['trackingCompany'] = $fulfills[0]->getTrackingCompany();
                $data['trackingNumber'] = $fulfills[0]->getTrackingNumber();
                $data['trackingurl'] = $fulfills[0]->getTrackingUrl();
                $data['shipmentStatus'] = $fulfills[0]->getShipmentStatus();
            } else {
                $data['tracking']=false;
            }
            //print_r($order);
            // echo "--------";
            // print_r($fulfills);
            header('Content-Type: application/json; charset=utf-8');
            header("Access-Control-Allow-Origin: *"); 
            header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
            echo json_encode($data);
        
        //print_r($currentProducts);
        //include("./inc/request_shopfify.php");
        }
        
    }
} elseif($func =="tracking"){
    $chanel="";
    if(isset($_GET['chanel'])){
        $chanel = $_GET['chanel'];
    }
    //$credential = new Slince\Shopify\PublicAppCredential('Access Token');
    // Or Private App
    $credential = new Slince\Shopify\PrivateAppCredential($_ENV['APIKEYSHOP'], $_ENV['PASSAPISHOP'], $_ENV['SHAREDSECRET']);
    
    $client = new Slince\Shopify\Client($_ENV['NAMESHOP'], $credential, [
        'meta_cache_dir' => './tmp/log' // Metadata cache dir, required
    ]);
    // if($chanel != ""){
    //     $query = array("status" => "any","note"=>$chanel);    
    // } else {
    //     $query = array("status" => "any");//,'created_at_min'=>'2022-07-30','created_at_max'=>'2022-07-30');
    //     //$query = array("status" => "any");//
    // }
    $query = array("status" => "any");
    //$orders = $client->getOrderManager()->findAll($query);//.$orderID.".1"]);
    //echo count($orders);

    //print_r($product);
    $now = new DateTime();// empty argument returns the current date
    $interval = new DateInterval('P60D');//this objet represents a 7 days interval
     $min = $now->sub($interval);
echo $min->format("Y-m-d");
    $pagination = $client->getOrderManager()->paginate([
        // // filter your product
         'limit' => 100,
         //'page'=>1,
         'created_at_min' => $min->format("Y-m-d"),
    ]);
    //$pagination is instance of `Slince\Shopify\Common\CursorBasedPagination`

    //$pagination->current(); //current page
    //print_r($pagination);
    include("./inc/request_tracking_orderss.php");
} else {
     include("./inc/leftbar.php");
     $sql = "SELECT count(*) FROM `product_images`"; 
     $totalImage = $conn->query($sql)->fetchColumn(); 

     $sql = "SELECT count(*) FROM `product_images` where `apply`=1"; 
     $totalImageProccess = $conn->query($sql)->fetchColumn(); 
     
     $sql = "SELECT count(*) FROM `product_images` where `apply`=2"; 
     $totalImageSkip = $conn->query($sql)->fetchColumn(); 
     
     $percentTotalProccess = (($totalImageProccess + $totalImageSkip)/$totalImage) * 100;
     $percentSkip = ($totalImageSkip/$totalImage) * 100;
     $percentProccess = ($totalImageProccess/$totalImage) * 100;
     
     $sql = "SELECT count(*) FROM `product_images` where `apply`=3 or `apply`=9"; 
     $totalImageError = $conn->query($sql)->fetchColumn(); 
     
     $sql = "SELECT sum(originalsize) as original, sum(optimalsize) as optimal FROM `product_images` where `apply`=1"; 
     $original = $conn->query($sql)->fetchColumn(); 
     $optimal = $conn->query($sql)->fetchColumn(1);
     $total = 100 - ($optimal/$original)*100;
     
     include("./inc/dashboard.php");
     include("./inc/footer.php");    
}

?>

