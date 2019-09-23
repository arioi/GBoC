<?php
    session_start();
    include("functions.php");
    if(!user_verified()){
        header('location: reception.php');
    }
    $db = connecting_db();
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
        <title>GBoC - Désincription</title>
    </head>
    <body>
        <?php include("menus.php"); ?>
        <div id="corps">
            Vous êtes sur le point de vous désincrire du site, si vous continuez, toutes les informations vous consernant serons supprimmées.
            <form method="post" action="post_data_volunteer.php">
                <input type="submit" value="Continuer">
            </form>
            <form method="post" action="data_volunteer.php">
                <input type="submit" name="unsubscribe" value="Annuler">
            </form>
        </div>
    </body>
</html>