<!-- créé par Georgia Eraste le 06/03/2025 -->
<!-- Site web pour projet Sellection en lien avec la marchandise du groupe de K-Pop BTS. -->

<?php
   session_start();
   
   $message="";
   $lien="";

   if (!empty($_POST['pseudo']) && !empty(($_POST['mdp'])) ){


      $id=htmlspecialchars(addslashes($_POST['pseudo']));
      $motdepasse=htmlspecialchars(addslashes($_POST['mdp']));

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
    //echo ("Connexion BDD réussie !");

      //REQUETE POUR RECUP TOUS LES PSEUDOS ET EVITER DOUBLON
      $requete_verif_pseudo="SELECT * FROM t_compte_cpt LEFT JOIN t_profil_pro USING (cpt_mail) WHERE cpt_mail = '$id' AND cpt_mot_de_passe = MD5('$motdepasse') AND (pro_validite = 'A' OR cpt_mail = 'gEstionnaire1@gmail.com');";
      $result_verif_pseudo=$mysqli->query($requete_verif_pseudo);
      if ($result_verif_pseudo==false) {
          // La requête a echoué
         $message="Votre compte n'est pas activé, veuillez contacter un administrateur pour qu'il l'active\n";
         exit();
      } else {
         //CONDITIONNELLE POUR VERIFIER MDP ET MDP CONFIRM
         if($result_verif_pseudo->num_rows==1){
            $profil=$result_verif_pseudo->fetch_assoc();
            $_SESSION['login']=$id;
            $_SESSION['role']  = ($id === 'gEstionnaire1@gmail.com') ? 'A' : $profil['pro_statut']; // ligne générée avec IA

                if($_SESSION['role']=='A' || $_SESSION['role']=='gEstionnaire1@gmail.com'){
                header("Location:../back/admin_acceuil.php");
                } else {

                header("Location:../back/resp_accueil.php");
                }
            } else{

                $message="compte ou profil inconnu !";
                $lien="<br /><a href=\"./session.php\">Cliquez ici pour réafficher le formulaire</a>";
            }
        }

   } else {
      $message="Veuillez remplir tous les champs";
      $lien="<br /><a href=\"./session.php\">Cliquez ici pour réafficher le formulaire</a>";
   }

   $requete_pre="SELECT * FROM t_presentation_pre;";
    $result2 = $mysqli->query($requete_pre);

    if ($result2 == false) //Erreur lors de l’exécution de la requête
    { // La requête a echoué
        echo "Error: La requête a echoué \n";
        echo "Errno: " . $mysqli->errno . "\n";
        echo "Error: " . $mysqli->error . "\n";
        exit();
    }

    $struct = $result2->fetch_assoc();
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
                            <li><a href="session.php">My Account</a></li>
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
                                <li><a href="../vitrine/selection.php">Niveau selection</a></li>
                                <li><a href="../vitrine/select.php">Niveau select</a></li>
                                <li><a href="../vitrine/sel.php">Niveau sel</a></li>
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

    <!-- Start Connexion -->
    <div class="contact-box-main">
        <div class="container">
        <?php if (!empty($message)) { ?>
            <div class="alert alert-danger" role="alert">
                <?php echo $message; ?>
            </div>
        <?php } ?>
        </div>
    </div>
    <!-- End Connexion -->

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