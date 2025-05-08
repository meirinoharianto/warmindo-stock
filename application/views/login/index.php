<!DOCTYPE html>
<!-- Coding by CodingLab | www.codinglabweb.com-->
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Saresto - Sam Ndut - salasatekno.com</title>
    <!-- CSS -->
    <!-- <link rel="stylesheet" href="style.css"> -->
    <link rel="stylesheet" href="<?= base_url('assets/plugins/bootstrap/css/bootstrap.min.css'); ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/style.css'); ?>" />
    <link rel="stylesheet" href="<?= base_url('assets/plugins/sweetalert2/sweetalert2.css'); ?>">


    <!-- Boxicons CSS -->
    <link href='https://unpkg.com/boxicons@2.1.2/css/boxicons.min.css' rel='stylesheet'>
    <script src="<?= base_url('assets/plugins/sweetalert2/sweetalert2.all.min.js'); ?>"></script>

</head>

<body>
    <section class="container forms">
        <div class="form login">
            <?php if (!empty($this->session->flashdata('failed'))) { ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                        <span class="sr-only">Close</span>
                    </button>
                    <?= $this->session->flashdata('failed'); ?>
                </div>
            <?php } ?>
            <div class="form-content">
                <header>SAM NDUT</header>
                <header>Login</header>
                <form method="POST" action="<?= base_url('login/proses'); ?>">
                    <div class="field input-field">
                        <!-- <input type="email" placeholder="Email" class="input"> -->
                        <input type="text" required class="form-control" autocomplete="off" name="user" id="user" placeholder="Masukan Username">

                    </div>
                    <div class="field input-field">
                        <!-- <input type="password" placeholder="Password" class="password"> -->
                        <input type="password" required class="form-control" autocomplete="off" name="pass" id="pass" placeholder="Masukan Password">

                    </div>

                    <div class="field button-field">
                        <button>Login</button>
                    </div>
                </form>
                <!-- <div class="form-link">
                    <span>Don't have an account? <a href="#" class="link signup-link">Signup</a></span>
                </div> -->
            </div>
            <div class="line"></div>
            <div class="media-options">
                <div class="form-link">
                    <span>Copyright &copy; 2023 SARESTO</span>
                    <span> <a href="https://www.salasatekno.com" class="link signup-link"><b>Salasa Teknologi Solusindo</b></a></span>
                    <span>2.00.00</span>
                </div>
            </div>

        </div>

    </section>
    <!-- JavaScript -->
    <script src="<?= base_url('assets/js/script.js'); ?>"></script>
</body>

</html>