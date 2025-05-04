<!doctype html>
<html lang="en">

<head>
    <title>Login - Saresto Sam Ndut - salasatekno.com</title>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="<?= base_url('assets/plugins/bootstrap/css/bootstrap.min.css'); ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/login.css?v=' . time()); ?>" />
</head>

<!-- <body style="background:#0c4e68;"> -->

<body style="background: linear-gradient(135deg, #ed1c24 0%, #fff200 60%, #00a651 100%);
  background-repeat: no-repeat;
  background-attachment: fixed;">
    <!-- <button id="full-screen">FULL SCREEN</button> -->
    <section class="vh-100 gradient-custom">
        <div class="container py-5 h-100">
            <!-- grid -->
            <div class="row d-flex justify-content-center align-items-center h-100">
                <div class="col-12 col-md-8 col-lg-6 col-xl-5">
                    <?php if (!empty($this->session->flashdata('failed'))) { ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                                <span class="sr-only">Close</span>
                            </button>
                            <?= $this->session->flashdata('failed'); ?>
                        </div>
                    <?php } ?>
                    <div class="card bg-dark text-white" style="border-radius: 1rem;">
                        <!-- <div class="card-header text-center">
                            <h3><b>WARMINDO SAM NDUT</b></h3>
                        </div> -->

                        <div class="card-body p-5 text-center">
                            <div class="mb-md-5 mt-md-4 pb-5">

                                <h3 class="fw-bold mb-2 text-uppercase">WARMINDO SAM NDUT</h3>
                                <h2 class="fw-bold mb-2 text-uppercase">Login</h2>
                                <p class="text-white-50 mb-5">Silahkan masukkan username dan password Anda!</p>

                                <!-- <div data-mdb-input-init class="form-outline form-white mb-4">
                                    <input type="email" id="typeEmailX" class="form-control form-control-lg" />
                                    <label class="form-label" for="typeEmailX">Email</label>
                                </div> -->

                                <form method="POST" action="<?= base_url('login/proses'); ?>">
                                    <div class="form-group">
                                        <label for="">Username</label>
                                        <input type="text" required class="form-control" autocomplete="off" name="user" id="user" placeholder="Masukan Username">
                                    </div>
                                    <div class="form-group">
                                        <label for="">Password</label>
                                        <input type="password" required class="form-control" autocomplete="off" name="pass" id="pass" placeholder="Masukan Password">
                                    </div>
                                    <button type="submit" class="btn btn-primary btn-md float-right">
                                        Masuk
                                    </button>
                                </form>
                            </div>

                            <div>
                                Copyright &copy; <?= date('Y'); ?> SARESTO
                                </br><a href="https://www.salasatekno.com" target="_blank"><b>Salasa Teknologi Solusindo</b></a>
                                </br>1.12.01
                            </div>
                        </div>


                    </div>
                </div>
            </div>
            <!-- grid -->
        </div>
    </section>
    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="<?= base_url('assets/js/jquery-3.3.1.min.js'); ?>"></script>
    <script src="<?= base_url('assets/plugins/bootstrap/popper.min.js'); ?>"></script>
    <script src="<?= base_url('assets/plugins/bootstrap/js/bootstrap.min.js'); ?>"></script>
    <script type="text/javascript" />
    document.getElementById("user").addEventListener("click", function(e) {
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