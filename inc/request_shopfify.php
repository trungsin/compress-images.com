<?php
 $products = $pagination->current();
    while ($pagination->hasNext()) {
        foreach($products as $product){
            // print_r($product);
            //print_r($product->getImages());
            $productID = $product->getId();
            if(checkProductExist($productID))
                updateProduct($productID,$product->getTitle(),date("Y-m-d H:i:s"));
            else 
                createProduct($productID,$product->getTitle(),date("Y-m-d H:i:s"),date("Y-m-d H:i:s"));
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
    //
?>