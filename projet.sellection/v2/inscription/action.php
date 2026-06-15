<?php
    $affich_formulaire = false;
    $inscription = false;
    $erreur = "";

    $mysqli = new mysqli('localhost','e22400857sql','tJympCEx','e22400857_db1');
    
    if ($mysqli->connect_errno) {
        // Affichage d'un message d'erreur
        echo "Error: Problème de connexion à la BDD \n";
        echo "Errno: " . $mysqli->connect_errno . "\n";
        echo "Error: " . $mysqli->connect_error . "\n";
        // Arrêt du chargement de la page
        exit();
    }
    // Instructions PHP à ajouter pour l'encodage utf8 du jeu de caractères
    if (!$mysqli->set_charset("utf8")) {
        printf("Pb de chargement du jeu de car. utf8 : %s\n", $mysqli->error);
        exit();
    }
    // Instruction à rajouter depuis PHP 8.1
    mysqli_report(MYSQLI_REPORT_OFF);
    //echo ("Connexion BDD réussie !\n");

    $requete_pre="SELECT * FROM t_presentation_pre;";

    $res_pre = $mysqli->query($requete_pre);

    if ($res_pre == false) //Erreur lors de l’exécution de la requête
    { // La requête a echoué
        echo "Error: La requête a echoué \n";
        echo "Errno: " . $mysqli->errno . "\n";
        echo "Error: " . $mysqli->error . "\n";
        exit();
    }
    
    $struct = $res_pre->fetch_assoc();

    //Exécution de la requête d'ajout d'un compte dans la table des comptes


    //1) On teste et on récupère les données saisies
    if (!empty($_POST["nom"]) && !empty($_POST["prenom"]) && !empty($_POST["pseudo"]) && !empty($_POST["code"]) && !empty($_POST["mdp1"]) && !empty($_POST["mdp2"])){

        $nom = htmlspecialchars(addslashes($_POST["nom"]));
        $prenom = htmlspecialchars(addslashes($_POST["prenom"]));    
        $id = htmlspecialchars(addslashes($_POST["pseudo"]));
        $code = htmlspecialchars(addslashes($_POST["code"]));
        $mdp1 = htmlspecialchars(addslashes($_POST["mdp1"]));
        $mdp2 = htmlspecialchars(addslashes($_POST["mdp2"]));

        $sql_code = "SELECT pre_code_inscription FROM t_presentation_pre;";
        
        $result_code = $mysqli->query($sql_code);
        
        $code_inscription = $result_code->fetch_assoc();

        if (strcmp($code, $code_inscription['pre_code_inscription']) == 0) {
            if (strcmp($mdp1, $mdp2) == 0) {
                $sql1 = "INSERT INTO t_compte_cpt VALUES('" . $id . "' , MD5('" . $mdp1 . "'));";
                $result3 = $mysqli->query($sql1);
                if ($result3 == false) {
                    echo "Error: La requête a échoué \n";
                    echo "Errno: " . $mysqli->errno . "\n";
                    echo "Error: " . $mysqli->error . "\n";
                    $erreur = "Ce pseudo est déjà utilisé.";
                    $affich_formulaire = true;
                    //exit;
                }

                $sql2 = "INSERT INTO t_profil_pro VALUES('" . $id . "', '" . $nom . "', '" . $prenom . "', '" . 'R' . "', '" . 'D' . "', NOW())";
                $result4 = $mysqli->query($sql2);

                if ($result4 == false) {
                    $sql3 = "DELETE FROM t_compte_cpt WHERE cpt_mail='$id'";
                    $mysqli->query($sql3);
                    $erreur = "Erreur lors de la création du profil. Compte supprimé.";
                    $affich_formulaire = true;

                    echo "Error: La requête a échoué \n";
                    echo "Errno: " . $mysqli->errno . "\n";
                    echo "Error: " . $mysqli->error . "\n";
                    echo "Compte supprimé";
                    //exit;
                 } else {
                    $inscription = true;
                }
            } else {
                $erreur = "Les mots de passe ne correspondent pas.";
                $affich_formulaire = true;
            }
        } else {
            $erreur = "Le code d'inscription est incorrect.";
            $affich_formulaire = true;
        }
    } else {
        $erreur = "Veuillez remplir tous les champs.";
        $affich_formulaire = true;
    }
?>

<!DOCTYPE html>
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
                    <a href="../index.php"><img src="../ressources/<?php echo $struct['pre_logo'] ?>" class="logo" alt="logo of the website"/></a>
                </div>
                <!-- End Header Navigation -->

                <!-- Collect the nav links, forms, and other content for toggling -->
                <div class="collapse navbar-collapse" id="navbar-menu">
                    <ul class="nav navbar-nav ml-auto" data-in="fadeInDown" data-out="fadeOutUp">
                        <li class="nav-item"><a class="nav-link" href="../index.php">Home</a></li>
                        <li class="nav-item"><a class="nav-link" href="../about.php">About Us</a></li>
                        <li class="dropdown">
                            <a href="#" class="nav-link dropdown-toggle arrow" data-toggle="dropdown">Products</a>
                            <ul class="dropdown-menu">
                                <li><a href="../vitrine/selection.php">Niveau selection</a></li>
                                <li><a href="../vitrine/select.php">Niveau select</a></li>
                                <li><a href="../vitrine/sel.php">Niveau sel</a></li>
                            </ul>
                        </li>
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
            <?php
                if ($inscription) {
                    echo 'Bonjour, ' . htmlspecialchars($_POST['nom']) . '.<br />Pseudo : ' . htmlspecialchars($_POST['pseudo']) . ' !';
                } elseif ($affich_formulaire && !empty($erreur)) {
                    echo '<p>Erreur : ' . $erreur . '</p>';
                    echo '<a href="inscription.php">Retour.</a>';
                }
            ?>
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