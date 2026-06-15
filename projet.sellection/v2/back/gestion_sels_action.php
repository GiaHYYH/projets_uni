/* créé par Georgia Eraste le 06/03/2025 */
/* Site web pour projet Sellection en lien avec la marchandise du groupe de K-Pop BTS. */

<?php
    session_start();

    if (!empty($_POST['id_sel'])) {
        $id = $_POST['id_sel'];
        if (!ctype_digit($id)) {
            $erreur = urlencode("Erreur : L'identifiant de la sélection doit être un chiffre.");
            header("Location: ./gestion_selections.php?erreur=" . $erreur);
            exit();
        }

        $mysqli = new mysqli('localhost', 'e22400857sql', 'tJympCEx', 'e22400857_db1');
        if ($mysqli->connect_errno) {
            echo "Error: Problème de connexion à la BDD \n";
            echo "Errno: " . $mysqli->connect_errno . "\n";
            echo "Error: " . $mysqli->connect_error . "\n";
            exit();
        }

        if (!$mysqli->set_charset("utf8")) {
            printf("Pb de chargement du jeu de car. utf8 : %s\n", $mysqli->error);
            exit();
        }

        mysqli_report(MYSQLI_REPORT_OFF);

         // Vérification que l'identifiant existe dans la BDD
        $requete_verif = "SELECT sel_numero FROM t_selection_sel WHERE sel_numero = " . $id . ";";
        $result_verif = $mysqli->query($requete_verif);

        if ($result_verif == false || $result_verif->num_rows == 0) {
            $erreur = urlencode("Erreur : La sélection avec l'identifiant " . $id . " n'existe pas dans la BDD.");
            $mysqli->close();
            header("Location: ./gestion_selections.php?erreur=" . $erreur);
            exit();
        }

        // Suppression de la sélection & de ces éléments (s'il y en a)
        $requete_suppr1 = "DELETE FROM t_rassemble_ras WHERE sel_numero = " . $id . ";";
        $result_suppr1 = $mysqli->query($requete_suppr1);
        $requete_suppr2 = "DELETE FROM t_selection_sel WHERE sel_numero = " . $id . ";";
        $result_suppr2 = $mysqli->query($requete_suppr2);

        if ($result_suppr1 == false || $result_suppr2 == false) {
            $erreur = urlencode("Erreur : La suppression de la sélection a échoué. [" . $mysqli->errno . "] " . $mysqli->error);
            $mysqli->close();
            header("Location: ./gestion_selections.php?erreur=" . $erreur);
            exit();
        }

        $mysqli->close();
        header("Location: ./gestion_selections.php");
        exit();

    }elseif (!empty($_POST['titre_sel'])){
        $titre=htmlspecialchars(addslashes($_POST['titre_sel']));

        $desc_brute = $_POST['description'];

        if (empty($desc_brute)) {
            $desc = NULL;
        } else {
            $desc=htmlspecialchars(addslashes($_POST['description']));
        }

        
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

        $requete_ajout = "INSERT INTO t_selection_sel VALUES (NULL, '" . $titre . "', '" . $desc . "', NOW(), '" . $_SESSION['login'] . "', 'D');";
        $result_ajout = $mysqli->query($requete_ajout);

        if ($result_ajout == false) {
            $erreur = urlencode("Erreur : L'ajout de la sélection a échoué. [" . $mysqli->errno . "] " . $mysqli->error);
            $mysqli->close();
            header("Location: ./gestion_selections.php?erreur=" . $erreur);
            exit();
        }

        $mysqli->close();
        header("Location: ./gestion_selections.php");
        exit();

    } else {
        $erreur = urlencode("Veuillez renseigner au minimum le titre de la sélection.");
        header("Location: ./gestion_selections.php?erreur=" . $erreur);
        exit();
    }
    
?>
