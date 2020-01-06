<?php
    session_start();
    include("functions.php");
    if(!user_verified()){
        header('location: reception.php');
    }

    $db = connecting_db();
    $volunteer = $db->query('SELECT id_volunteer, name_volunteer, surname_volunteer, birth_date, number_tel, mail FROM volunteers WHERE hex(id_volunteer)= \''.bin2hex($_SESSION['uuid']).'\'');
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
            <form id="userInfosForm" method="post" action="post_data_volunteer.php">
                <div class="row notClear">
                    <div class="notClear col s12 m4 l4">Nom de famille :</div>
                    <div class="notClear col s12 m8 l8">
                        <input type="text" class="browser-default" style="width: 100% !important;" name="name" required="" value= <?php echo '"'.$volunteer['name_volunteer'].'"'?>>
                    </div>
                    <div class="notClear col s12 m4 l4">Prénom :</div>
                    <div class="notClear col s12 m8 l8">
                        <input type="text" class="browser-default" style="width: 100% !important;" name="surname" required=""value= <?php echo '"'.$volunteer['surname_volunteer'].'"'?>>
                    </div>
                    <div class="notClear col s12 m4 l4">Adresse E-mail :</div>
                    <div class="notClear col s12 m8 l8">
                        <input type="email" class="browser-default" style="width: 100% !important;" name="mail" required=""value= <?php echo '"'.$volunteer['mail'].'"'?>>
                    </div>
                    <div class="notClear col s12 m4 l4">N° de telephone :</div>
                    <div class="notClear col s12 m8 l8">
                        <input type="number" class="browser-default" style="width: 100% !important;" name="tel" pattern="0[0-9]{9}|0[0-9]( [0-9]{2}){4}"value= <?php echo '"'.$volunteer['number_tel'].'"'?>>
                    </div>
                    <div class="notClear col s12 m4 l4">Date de naissance :</div>
                    <div class="notClear col s12 m8 l8">
                        <input type="date" class="browser-default" style="width: 100% !important;" name="birth_date" required="" value= <?php echo '"'.$volunteer['birth_date'].'"'?> >
                    </div>
                    <div class="notClear col s12 m6 l6">Participation aux commissions :</div>
                    <div class="notClear col s12 m6 l6">
                        <?php $commissions = $db->query('SELECT * FROM commissions WHERE active');
                            $commissions_volunteers = $db->query('SELECT GROUP_CONCAT(id_commission) as id_commissions FROM commissions_volunteers WHERE id_volunteer = \''.$_SESSION['uuid'].'\'');
                            $list_commissions = $commissions_volunteers->fetch();
                            while($commission = $commissions->fetch()){
                                echo '<input type="checkbox" name ="'.$commission['name_commission'].'"';
                                if(in_array($commission['id_commission'], explode(",",$list_commissions['id_commissions'])))  echo "checked='checked'"; echo '> '.$commission['name_commission'].'<br>';
                        }?>
                    </div>
                </div>
                <br><br>
                <input type="submit" name="update_data" value="Modifier mes informations">
            </form>
            <br><br>
            <form id="userPwdForm" method="post" action="post_data_volunteer.php">
                <div class="row notClear">
                    <div class="notClear col s12 m6 l6">Ancien mot de passe :</div>
                    <div class="notClear col s12 m6 l6"><input type="password" class="browser-default" style="width: 100% !important;" name="old_password"></div>
                    <div class="notClear col s12 m6 l6">Nouveau mot de passe :</div>
                    <div class="notClear col s12 m6 l6"><input type="password" class="browser-default" style="width: 100% !important;" name="new_password"></div>
                    <div class="notClear col s12 m6 l6">Répétez le nouveau mot de passe :</div>
                    <div class="notClear col s12 m6 l6"><input type="password" class="browser-default" style="width: 100% !important;" name="new_password_repeated"></div>
                </div>
                <br><br>
                <input type="submit" name="update_password" value="Modifier mon mot de passe">
            </form>
            <br><br>
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
            <form id="userUnsubscribeForm" method="post" action="unsubscribe.php">
                <input type="submit" value="Se désincrire du site">
            </form>
            <br>
        </div>
        <footer id="pied_de_page"></footer>
    </body>
</html>