<?php
    session_start();
    include("functions.php");
    if(!user_verified()){
        header('location: reception.php');
    }
    $db = connecting_db();
    $volunteer = $db->query('SELECT id_volunteer, name_volunteer, surname_volunteer, birth_date, number_tel, mail FROM volunteers WHERE id_volunteer= \''.$_SESSION['uuid'].'\'');
    $volunteer = $volunteer->fetch()
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
        <title>GBoC - Mes informations</title>
    </head>

    <body>
        <?php include("menus.php"); ?>
        <div id="corps">
            <h1>Mes informations</h1>
            <form method="post" action="post_data_volunteer.php">
                Nom de famille :<br>
                <input type="text" name="name" required=""value= <?php echo '"'.$volunteer['name_volunteer'].'"'?>><br>
                Prénom :<br>
                <input type="text" name="surname" required=""value= <?php echo '"'.$volunteer['surname_volunteer'].'"'?>><br>
                Adresse E-mail :<br>
                <input type="email" name="mail" required=""value= <?php echo '"'.$volunteer['mail'].'"'?>><br>
                Numéro de telephone :<br>
                <input type="number" name="tel" pattern="0[0-9]{9}|0[0-9]( [0-9]{2}){4}"value= <?php echo '"'.$volunteer['number_tel'].'"'?>><br>
                Date de naissance :<br>
                <input type="date" name="birth_date" required="" value= <?php echo '"'.$volunteer['birth_date'].'"'?> ><br>
                Participation aux commissions :<br>
                <?php $commissions = $db->query('SELECT * FROM commissions WHERE active');
                $commissions_volunteers = $db->query('SELECT GROUP_CONCAT(id_commission) as id_commissions FROM commissions_volunteers WHERE id_volunteer = \''.$_SESSION['uuid'].'\'');
                $list_commissions = $commissions_volunteers->fetch();
                while($commission = $commissions->fetch()){
                    echo '<input type="checkbox" name ="'.$commission['name_commission'].'"';
                    if(in_array($commission['id_commission'], explode(",",$list_commissions['id_commissions'])))  echo "checked=\"\""; echo '>'.$commission['name_commission'].'<br>';
                }?>
                <input class="form" type="submit" name="update_data" value="Modifier mes informations">
            </form>
            <form method="post" action="post_data_volunteer.php">
                Ancien mot de passe :<br>
                <input type="password" name="old_password"><br>
                Nouveau mot de passe :<br>
                <input type="password" name="new_password"><br>
                Répétez le nouveau mot de passe :<br>
                <input type="password" name="new_password_repeated"><br>
                <input class="form" type="submit" name="update_password" value="Modifier mon mot de passe"><br>
            </form>
            <?php
            if(isset($_GET['error'])){
                if ($_GET['error'] == "mailexist") {
                    echo "Oups, cette adresse mail est déjà enregistrée. Une erreur dans l'adresse? Compte déjà créé?";
                }else if($_GET['error'] == "password"){
                    echo "Mot de passe incorrecte";
                }else{
                    echo "Oups, vous avez du faire une erreur en recopiant votre mot de passe. Veuillez recommencer s'il vous plait";
                }
            }
            if(isset($_GET['statut'])){
                echo "Vos nouvelles information on bien été enregistrées";
            } ?>
            <form method="post" action="unsubscribe.php">
                <input class="form" type="submit" value="Se désincrire du site">
            </form>
        </div>
        <footer id="pied_de_page"></footer>
    </body>
</html>
