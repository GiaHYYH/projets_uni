<!-- créé par Georgia Eraste le 06/03/2025 -->
 <!-- Site web pour projet Sellection en lien avec la marchandise du groupe de K-Pop BTS. -->

<!DOCTYPE html>

<?php
    $error_message = null;

    $mysqli = new mysqli('localhost','e22400857sql','tJympCEx','e22400857_db1');
    
    if ($mysqli->connect_errno) {
        $error_message = "Problème de connexion à la base de données.";
    }
    // Instructions PHP à ajouter pour l'encodage utf8 du jeu de caractères
    if (!$mysqli->set_charset("utf8")) {
        $error_message = "Problème de chargement du jeu de caractères UTF-8.";
    }
    // Instruction à rajouter depuis PHP 8.1
    mysqli_report(MYSQLI_REPORT_OFF);
    //echo ("Connexion BDD réussie !\n");

    if (!$error_message) {
        $requete_pre="SELECT * FROM t_presentation_pre;";
        $requete_sel = "SELECT * FROM t_selection_sel;";
    
        $res_pre = $mysqli->query($requete_pre);
        $res_sel = $mysqli->query($requete_sel);

        if ($res_pre == false || $res_sel == false) {
            $error_message = "La requête a échoué. (Errno: " . $mysqli->errno . " — " . $mysqli->error . ")";
        } else {
            $struct = $res_pre->fetch_assoc();
        }

    }

   if (!$error_message) {
        if (!isset($_GET['elt_numero']) || !isset($_GET['sel_numero'])) {
            $error_message = "Vous avez oublié les paramètres.";
        } elseif (!ctype_digit($_GET['sel_numero']) || !ctype_digit($_GET['elt_numero'])) {
            $error_message = "Les paramètres doivent être des nombres.";
        } else {
            $sid = $_GET['sel_numero'];
            $eid = $_GET['elt_numero'];

            $res_verif_sel = $mysqli->query("SELECT * FROM t_selection_sel WHERE sel_numero = $sid;");
            if ($res_verif_sel->num_rows == 0) {
                $error_message = "La sélection n'existe pas.";
            } else {
                $res_verif_elt = $mysqli->query("SELECT * FROM t_rassemble_ras WHERE sel_numero = $sid AND elt_numero = $eid;");
                if ($res_verif_elt->num_rows == 0) {
                    $error_message = "Cet élément n'existe pas.";
                }
            }
        }
    }

    if (!$error_message) {
        // Intitulé de la sélection
        $req_sel_id = "SELECT sel_intitule AS titre FROM t_selection_sel WHERE sel_numero = $sid;";
        $res_intitule = $mysqli->query($req_sel_id);
        $sel_titre = $res_intitule->fetch_assoc();

        // Tous les infos sur l'élément
        $req_elt = "SELECT * FROM t_element_elt LEFT JOIN t_rassemble_ras USING(elt_numero) LEFT JOIN t_selection_sel USING(sel_numero) LEFT JOIN t_lien_lie USING(elt_numero) WHERE elt_numero = $eid AND sel_numero = $sid;";
        $res_elt = $mysqli->query($req_elt);
        $elt_id = $res_elt->fetch_assoc();

        // Élément suivant
        $req_next = "SELECT * FROM t_rassemble_ras WHERE elt_numero > $eid AND sel_numero = $sid ORDER BY elt_numero ASC LIMIT 1;";
        $res_next = $mysqli->query($req_next);
        $next = $res_next->fetch_assoc();

        // Élément précédent
        $req_prec = "SELECT * FROM t_rassemble_ras WHERE elt_numero < $eid AND sel_numero = $sid ORDER BY elt_numero DESC LIMIT 1;";
        $res_prec = $mysqli->query($req_prec);
        $prec = $res_prec->fetch_assoc();
    }
?>

<html lang="en">
<!-- Basic -->

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <!-- Mobile Metas -->
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Site Metas -->
    <title>ARMY Fanshop</title>
    <meta name="keywords" content="">
    <meta name="description" content="">
    <meta name="author" content="G. Eraste">

    <!-- Site Icons -->
    <link rel="shortcut icon" href="../images/favicon.ico" type="image/x-icon">
    <link rel="apple-touch-icon" href="../images/apple-touch-icon.png">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <!-- Site CSS -->
    <link rel="stylesheet" href="../css/style.css">
    <!-- Responsive CSS -->
    <link rel="stylesheet" href="../css/responsive.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="../css/custom.css">

    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
      <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->

</head>

