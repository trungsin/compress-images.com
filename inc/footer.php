
</div>
            <!-- End of Main Content -->

            <!-- Footer -->
            <footer class="sticky-footer bg-white">
                <div class="container my-auto">
                    <div class="copyright text-center my-auto">
                        <span>Copyright &copy; New Way Hub 2022</span>
                    </div>
                </div>
            </footer>
            <!-- End of Footer -->

        </div>
        <!-- End of Content Wrapper -->

    </div>
    <!-- End of Page Wrapper -->

    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <!-- Logout Modal-->
    <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Ready to Leave?</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">Select "Logout" below if you are ready to end your current session.</div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                    <a class="btn btn-primary" href="login.html">Logout</a>
                </div>
            </div>
        </div>
    </div>
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