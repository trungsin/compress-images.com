
<!-- Page Heading -->
<h1 class="h3 mb-2 text-gray-800"><?php echo $nameStore;?></h1>
<!-- DataTales Example -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Detail Order from <?php echo $nameStore;?></h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
        <table class="table table-bordered"  border="1" style="border-color: aqua;">
            <thead>
              <tr>
                <th scope="col" style="width: 300px;">Id Order</th>
                <th scope="col">Name </th>
                <th scope="col">Status</th>
                <th scope="col">Create Date</th>
                <th scope="col">Tracking Company</th>
                <th scope="col">Tracking Number</th>
                <th scope="col">Tracking url</th>
              </tr>
            </thead>
            <tbody>
         <?php
           $orders = $pagination->current();
            while ($pagination->hasNext()) {
                foreach($orders as $order){
                    $nameShip = "not yet";
                    if($order->getBillingAddress() != null)
                        $nameShip = $order->getBillingAddress()->getName();
                    echo '<tr class="table-active">';
                    echo '<th>'.$order->getNumber().'</th>';
                    echo '<td>'.$nameShip.'</td>';
                    echo '<td>'.$order->getFinancialStatus().'</td>';
                    echo '<td>'.$order->getCreatedAt().'</td>';
                    $fulfills = $order->getFulfillments();
                    if(count($fulfills) > 0){
                        echo '<td>'.$fulfills[0]->getTrackingCompany().'</td>';
                        echo '<td><a href="'.$fulfills[0]->getTrackingUrl().'" target="_blank">'.$fulfills[0]->getTrackingNumber().'</a></td>';
                        echo '<td>'.$fulfills[0]->getShipmentStatus().'</td>';

                    }
                    else 
                        echo '<td colspan="3">Not Yet</td>';
                    echo '</tr>';
                }
                $orders = $pagination->next();
            }
         ?>
            </tbody>
          </table>
       </div>
    </div>
</div>