<body>

    <!-- Start Main Top -->
    <div class="main-top">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                </div>
                <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                    <div class="custom-select-box">
                        <select id="basic" class="selectpicker show-tick form-control" data-placeholder="$ USD">
						<option>¥ JPY</option>
						<option>$ USD</option>
						<option>€ EUR</option>
					</select>
                    </div>
                    <div class="our-link">
                        <ul>
                            <li><a href="../connexion/session.php">My Account</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End Main Top -->

    <!-- Start Main Top -->
    <header class="main-header">
        <!-- Start Navigation -->
        <nav class="navbar navbar-expand-lg navbar-light bg-light navbar-default bootsnav">
            <div class="container">
                <!-- Start Header Navigation -->
                <div class="navbar-header">
                    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbar-menu" aria-controls="navbars-rs-food" aria-expanded="false" aria-label="Toggle navigation">
                    <i class="fa fa-bars"></i>
                </button>
                    <a href="../index.php"><img src="../ressources/<?php echo $struct['pre_logo'] ?>" class="logo" alt=""></a>
                </div>
                <!-- End Header Navigation -->

                <!-- Collect the nav links, forms, and other content for toggling -->
                <div class="collapse navbar-collapse" id="navbar-menu">
                    <ul class="nav navbar-nav ml-auto" data-in="fadeInDown" data-out="fadeOutUp">
                        <li class="nav-item active"><a class="nav-link" href="../index.php">Home</a></li>
                        <li class="nav-item"><a class="nav-link" href="../about.php">About Us</a></li>
                        <li class="dropdown">
                            <a href="#" class="nav-link dropdown-toggle arrow" data-toggle="dropdown">Products</a>
                            <ul class="dropdown-menu">
                                <li><a href="selection.php">Niveau selection</a></li>
                                <li><a href="select.php">Niveau select</a></li>
                                <li><a href="sel.php">Niveau sel</a></li>
                            </ul>
                        </li>
                        <li class="nav-item"><a class="nav-link" href="../inscription/inscription.php">Inscription</a></li>
                    </ul>
                </div>
                <!-- /.navbar-collapse -->

                <!-- Start Atribute Navigation -->
                <div class="attr-nav">
                    <ul>
                        <li class="search"><a href="#"><i class="fa fa-search"></i></a></li>
                    </ul>
                </div>
                <!-- End Atribute Navigation -->
            </div>

        </nav>
        <!-- End Navigation -->
    </header>
    <!-- End Main Top -->

    <!-- Start Top Search -->
    <div class="top-search">
        <div class="container">
            <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-search"></i></span>
                <input type="text" class="form-control" placeholder="Search">
                <span class="input-group-addon close-search"><i class="fa fa-times"></i></span>
            </div>
        </div>
    </div>
    <!-- End Top Search -->

    <div class="contact-box-main">
        <div class="container">
        <!--uniquement blocs if/else php initial pour affichage message erreur générés à l'aide d'IA générative; reste du code créé par G. Eraste-->
            <?php if ($error_message): ?>
                <div class="row justify-content-center mt-5">
                    <div class="col-md-6">
                        <div class="card shadow">
                            <div class="card-body text-center">
                                <div class="alert alert-warning" role="alert">
                                    <h4 class="alert-heading">Erreur</h4>
                                    <p><?php echo htmlspecialchars($error_message); ?></p>
                                </div>
                                <a href="selection.php" class="btn btn-outline-secondary">Retour à la page selection</a>
                            </div>
                        </div>
                    </div>
                </div>

            <?php else: ?>
                <div class="row justify-content-center">
                    <div class="col-lg-6 col-md-8 col-sm-12 text-center">
                        <h1><?php echo htmlspecialchars($sel_titre['titre']); ?></h1>
                        <br/>

                        <?php if ($elt_id['elt_etat'] === 'O'): ?>
                            <div class="alert alert-warning d-flex align-items-center justify-content-center gap-2" role="alert">
                                <i class="bi bi-lock-fill"></i>
                                <strong>Cet élément n'est pas publié.</strong>
                            </div>
                            <div class="navigation-liens">
                                <?php if ($res_prec->num_rows == 1): ?>
                                    <a href="affiche_selection.php?sel_numero=<?php echo $sid; ?>&elt_numero=<?php echo $prec['elt_numero']; ?>" class="btn btn-link">← Précédent</a>
                                <?php endif; ?>
                                <?php if ($res_next->num_rows == 1): ?>
                                    <a href="affiche_selection.php?sel_numero=<?php echo $sid; ?>&elt_numero=<?php echo $next['elt_numero']; ?>" class="btn btn-link">Suivant →</a>
                                <?php endif; ?>
                            </div>

                        <?php else: ?>
                            <div class="elt-detail">
                                <img src="../ressources/<?php echo $elt_id['elt_fichier_image']; ?>" alt="<?php echo htmlspecialchars($elt_id['elt_titre']); ?>" width="200">
                                <br/><br/>
                                <h3><?php echo htmlspecialchars($elt_id['elt_titre']); ?></h3>
                                <p><?php echo htmlspecialchars($elt_id['elt_descriptif']); ?></p>
                                <p><strong>Date de publication :</strong> <?php echo htmlspecialchars($elt_id['elt_date_publication']); ?></p>
                                <?php if (!empty($elt_id['lie_url'])): ?>
                                    <p><strong>Lien :</strong> <a href="<?php echo htmlspecialchars($elt_id['lie_url']); ?>" target="_blank"><?php echo htmlspecialchars($elt_id['lie_titre']); ?></a></p>
                                <?php else: ?>
                                    <p><strong>Lien :</strong> Aucun lien disponible.</p>
                                <?php endif; ?>
                            </div>
                            <br/>
                            <div class="navigation-liens">
                                <?php if ($res_prec->num_rows == 1): ?>
                                    <a href="affiche_selection.php?sel_numero=<?php echo $sid; ?>&elt_numero=<?php echo $prec['elt_numero']; ?>" class="btn btn-link">← Précédent</a>
                                <?php endif; ?>
                                <?php if ($res_next->num_rows == 1): ?>
                                    <a href="affiche_selection.php?sel_numero=<?php echo $sid; ?>&elt_numero=<?php echo $next['elt_numero']; ?>" class="btn btn-link">Suivant →</a>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>

                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Start Footer  -->
    <footer>
        <div class="footer-main">
            <div class="container">
                <div class="row">
                    <div class="col-lg-4 col-md-12 col-sm-12">
                        <div class="footer-widget">
                            <h4>Socials</h4>
                            <ul>
                                <li><a href="#"><i class="fab fa-twitter" aria-hidden="true"></i></a></li>
                                <li><a href="#"><i class="fab fa-instagram" aria-hidden="true"></i></a></li>
                                <li><a href="#"><i class="fab fa-whatsapp" aria-hidden="true"></i></a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-12 col-sm-12">
                        <div class="footer-link-contact">
                            <h4>Contact Us</h4>
                            <ul>
                                <?php
                                    echo "<li>";
                                    echo "Email : ";
                                    echo $struct['pre_mail'];
                                    echo "</li>";
                                    echo "<li>";
                                    echo "Adresse : ";
                                    echo $struct['pre_adresse'];
                                    echo "</li>";
                                    echo "<li>";
                                    echo "Numero de téléphone : ";
                                    echo $struct['pre_telephone'];
                                    echo "</li>";
                                    echo "<li>";
                                    echo "Horaires de disponibilités : ";
                                    echo $struct['pre_horaires'];
                                    echo "</li>";
                                ?>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php 
            //Ferme la connexion avec la base MariaDB
            $mysqli->close();
        ?>
    </footer>
    <!-- End Footer  -->

    <!-- Start copyright  -->
    <div class="footer-copyright">
        <p class="footer-company">All Rights Reserved. &copy; 2018 <a href="#">ThewayShop</a> Design By :
            <a href="https://html.design/">html design</a></p>
    </div>
    <!-- End copyright  -->

    <a href="#" id="back-to-top" title="Back to top" style="display: none;">&uarr;</a>

    <!-- ALL JS FILES -->
    <script src="../js/jquery-3.2.1.min.js"></script>
    <script src="../js/popper.min.js"></script>
    <script src="../js/bootstrap.min.js"></script>
    <!-- ALL PLUGINS -->
    <script src="../js/jquery.superslides.min.js"></script>
    <script src="../js/bootstrap-select.js"></script>
    <script src="../js/inewsticker.js"></script>
    <script src="../js/bootsnav.js."></script>
    <script src="../js/images-loded.min.js"></script>
    <script src="../js/isotope.min.js"></script>
    <script src="../js/owl.carousel.min.js"></script>
    <script src="../js/baguetteBox.min.js"></script>
    <script src="../js/form-validator.min.js"></script>
    <script src="../js/contact-form-script.js"></script>
    <script src="../js/custom.js"></script>
</body>

</html>