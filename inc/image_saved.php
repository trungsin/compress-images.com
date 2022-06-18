<?php
if (isset($_GET['page']))
        $page = $_GET['page'];
    else 
        $page = 1; 
    // Get range data for the current page
    $sql = "SELECT * FROM `Products` LIMIT {$paginationdb->offset}, {$paginationdb->limit}"; 
    $sth = $conn->prepare($sql,array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
    $sth->setFetchMode(PDO:: FETCH_ASSOC);

    $sth->execute();
    //$sql = "SELECT * FROM `Products` LIMIT ". (($page - 1)*10).", ".($page*10);  // Retrieve rows 6-15
    //$result = $conn->query($sql);
    ?>
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
                <th scope="col" style="width: 300px;">Title Product</th>
                <th scope="col">Original file</th>
                <th scope="col">Optimal file</th>
                <th scope="col">Time Optimal</th>
                <th scope="col">Original Size</th>
                <th scope="col">Optimal Size</th>
                <th scope="col">Percent</th>
                <th scope="col" style="width: 300px;">Alt Title</th>
                <th scope="col"><button type="button" id="btnOptimze" class="btn btn-primary">Optimazing</button></th>
                <th scope="col"><button type="button" id="btnApply" class="btn btn-primary">Apply</button></th>
              </tr>
            </thead>
            <tbody>
         <?php
            if ($count > 0) {
                while ($row = $sth->fetch()) {
                    $sql1 = "SELECT * FROM `product_images` WHERE `productID` ='".$row['productID']."'";
                    $sth1 = $conn->prepare($sql1);
                    $sth1->setFetchMode(PDO:: FETCH_ASSOC);

                    $sth1->execute();
                    $numimage  = $conn->query("SELECT count(*) FROM `product_images` WHERE `productID` ='".$row['productID']."'")->fetchColumn(); 
                    if($numimage > 0){
                        $row1 = $sth1->fetch();
                        echo '<tr class="table-active">';
                        echo '<th rowspan="'.$numimage.'" scope="row"><a href="'.$rootShop.'/admin/products/'.$row['productID'].'">'.$row['title'].'</a><br>
                        <button type="button" id="btnSelectProduct" onclick="selectProduct(\''.$row['productID'].'\')" class="btn btn-primary">Choose this product</button>
                        </th>';
                        echo '<td><img style="width: 80px;"  src="./node/originalfiles/'.$row1['originalfile'].'"/></td>';
                        if($row1['optimalfile'] == '')
                            echo '<td><span id="'.$row1['imageID'].'"></td>';
                        else 
                            echo '<td><img style="width: 80px;" src="./node/optimalfile/'.$row1['optimalfile'].'"/></td>';
                        echo '<td>'.$row1['timeoptimal'].'</td>';
                        echo '<td>'.$row1['originalsize'].'</td>';
                        echo '<td>'.$row1['optimalsize'].'</td>';
                        echo '<td>'.$row1['percent'].'</td>';
                        echo '<td>'.$row1['alttitle'].'</td>';
                        if($row1['timeoptimal']>0)
                            echo '<td><input name="optimze-check-input" disabled class="optimze-check-input mt-0" type="checkbox"  value="'.$row1['imageID'].','.$row1['originalfile'].'" aria-label="Checkbox for following text input"></td>';
                        else 
                            echo '<td><input name="optimze-check-input" choose="'.$row['productID'].'" class="optimze-check-input mt-0" type="checkbox"  value="'.$row1['imageID'].','.$row1['originalfile'].'" aria-label="Checkbox for following text input"></td>';
                        if($row1['optimalfile'] == '')
                            echo '<td><input class="apply-check-input mt-0" disabled type="checkbox" aria-label="Checkbox for following text input"></td>';
                        else 
                            echo '<td><input class="apply-check-input mt-0" choose="'.$row['productID'].'" type="checkbox" value="'.$row1['imageID'].','.$row1['imageID'].','.$row1['optimalfile'].'" aria-label="Checkbox for following text input"></td>';
                        echo '</tr>';
                        while($row1 = $sth1->fetch()){
                            echo '<tr class="table-active">';
                            //echo '<th rowspan="'.$numimage.'" scope="row">'.$row['title'].'</th>';
                            echo '<td><img style="width: 80px;" src="./node/originalfiles/'.$row1['originalfile'].'"/></td>';
                            if($row1['optimalfile'] == '')
                                echo '<td><span id="'.$row1['imageID'].'"></td>';
                            else 
                                echo '<td><img style="width: 80px;" src="./node/optimalfile/'.$row1['optimalfile'].'"/></td>';
                            echo '<td>'.$row1['timeoptimal'].'</td>';
                            echo '<td>'.$row1['originalsize'].'</td>';
                            echo '<td>'.$row1['optimalsize'].'</td>';
                            echo '<td>'.$row1['percent'].'</td>';
                            echo '<td>'.$row1['alttitle'].'</td>';
                            if($row1['timeoptimal']>0)
                                echo '<td><input name="optimze-check-input" disabled  class="optimze-check-input mt-0" type="checkbox"  value="'.$row1['imageID'].','.$row1['originalfile'].'" aria-label="Checkbox for following text input"></td>';
                            else 
                                echo '<td><input name="optimze-check-input" choose="'.$row['productID'].'" class="optimze-check-input mt-0" type="checkbox"  value="'.$row1['imageID'].','.$row1['originalfile'].'" aria-label="Checkbox for following text input"></td>';
                            if($row1['optimalfile'] == '')
                                echo '<td><input class="apply-check-input mt-0" disabled type="checkbox"  aria-label="Checkbox for following text input"></td>';
                            else
                                echo '<td><input class="apply-check-input mt-0" choose="'.$row['productID'].'" type="checkbox"   value="'.$row1['productID'].','.$row1['imageID'].','.$row1['optimalfile'].'" aria-label="Checkbox for following text input"></td>';
                            echo '</tr>';
                        } //end while
                    }//end if

                }//end while
            }//end if

         ?> <tr>
            <td colspan="10">
                <?php 
                echo  \yidas\widgets\Pagination::widget([
                    'pagination' => $paginationdb,
                    'view' => 'bootstrap',
                    ]);
                ?>
            </td>
            </tr> 
            </tbody>
          </table>
       </div>
    </div>
</div>
