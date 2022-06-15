<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>SB Admin 2 - Dashboard</title>

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
//$credential = new Slince\Shopify\PublicAppCredential('Access Token');
// Or Private App
$credential = new Slince\Shopify\PrivateAppCredential($_ENV['APIKEYSHOP'], $_ENV['PASSAPISHOP'], '617f6659065b53e31eacb54a6686fd5e');
$rootShop = "https://".$_ENV["NAMESHOP"];
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
$i=1;
$servername = $_ENV["MYSQLSERVER"];
$username = $_ENV["MYSQLUSER"];
$password = $_ENV["MYSQLPASS"];
$db = $_ENV["MYSQLDB"];
$localApi = $_ENV["LOCALAPI"];

// Create connection
$conn = new PDO("mysql:host=".$servername.";dbname=".$db, $username, $password);


// Check connection
// if ($conn->connect_error) {
//   die("Connection failed: " . $conn->connect_error);
// }
//echo "Connected successfully";
// Get count of data set first
$sql = "SELECT count(*) FROM `Products`"; 
$count = $conn->query($sql)->fetchColumn(); 

// Initialize a Data Pagination with previous count number
$paginationdb = new \yidas\data\Pagination([
    'totalCount' => $count,
    'pergpage' => 20,

]);

$func = $_GET['func'];
if($func == 'saved'){ 
    include("./inc/image_saved.php");
<?php
} elseif($func == "request"){ //read data from shopify
    $products = $pagination->current();
    while ($pagination->hasNext()) {
        foreach($products as $product){
            // print_r($product);
            //print_r($product->getImages());
            $productID = $product->getId();
            if(checkProductExist($productID))
                updateProduct($productID,$product->getTitle());
            else 
                createProduct($productID,$product->getTitle());
            $images = $product->getImages();
            foreach($images as $image){
                // echo $image->getSrc()."<br>";
                $imageID = $image->getId();
                if (checkImageExist($imageID) == 1)
                    continue;
                elseif (checkImageExist($imageID)>1)
                {
                    $sql = "DELETE FROM `product_images` WHERE optimalfile ='' and `imageID`='".$imageID."'";
                    $result = $conn->prepare($sql); 
                    $result->execute();
                    continue;
                }
                $url = $image->getSrc();
                $img = './node/originalfiles/';
                $filename = substr(basename($url),0,strpos(basename($url),"?v="));
                echo $filename;
                createImage($productID, $filename, '', $image->getAlt(), $imageID);
                file_put_contents($img.$filename, file_get_contents($url));
            }
             echo "----<br>";
         
         }
        echo ($i++)."----------<br>";
        $products = $pagination->next();
    }
    echo $client->getProductManager()->count();
    //$productCount =$client->getProductManager()->count();
} elseif($func == "optimze") { //optimage image
    $image = $_GET['image'];
    print_r(Optimaze($image));
} elseif($func == "") { //apply optimzed image to product shopify

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
<?php
function checkProductExist($productID_){
    global $conn;
    $sql = "SELECT count(*) AS `total` FROM `Products` WHERE `ProductID`='$productID_'";
    $result = $conn->prepare($sql); 
    $result->execute();
    if ($result->fetchObject()->total > 0)       
        return true;
    return false;
}
function checkImageExist($imageID_){
    global $conn;
    $sql = "SELECT count(*) AS `total` FROM `product_images` WHERE `imageID`='$imageID_'";
    $result = $conn->prepare($sql); 
    $result->execute();
    return $result->fetchObject()->total;
}
function createProduct($productID_, $title_){
    global $conn;
    $sql = "INSERT INTO `Products`(`productID`,`title`) VALUES(:productID,:title)";
    $result = $conn->prepare($sql); 

    return $result->execute(array(':productID'=>$productID_,':title'=>$title_));
}
function updateProduct($productID_, $title_){
    global $conn;
    $sql = "UPDATE `Products` SET `title`=:title WHERE `productID`= :productID";
    $result = $conn->prepare($sql); 

    return $result->execute(array(':productID' => $productID_,':title'=>$title_));
}

function createImage($productID_,$originalfile_, $optimalfile_, $alttitle_, $imageID_){
    global $conn;
    $sql = "INSERT INTO `product_images`(`productID`,`originalfile`,`optimalfile`,`alttitle`,`imageID`) VALUES(:productID,:originalfile,:optimalfile,:alttitle,:imageID)";
    $result = $conn->prepare($sql); 

    return $result->execute(array(':productID' => $productID_,':originalfile' =>$originalfile_, ':optimalfile'=>$optimalfile_,':alttitle'=>$alttitle_,':imageID'=>$imageID_));
}
function Optimaze($data)
{
    global $localApi;
    $curl = curl_init();
    $url = "http://".$localApi."/optimze/".$data;

    // Optional Authentication:
//    curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
//    curl_setopt($curl, CURLOPT_USERPWD, "username:password");

    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

    $result = curl_exec($curl);

    curl_close($curl);

    return $result;
}

?>