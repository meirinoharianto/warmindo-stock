<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
    <title>Print Struk</title>

    <style>
        @media print {
            @page {
                size: 58mm auto;
                margin: 0;
            }

            body {
                width: 58mm;
                margin: 0;
                padding: 3mm;
                font-size: 10px;
                font-family: monospace;
            }
        }

        body {
            font-family: monospace;
            font-size: 10px;
            margin: 0;
        }

        /* TABLE */
        table {
            width: 100%;
            border-collapse: collapse;
        }

        /* JARAK BARIS DIPERKECIL */
        td {
            padding: 2px 0;
            vertical-align: top;
        }

        /* ALIGNMENT */
        .right {
            text-align: right;
        }

        .center {
            text-align: center;
        }

        /* GARIS PEMBATAS */
        .line {
            border-top: 1px dashed #000;
            margin: 4px 0;
        }

        /* JUDUL TOKO */
        .title {
            font-size: 12px;
            font-weight: bold;
        }

        /* SPASI */
        .mt-1 {
            margin-top: 3px;
        }

        .mt-2 {
            margin-top: 6px;
        }
    </style>

    <!-- <script>window.print();</script> -->
</head>

<body class="receipt">
    <section>
        <br />

        <center>
            <img src="<?= base_url('assets/image/' . $pp->driver); ?>" alt="Logo" style="width:100px;">
            <!-- <h3><b> <?= base_url('assets/image/' . $pp->driver); ?> </b></h3> -->
            <div class="title"><?= $pp->nama_toko; ?></div>
            <div><?= $pp->alamat_toko; ?></div>
        </center>
        <div class="line"></div>

        <table>
            <tr>
                <td>No</td>
                <td>: <?= $t->no_bon; ?></td>
            </tr>
            <tr>
                <td>Nama</td>
                <td>: <?= $t->atas_nama; ?></td>
            </tr>
            <tr>
                <td>Status</td>
                <td>: <?= $t->status; ?></td>
            </tr>
            <tr>
                <td>Shift</td>
                <td>: <?= $t->shift_id; ?></td>
            </tr>
            <tr>
                <td>Tanggal</td>
                <td>: <?= $t->created_at; ?></td>
            </tr>
        </table>

        <div class="line"></div>

        <table>
            <?php $hr = 0;
            foreach ($tp as $r) { ?>
                <tr>
                    <td colspan="2"><b><?= $r->nama_menu; ?></b></td>
                </tr>
                <tr>
                    <td><?= $r->qty; ?> x <?= number_format($r->harga_jual); ?></td>
                    <td class="right">Rp<?= number_format($r->qty * $r->harga_jual); ?></td>
                </tr>
            <?php $hr += $r->harga_jual * $r->qty;
            } ?>
        </table>

        <div class="line"></div>

        <table>
            <tr>
                <td>Total</td>
                <td class="right">Rp<?= number_format($hr); ?></td>
            </tr>

            <?php if ($pp->diskon > 0) {
                $RPdiskon = $hr * $t->diskon / 100; ?>
                <tr>
                    <td>Diskon</td>
                    <!-- <td class="right">- Rp<?= number_format($diskon); ?></td> -->
                    <td class="right"><?= $t->diskon; ?> % / Rp<?= $RPdiskon; ?></td>
                </tr>
            <?php } ?>

            <?php if ($pp->pajak > 0) {
                $pajak = $hr * $t->pajak / 100; ?>
                <tr>
                    <td>Pajak</td>
                    <td class="right">+ Rp<?= number_format($pajak); ?></td>
                </tr>
            <?php } ?>
            <?php

            $diskon =  $hr * $t->diskon / 100;
            $pajak =  $hr * $t->pajak / 100;
            $grd = ($hr - $t->voucher - $diskon) + $pajak;
            ?>
            <tr>
                <td><b>Grand Total</b></td>
                <td class="right"><b>Rp<?= number_format($grd); ?></b></td>
            </tr>

            <tr>
                <td>Dibayar</td>
                <td class="right">Rp<?= number_format($t->dibayar); ?></td>
            </tr>

            <tr>
                <td>Kembali</td>
                <td class="right">Rp<?= number_format($t->dibayar - $grd); ?></td>
            </tr>
        </table>


        <div class="line"></div>

        <center>
            <?= $pp->footer_struk; ?><br>
            <?= $t->created_at; ?>
        </center>

        <br><br>
        <br>
        <br>
        <br>
        <br>
        <br>
        <br>
    </section>
</body>

</html>