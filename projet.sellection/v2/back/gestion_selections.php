<!-- créé par Georgia Eraste le 06/03/2025 -->
 <!-- Site web pour projet Sellection en lien avec la marchandise du groupe de K-Pop BTS. -->

<?php
    session_start();

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

    // code pour la sélection
    $sql_sel = "SELECT sel_numero, sel_intitule, sel_etat, cpt_mail FROM t_selection_sel;";
    $result = $mysqli->query($sql_sel);

    if ($result == false) //Erreur lors de l’exécution de la requête
    { // La requête a echoué
        echo "Error: La requête a echoué \n";
        echo "Errno: " . $mysqli->errno . "\n";
        echo "Error: " . $mysqli->error . "\n";
        exit();
    }


    // code pour la présentation
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

<?php
    if (!empty($_GET['erreur'])) {
        echo '<script>alert("' . htmlspecialchars($_GET['erreur']) . '");</script>';
    }
?>

<!DOCTYPE html>
<html lang="en">
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
                        <a href="<?php echo ($_SESSION['role'] === 'A') ? 'admin_acceuil.php' : 'resp_accueil.php'; ?>"><img src="../ressources/<?php echo $struct['pre_logo'] ?>" class="logo" alt=""></a>
                    </div>
                    <!-- End Header Navigation -->
                     <!-- Collect the nav links, forms, and other content for toggling -->
                <div class="collapse navbar-collapse" id="navbar-menu">
                    <ul class="nav navbar-nav ml-auto" data-in="fadeInDown" data-out="fadeOutUp">
                        <?php if ($_SESSION['role'] == 'A') : ?>
                            <li class="nav-item"><a class="nav-link" href="gestion_comptes_pro.php">Gestion Comptes & Profils</a></li>
                        <?php endif; ?>
                        <li class="nav-item"><a class="nav-link" href="#">Gestion Actualités</a></li>
                        <li class="nav-item"><a class="nav-link" href="gestion_selections.php">Gestion Sélections/Éléments</a></li>
                        <li class="nav-item"><a class="nav-link" href="#">Gestion Présentation</a></li>
                        <li class="nav-item"><a class="nav-link" href="#">Gestion Liens</a></li>
                    </ul>
                </div>
                <!-- /.navbar-collapse -->
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


        <div class="container">
            <br/>
            <br/>
            <h1>GESTION SELECTIONS & ÉLÉMENTS</h1>
            <br/>
            <h2>Ajout d'une sélection</h2>
            <form action="gestion_sels_action.php" method="post">
                <fieldset>
                    <p>Titre : <input type="text" class="form-control" name="titre_sel" placeholder="Titre Sélection" /></p>
                    <p>Description : <input type="text" class="form-control" name="description" placeholder="Description Sélection" /></p>
                    <br/>
                    <p><input type="submit" class="btn hvr-hover" value="Valider"/></p>
                    <br/>
                    <br/>
                </fieldset>
            </form>
        </div>
        <div class="container">
            <h2>Suppression d'une sélection</h2>
            <form action="gestion_sels_action.php" method="post">
                <fieldset>
                    <p>Numéro Identifiant Sélection : <input type="number" class="form-control" name="id_sel" placeholder="Identifiant" /></p>
                    <br/>
                    <p><input type="submit" class="btn hvr-hover" value="Valider"/></p>
                    <br/>
                    <br/>
                </fieldset>
            </form>
        </div>
        <div class="container">
        <br />
        <?php
            echo "<div class='row'>";
            echo "<div class='col-lg-12'>";
            echo "<table class='table table-hover table-bordered'>";
            echo "<thead>
                <tr>
                    <th>Numéro Sélection</th>
                    <th>Sélection</th>
                    <th>État Sélection</th>
                    <th>Élément(s)</th>
                    <th>Créateur</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>";

            while ($sel = $result->fetch_assoc()) {

                $sql_elt = "SELECT elt_numero, elt_titre, elt_etat, lie_url, lie_titre FROM t_rassemble_ras JOIN t_element_elt USING(elt_numero) LEFT JOIN t_lien_lie USING(elt_numero) WHERE sel_numero = " . $sel['sel_numero'];

                $result_elt = $mysqli->query($sql_elt);

                $elements = "";

                if ($result_elt->num_rows > 0) {

                    while ($elt = $result_elt->fetch_assoc()) {
                        $elements .= "N°{$elt['elt_numero']} - {$elt['elt_titre']} ({$elt['elt_etat']})<br>";
                    }
            
                } else {
                    $elements = "Aucun élément";
                }
            
                echo "<tr>";
                echo "<td>{$sel['sel_numero']}</td>";
                echo "<td>{$sel['sel_intitule']}</td>";
                echo "<td>{$sel['sel_etat']}</td>";
                echo "<td>{$elements}</td>";
                echo "<td>{$sel['cpt_mail']}</td>";
            
                echo "<td>
                    <form action='gestion_sels_action.php' method='post'
                    onsubmit=\"return confirm('Suppression sélection n°{$sel['sel_numero']} ?');\">
                        <input type='hidden' name='id_sel' value='{$sel['sel_numero']}' />
                        <button type='submit' class='btn btn-danger'>
                            <i class='fa fa-trash'></i>
                        </button>
                    </form>
                </td>";
            
                echo "</tr>";
            }
            
            echo "</tbody></table>";
            echo "</div></div>";
            ?>
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