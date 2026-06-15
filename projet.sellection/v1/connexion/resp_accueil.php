<!-- créé par Georgia Eraste le 06/03/2025 -->
 <!-- Site web pour projet Sellection en lien avec la marchandise du groupe de K-Pop BTS. -->

<?php
    /* Vérification ci-dessous à faire sur toutes les pages dont l'accès est
    autorisé à un utilisateur connecté. */
    session_start();
    if (!isset($_SESSION['login']) || !isset($_SESSION['role'])) {
        header("Location: session.php");
        exit();
    } else if ($_SESSION['role'] != 'R') {
        header("Location: session.php");
        exit();
    }
?>

 <!DOCTYPE html>
<html lang="en">
<!-- Basic -->

<head>
<!--entête du fichier HTML-->
</head>

<body>
    <body>
        <!--contenu du fichier HTML-->
        <h1>ESPACE RESPONSABLES</h1>
        <!--Suite du contenu du fichier HTML-->
        <?php
            echo "<p>Bienvenue, <strong>" . htmlspecialchars($_SESSION['login']) . "</strong> !</p>";
            echo "<p>Votre statut : <strong>" . htmlspecialchars($_SESSION['role']) . "</strong></p>";
        ?>
        <br/>
        <br/>
        <a href = "session.php"><input type="button" value="Déconnexion" /></a>
</body>

</html>