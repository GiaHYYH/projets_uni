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
         echo $message;
         exit();
      } else {
         //CONDITIONNELLE POUR VERIFIER MDP ET MDP CONFIRM
         if($result_verif_pseudo->num_rows==1){
            $profil=$result_verif_pseudo->fetch_assoc();
            $_SESSION['login']=$id;
            $_SESSION['role']  = ($id === 'gEstionnaire1@gmail.com') ? 'A' : $profil['pro_statut']; // ligne générée avec IA

                if($_SESSION['role']=='A' || $_SESSION['role']=='gEstionnaire1@gmail.com'){
                header("Location:admin_acceuil.php");
                } else {

                header("Location:resp_accueil.php");
                }
            } else{

                $message="compte ou profil inconnu !";
                echo $message;
                $lien="<br /><a href=\"./session.php\">Cliquez ici pour réafficher le formulaire</a>";
            }
        }

   } else {
      $message="Veuillez remplir tous les champs";
      echo $message;
      $lien="<br /><a href=\"./session.php\">Cliquez ici pour réafficher le formulaire</a>";
   }

    //Fermeture de la communication avec la base MariaDB
    $mysqli->close();
?>