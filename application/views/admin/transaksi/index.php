<?php
// require __DIR__ . '/autoload.php';

// use Mike42\Escpos\Printer;
// use Mike42\Escpos\PrintConnectors\DummyPrintConnector;
// use Mike42\Escpos\CapabilityProfile;

$this->load->library('escpos');

// membuat connector printer ke shared printer bernama "printer_a" (yang telah disetting sebelumnya)
$connector = new Escpos\PrintConnectors\DummyPrintConnector();

// membuat objek $printer agar dapat di lakukan fungsinya
$printer = new Escpos\Printer($connector);

// $connector = new DummyPrintConnector();
$profile = Escpos\CapabilityProfile::load("simple");
$printer = new Escpos\Printer($connector);
$printer->selectPrintMode(Escpos\Printer::MODE_DOUBLE_WIDTH);;
$printer->text("Title!\n");
$printer->selectPrintMode();
$printer->text("Item 1\n");
$printer->text("Item 2\n");
$printer->feed();
$printer->cut();
?>

<a href="rawbt:base64,<?php echo base64_encode($connector->getData());
                        ?>"> click(with Android app(base64)) </a> <br> <br>

<a href="rawbt:<?php echo ($connector->getData()); ?>"> click2(with
    Android app without base64) </a> <br> <br>

<script type="text/plain" class="language-javascript">onclick="BtPrint(document.getElementById('pre_print').innerText)"</script>

<p><button class="btn btn-green" onclick="BtPrint(document.getElementById('pre_print').innerText)">Print
        text from pre block</button></p>

I have attached the result as an image.(result.jpg)

Then I added a simple Receipt preview with html
<pre tag>

<pre id="pre_print">
<?php echo ($connector->getData()); ?>
</pre>
********* Java Script ****

<script>
    function BtPrint(prn) {
        var S = "#Intent;scheme=rawbt;";
        var P = "package=ru.a402d.rawbtprinter;end;";
        var textEncoded = encodeURI(prn);
        window.location.href = "intent:" + textEncoded + S + P;
    }
</script>