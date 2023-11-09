<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, 
    user-scalable=0" />
    <meta name="description" content="" />
    <meta name="author" content="" />

    <title>AGRIKA GROUP</title>
    <!-- Favicon-->
    <link rel="icon" type="image/x-icon" href="https://putrirembulan.com/logo_agrika.png" />
    <!-- Font Awesome icons (free version)-->
    <script src="https://use.fontawesome.com/releases/v5.15.1/js/all.js" crossorigin="anonymous"></script>
    <!-- Google fonts-->
    <link href="https://fonts.googleapis.com/css?family=Montserrat:400,700" rel="stylesheet" type="text/css" />
    <link href="https://fonts.googleapis.com/css?family=Lato:400,700,400italic,700italic" rel="stylesheet" type="text/css" />
    <!-- Core theme CSS (includes Bootstrap)-->
    <link href="css/styles.css" rel="stylesheet" />
    <style>
        .filterDiv {
            display: none;
        }

        .show {
            display: block;
        }
    </style>
</head>

<body id="page-top">
    <!-- Navigation-->
    <nav class="navbar navbar-expand-lg bg-secondary text-uppercase fixed-top" id="mainNav">
        <div class="container">
            <a class="navbar-brand js-scroll-trigger" href="#page-top">AGRIKA GROUP</a>
        </div>
    </nav>
    <!-- Masthead-->
    <header class="masthead bg-primary text-white text-center">
        <div class="container d-flex align-items-center flex-column">
            <!-- Masthead Heading-->
            <h1 class="masthead-heading text-uppercase mb-10">
                CHOOSE YOUR PROJECTS
            </h1>
            <!-- Icon Divider-->

            <!-- Masthead Subheading-->
            <p class="masthead-subheading font-weight-light mb-0">
                Who likes the system will make it easier to work.
            </p>
        </div>
    </header>
    <!-- Portfolio Section-->
    <section class="page-section portfolio" id="portfolio">
        <div class="container">
            <!-- Portfolio Section Heading-->
            <div class="button-group filters-button-group" id="myBtnContainer" align="center">

                <button class="button btn btn-primary" onclick="filterSelection('agav2')">
                    AGA V2
                </button>
                <button class="button btn btn-primary" onclick="filterSelection('agri')">
                    AGRI LARAS
                </button>
                <button class="button btn btn-primary" onclick="filterSelection('kasaga')">
                    AGRIKA
                </button>
                <!-- <button class="button btn btn-primary" onclick="filterSelection('sarang')">
                    PRODUKSI
                </button> -->
                <button class="button btn btn-primary" onclick="filterSelection('acc')">
                    ACCOUNTING
                </button>
            </div>

            <br /><br />
            <!-- Icon Divider-->

            <!-- Portfolio Grid Items-->

            <div class="row justify-content-center">
                <!--Accounting-->
                <div class="col-md-6 col-lg-3 mb-5 element-item filterDiv acc">
                    <a href="https://kasdll.ptagafood.com/">
                        <div class="portfolio-item mx-auto">
                            <center>
                                <img class="img-fluid" width="150px" src="assets/img/portfolio/KasDll2.png" alt="" />
                            </center>
                        </div>
                        <br />
                    </a>
                </div>
                <div class="col-md-6 col-lg-3 mb-5 element-item filterDiv kasaga">
                    <a href="https://jurnals.ptagafood.com/">
                        <div class="portfolio-item mx-auto">

                            <center>
                                <img class="img-fluid" width="150px" src="assets/img/portfolio/KasAga2.png" alt="" />
                            </center>
                        </div>
                        <br />

                    </a>
                </div>
                <!--Aga V2-->

                <div class="col-md-6 col-lg-3 mb-5 element-item filterDiv agav2">
                    <a href="https://cong2an.putrirembulan.com/">
                        <div class="portfolio-item mx-auto">

                            <center>
                                <img class="img-fluid" width="150px" src="assets/img/portfolio/Cong2an.png" alt="" />
                            </center>
                        </div>
                        <br />
                    </a>
                </div>
                <div class="col-md-6 col-lg-3 mb-5 element-item filterDiv agav2">
                    <a href="https://toko.ptagafood.com">
                        <div class="portfolio-item mx-auto">

                            <center>
                                <img class="img-fluid" width="150px" src="https://putrirembulan.com/assets/img/portfolio/TokoAga2.png" alt="" />
                            </center>
                        </div>
                        <br />
                    </a>
                </div>

                <div class="col-md-6 col-lg-3 mb-5 element-item filterDiv agav2">
                    <a href="https://e-absensi.putrirembulan.com/">
                        <div class="portfolio-item mx-auto">
                            <center>
                                <img class="img-fluid" src="assets/img/portfolio/4.jpg" alt="" />
                            </center>
                        </div>
                        <br />
                        <h5 class="text-center">ABSENSI ANAK LAKI / AGRILARAS</h5>
                        <p class="text-center">AGA PROJECT</p>
                    </a>
                </div>

                <div class="col-md-6 col-lg-3 mb-5 element-item filterDiv aga2">
                    <a href="https://kasdll.putrirembulan.com/">
                        <div class="portfolio-item mx-auto">
                            <center>
                                <img class="img-fluid" width="150px" src="assets/img/portfolio/KasDll2.png" alt="" />
                            </center>
                        </div>
                        <br />
                    </a>
                </div>

                <!--Agri-->
                <div class="col-md-6 col-lg-3 mb-5 element-item filterDiv agri">
                    <a href="https://ternak.putrirembulan.com/Login">
                        <div class="portfolio-item mx-auto" data-toggle="modal" data-target="#portfolioModal3">
                            <div class="portfolio-item-caption d-flex align-items-center justify-content-center h-100 w-100">
                                <div class="portfolio-item-caption-content text-center text-white">
                                    <i class="fas fa-link fa-3x"></i>
                                </div>
                            </div>
                            <img class="img-fluid" style="opacity:0.5;" src="assets/img/portfolio/3.jpg" alt="" />
                        </div>
                        <br />
                        <h5 class="text-center">ADMIN LAMA</h5>
                        <p class="text-center">AGRI LARAS PROJECT</p>
                    </a>
                </div>
                <div class="col-md-6 col-lg-3 mb-5 element-item filterDiv agri">
                    <a href="https://agrilaras.putrirembulan.com/auth">
                        <div class="portfolio-item mx-auto" data-toggle="modal" data-target="#portfolioModal3">
                            <div class="portfolio-item-caption d-flex align-items-center justify-content-center h-100 w-100">
                                <div class="portfolio-item-caption-content text-center text-white">
                                    <i class="fas fa-link fa-3x"></i>
                                </div>
                            </div>
                            <img class="img-fluid" style="opacity:0.5;" src="assets/img/portfolio/telur.jpg" alt="" />
                        </div>
                        <br />
                        <h5 class="text-center">KANDANG LAMA</h5>
                        <p class="text-center">AGRI LARAS PROJECT</p>
                    </a>
                </div>
                <div class="col-md-6 col-lg-3 mb-5 element-item filterDiv agri">
                    <a href="https://ternak.ptagafood.com">
                        <div class="portfolio-item mx-auto" data-toggle="modal" data-target="#portfolioModal3">
                            <div class="portfolio-item-caption d-flex align-items-center justify-content-center h-100 w-100">
                                <div class="portfolio-item-caption-content text-center text-white">
                                    <i class="fas fa-link fa-3x"></i>
                                </div>
                            </div>
                            <img class="img-fluid" src="assets/img/portfolio/3.jpg" alt="" />
                        </div>
                        <br />
                        <h5 class="text-center">ADMIN</h5>
                        <p class="text-center">AGRI LARAS PROJECT</p>
                    </a>
                </div>
                <div class="col-md-6 col-lg-3 mb-5 element-item filterDiv agri">
                    <a href="https://agrilaras.ptagafood.com">
                        <div class="portfolio-item mx-auto" data-toggle="modal" data-target="#portfolioModal3">
                            <div class="portfolio-item-caption d-flex align-items-center justify-content-center h-100 w-100">
                                <div class="portfolio-item-caption-content text-center text-white">
                                    <i class="fas fa-link fa-3x"></i>
                                </div>
                            </div>
                            <img class="img-fluid" src="assets/img/portfolio/telur.jpg" alt="" />
                        </div>
                        <br />
                        <h5 class="text-center">KANDANG</h5>
                        <p class="text-center">AGRI LARAS PROJECT</p>
                    </a>
                </div>

                <div class="col-md-6 col-lg-3 mb-5 element-item filterDiv kasaga">
                    <a href="https://sarang.ptagafood.com/">
                        <div class="portfolio-item mx-auto" data-toggle="modal" data-target="#portfolioModal6">
                            <center>
                                <img class="img-fluid" width="120px" src="assets/img/portfolio/swallow.png" alt="" />
                            </center>
                        </div>
                        <br />
                        <h5 class="text-center">KERJA SARANG NEW</h5>
                        <p class="text-center">SARANG PROJECT</p>
                    </a>
                </div>

                <div class="col-md-6 col-lg-3 element-item task filterDiv">
                    <a href="https://task.putrirembulan.com/">
                        <div class="portfolio-item mx-auto" data-toggle="modal" data-target="#portfolioModal6">
                            <div class="portfolio-item-caption d-flex align-items-center justify-content-center h-100 w-100">
                                <div class="portfolio-item-caption-content text-center text-white">
                                    <i class="fas fa-link fa-3x"></i>
                                </div>
                            </div>
                            <img class="img-fluid" src="assets/img/portfolio/13.jpg" alt="" />
                        </div>
                        <br />
                        <h5 class="text-center">E-TASKS | TUGAS</h5>
                        <p class="text-center">PROJECT ANAK LAKI</p>
                    </a>
                </div>

                <!-- <div class="col-md-6 col-lg-3 mb-5 element-item sarang filterDiv">
                    <a href="https://toko-cabut.putrirembulan.com/">
                        <div class="portfolio-item mx-auto">
                            <center>
                                <img class="img-fluid" width="120px" src="assets/img/portfolio/TokoAga2.png" alt="" />
                            </center>
                        </div>
                        <br />
                    </a>
                </div> -->

                <div class="col-md-6 col-lg-3 mb-5 element-item filterDiv aga">
                    <a href="https://kasbon.putrirembulan.com/">
                        <div class="portfolio-item mx-auto">
                            <div class="portfolio-item-caption d-flex align-items-center justify-content-center h-100 w-100">
                                <div class="portfolio-item-caption-content text-center text-white">
                                    <i class="fas fa-link fa-3x"></i>
                                </div>
                            </div>

                            <center>
                                <img class="img-fluid" width="150px" src="assets/img/portfolio/pay-per-click.png" alt="" />
                            </center>
                        </div>
                        <br />
                        <h5 class="text-center">BUKU KASBON</h5>
                        <p class="text-center">AGA PROJECT</p>
                    </a>
                </div>

                <div class="col-md-6 col-lg-3 mb-5 element-item filterDiv aga">
                    <a href="https://stok-saos.putrirembulan.com/">
                        <div class="portfolio-item mx-auto">
                            <div class="portfolio-item-caption d-flex align-items-center justify-content-center h-100 w-100">
                                <div class="portfolio-item-caption-content text-center text-white">
                                    <i class="fas fa-link fa-3x"></i>
                                </div>
                            </div>
                            <center>
                                <img class="img-fluid" width="150px" src="assets/img/portfolio/sauce.png" alt="" />
                            </center>
                        </div>
                        <br />
                        <h5 class="text-center">STOK SAOS</h5>
                        <p class="text-center">AGA PROJECT</p>
                    </a>
                </div>
            </div>
        </div>
    </section>
    <!-- About Section-->

    <!-- Contact Section-->

    <!-- Footer-->

    <!-- Copyright Section-->
    <div class="copyright py-4 text-center text-white">
        <div class="container">
            <small>Copyright © 2019 | AGRIKA GROUP </small>
        </div>
    </div>
    <!-- Scroll to Top Button (Only visible on small and extra-small screen sizes)-->
    <div class="scroll-to-top d-lg-none position-fixed">
        <a class="js-scroll-trigger d-block text-center text-white rounded" href="#page-top"><i class="fa fa-chevron-up"></i></a>
    </div>
    <!-- Portfolio Modals-->
    <!-- Portfolio Modal 1-->
    <!-- Portfolio Modal 6-->

    <!-- Bootstrap core JS-->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Third party plugin JS-->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-easing/1.4.1/jquery.easing.min.js"></script>
    <!-- Contact form JS-->
    <script src="assets/mail/jqBootstrapValidation.js"></script>
    <script src="assets/mail/contact_me.js"></script>
    <!-- Core theme JS-->
    <script src="js/scripts.js"></script>
    <script src="https://unpkg.com/isotope-layout@3/dist/isotope.pkgd.min.js"></script>
    <script>
        filterSelection("all");

        function filterSelection(c) {
            var x, i;
            x = document.getElementsByClassName("filterDiv");
            if (c == "") c = "";
            for (i = 0; i < x.length; i++) {
                w3RemoveClass(x[i], "show");
                if (x[i].className.indexOf(c) > -1) w3AddClass(x[i], "show");
            }
        }

        function w3AddClass(element, name) {
            var i, arr1, arr2;
            arr1 = element.className.split(" ");
            arr2 = name.split(" ");
            for (i = 0; i < arr2.length; i++) {
                if (arr1.indexOf(arr2[i]) == -1) {
                    element.className += " " + arr2[i];
                }
            }
        }

        function w3RemoveClass(element, name) {
            var i, arr1, arr2;
            arr1 = element.className.split(" ");
            arr2 = name.split(" ");
            for (i = 0; i < arr2.length; i++) {
                while (arr1.indexOf(arr2[i]) > -1) {
                    arr1.splice(arr1.indexOf(arr2[i]), 1);
                }
            }
            element.className = arr1.join(" ");
        }

        // Add active class to the current button (highlight it)
        var btnContainer = document.getElementById("myBtnContainer");
        var btns = btnContainer.getElementsByClassName("btn");
        for (var i = 0; i < btns.length; i++) {
            btns[i].addEventListener("click", function() {
                var current = document.getElementsByClassName("active");
                current[0].className = current[0].className.replace(" active", "");
                this.className += " active";
            });
        }
    </script>
</body>

</html>