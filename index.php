<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Compress Shopify NWH - Dashboard</title>

    <!-- Custom fonts for this template-->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="css/sb-admin-2.min.css" rel="stylesheet">

</head>
<body>

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
            } else {
                $data['tracking']=false;
            }
            // print_r($order[0]);
            // echo "--------";
            // print_r($fulfills);
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode($data);
        
        //print_r($currentProducts);
        //include("./inc/request_shopfify.php");
        }
        
    }
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
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js" integrity="sha384-7+zCNj/IqJ95wo16oMtfsKbZ9ccEh31eOz1HGyDuCQ6wgnyJNSYdrPa03rtR1zdB" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js" integrity="sha384-QJHtvGhmr9XOIpI6YVutG+2QOK9T+ZnN4kzFN1RtK3zEFEIsxhlmWl5/YESvpZ13" crossorigin="anonymous"></script>
<script>
    //var filename = path.basename('/Users/Refsnes/demo_path.js');

    $(document).ready(function() {

        $("#btnOptimze").click(function(){
            var selected = [];
            $('input[name="optimze-check-input"]:checked').each(function() {
                selected.push(this.value); 
                dataOpt =  this.value;
                    //console.log(dataOpt);
                imgOpts = dataOpt.split(",");
                // $.ajax({
                //     url: "compress-images.php/?image="+imgOpts[1]+"&ID="+imgOpts[0], 
                //     success: function(result){
                //         console.log("sss");
                //         console.log(result);
                //         },
                //     beforeSend: function() {
                //         $("#"+imgOpts[0]).html('<div id="loading"><img src="./images/ajax-loader.gif" alt="Loading..."/></div>');
                //     },
                // });
                $.ajax({
                    url: "compress-images.php/?image="+imgOpts[1]+"&ID="+imgOpts[0], 
                    type: 'GET',
                    beforeSend: function( xhr ) {
                        $("#"+imgOpts[0]).html('<div id="loading"><img src="./images/ajax-loader.gif" alt="Loading..."/></div>');
                    }
                    })
                    .done(function( data ) {
                        console.log("sss");
                        console.log(data);
                        const obj = JSON.parse(data);
                        var filename = obj.path_out_new.substr(obj.path_out_new.lastIndexOf("/")+1);
                        $("#"+imgOpts[0]).html('<img style="width: 80px;" src="./node/optimalfile/'+ filename +'"/>');
                    });
            });
            
            // $('input[name="optimze-check-input"]:checked').each(function() {
            //      console.log(this.value);
            // });
            // //const forEachLoop = _ => {
            //     console.log('Start')
            //     $('input[name="optimze-check-input"]:checked').each(async function() {
            //     //selected.forEach(async dataOpt => {
            //         dataOpt = await this.value;
            //         //console.log(dataOpt);
            //         imgOpts = await dataOpt.split(",");
            //         imgOpt = await imgOpts[1];
            //         //console.log(imgOpt);
            //         const resultImg = await jsOptimaze(imgOpt)
            //         console.log(resultImg)
            //     });
            //     console.log('End')
            // //}
            //console.log(forEachLoop);
           
        });
        $("#btnApply").click(function(){
            var selected = [];
            $('input[name="apply-check-input"]:checked').each(function() {
                selected.push(this.value); 
                dataOpt =  this.value;
                    //console.log(dataOpt);
                imgOpts = dataOpt.split(",");
                // $.ajax({
                //     url: "compress-images.php/?image="+imgOpts[1]+"&ID="+imgOpts[0], 
                //     success: function(result){
                //         console.log("sss");
                //         console.log(result);
                //         },
                //     beforeSend: function() {
                //         $("#"+imgOpts[0]).html('<div id="loading"><img src="./images/ajax-loader.gif" alt="Loading..."/></div>');
                //     },
                // });
                $.ajax({
                    url: "applyimage.php/?image="+imgOpts[1]+"&ID="+imgOpts[0], 
                    type: 'GET',
                    beforeSend: function( xhr ) {
                        //$("#"+imgOpts[0]).html('<div id="loading"><img src="./images/ajax-loader.gif" alt="Loading..."/></div>');
                    }
                    })
                    .done(function( data ) {
                        console.log("sss");
                        console.log(data);
                        const obj = JSON.parse(data);
                        var filename = obj.path_out_new.substr(obj.path_out_new.lastIndexOf("/")+1);
                        //$("#"+imgOpts[0]).html('<img style="width: 80px;" src="./node/optimalfile/'+ filename +'"/>');
                    });
            });
        });
        
        async function jsOptimaze(data){
            $.ajax({url: "http://compress-images.com/?func=optimze&image="+data, success: function(result){
                return result;
            }});
        }

    });
    function selectProduct(productID){
            $('input[choose="'+productID+'"]').each(function() {
                this.checked = true;
            });
        }
</script>
 <!-- Bootstrap core JavaScript-->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Core plugin JavaScript-->
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>

    <!-- Custom scripts for all pages-->
    <script src="js/sb-admin-2.min.js"></script>

    <!-- Page level plugins -->
    <script src="vendor/chart.js/Chart.min.js"></script>

    <!-- Page level custom scripts -->
    <script src="js/demo/chart-area-demo.js"></script>
    <script src="js/demo/chart-pie-demo.js"></script>

</body>

</html>
