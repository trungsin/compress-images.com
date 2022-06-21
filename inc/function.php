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
function createProduct($productID_, $title_, $created_at_, $updated_at_){
    global $conn;
    $sql = "INSERT INTO `Products`(`productID`,`title`,`created_at`,`updated_at`) VALUES(:productID,:title,:created_at,:updated_at)";
    $result = $conn->prepare($sql); 

    return $result->execute(array(':productID'=>$productID_,':title'=>$title_,':created_at'=>$created_at_,':updated_at'=>$updated_at_));
}
function updateProduct($productID_, $title_,$updated_at_){
    global $conn;
    $sql = "UPDATE `Products` SET `title`=:title, `updated_at`=:updated_at WHERE `productID`= :productID";
    $result = $conn->prepare($sql); 

    return $result->execute(array(':productID' => $productID_,':title'=>$title_,':updated_at'=>$updated_at_));
}

function createImage($productID_,$originalfile_, $optimalfile_, $alttitle_, $imageID_){
    global $conn;
    $sql = "INSERT INTO `product_images`(`productID`,`originalfile`,`optimalfile`,`alttitle`,`imageID`) VALUES(:productID,:originalfile,:optimalfile,:alttitle,:imageID)";
    $result = $conn->prepare($sql); 

    return $result->execute(array(':productID' => $productID_,':originalfile' =>$originalfile_, ':optimalfile'=>$optimalfile_,':alttitle'=>$alttitle_,':imageID'=>$imageID_));
}
function Optimaze($tini)
{
    global $localApi;
    $curl = curl_init();
    $url = "http://".$localApi."/optimze_tini";
    if($tini)
        $url = "http://".$localApi."/optimze";

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