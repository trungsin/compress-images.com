<?php
 $orders = $pagination->current();
    while ($pagination->hasNext()) {
        foreach($orders as $order){
            print_r($order);
             echo "----<br>";
         }
        echo ($i++)."----------<br>";
        $products = $pagination->next();
    }
    echo $client->getOrderManager()->count();
    //
?>