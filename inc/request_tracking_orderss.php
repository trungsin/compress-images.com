
<!-- Page Heading -->
<h1 class="h3 mb-2 text-gray-800">Memory</h1>
<!-- DataTales Example -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Detail Image Crarl from Memory</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
        <table class="table table-bordered" style="border-color: aqua;">
            <thead>
              <tr>
                <th scope="col" style="width: 300px;">Id Order</th>
                <th scope="col">Name </th>
                <th scope="col">Status</th>
                <th scope="col">Tracking Company</th>
                <th scope="col">Tracking Number</th>
                <th scope="col">Tracking url</th>
                <th scope="col">Shipment Status</th>
              </tr>
            </thead>
            <tbody>
         <?php
           $orders = $pagination->current();
            while ($pagination->hasNext()) {
                foreach($orders as $order){
                    echo '<tr class="table-active">';
                    echo '<th>'.$order->getName().'</th>';
                    echo '<td>'.$order->getBillingAddress()->getName().'</td>';
                    echo '<td>'.$order->getFinancialStatus().'</td>';
                    $fulfills = $order->getFulfillments();
                    if(count($fulfills) > 0){
                        echo '<td>'.$order->getTrackingCompany().'"></td>';
                        echo '<td>'.$order->getTrackingNumber().'"></td>';
                        echo '<td>'.$order->getTrackingUrl().'"></td>';
                        echo '<td>'.$order->getShipmentStatus().'"></td>';

                    }
                    else 
                        echo '<td colspan="4">Not Yet</td>';
                    echo '</tr>';
                }
                echo ($i++)."----------<br>";
                $orders = $pagination->next();
            }
         ?>
            </tbody>
          </table>
       </div>
    </div>
</div>