<div class="clearfix"></div>
<div class="footer-second" style="border-radius:0px;clear: both;">
    <div class="container">Copyright © 2023 SARESTO |
        <a href="https://www.salasatekno.com" style="color:yellow" target="_blank">
            <b>Salasa Teknologi Solusindo </b>
        </a>
    </div>
</div>
<!-- DATATABLES BS 4-->
<script>
    $(document).ready(function() {
        $('#table1').DataTable({
            responsive: true
        });
    });
</script>
<script src="<?= base_url('assets/plugins/datatables/jquery.dataTables.min.js'); ?>"></script>
<script src="<?= base_url('assets/plugins/datatables/dataTables.bootstrap4.min.js'); ?>"></script>
<script src="<?= base_url('assets/plugins/datatables/dataTables.responsive.min.js'); ?>"></script>
<script src="<?= base_url('assets/plugins/datatables/responsive.bootstrap4.min.js'); ?>"></script>
<!-- <script src="https://code.jquery.com/jquery-3.5.1.js"></script> -->
<!-- <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script> -->

<script src="https://cdn.datatables.net/buttons/2.3.6/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.print.min.js"></script>


<script type="text/javascript" />
document.getElementById("atas_nama").addEventListener("click", function(e) {
toggleFullScreen();
}, false);
document.getElementById("fullscreenId").addEventListener("click", function(e) {
toggleFullScreen();
}, false);


function toggleFullScreen() {
// alert("coba");
// if (!document.fullscreenElement) {
var elem = document.documentElement;

if (elem.requestFullscreen) {
elem.requestFullscreen();
} else if (elem.webkitRequestFullscreen) { /* Safari */
elem.webkitRequestFullscreen();
} else if (elem.msRequestFullscreen) { /* IE11 */
elem.msRequestFullscreen();
}
// } else {
// if (document.exitFullscreen) {
// document.exitFullscreen();
// }
// }
}
</script>

</body>

</html>