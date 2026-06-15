<?php
    session_start();
    //destruction de la session
    session_destroy();
    // libération des variables globales associées à la session
    unset($_SESSION);
    //Redirection vers la page de connexion
    header("Location:session.php");
?>