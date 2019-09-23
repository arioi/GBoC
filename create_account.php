<?php
    include("functions.php");
    $db = connecting_db();
    $commissions = $db->query('SELECT * FROM commissions WHERE active');
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
        <title>GBoC - inscription</title>
        <link rel="stylesheet" type="text/css" href="GBoC.css" media="all"/>
    </head>

    <body>
        <div id="corps">
            <h1>Inscription au module de Gestion des Bénévoles Ou des Commissions</h1>

            <p>
                Pour vous inscrire au module de Gestion des bénévoles Ou des Commissions, Veuillez communiquer les informations suivantes (les champs avec un * sont obligatoires) :<br />
            </p>

            <form method="post" action="post_create_account.php">
                Nom de famille* :<br>
                <input type="text" name="name" required="" <?php if(isset($_GET['name'])) echo 'value="'.str_replace('+',' ',$_GET['name']).'"' ?> size="26"><br>
                Prénom* :<br>
                <input type="text" name="surname" required="" <?php if(isset($_GET['surname']))echo 'value="'.$_GET['surname'].'"' ?> size="26"><br>
                Adresse E-mail* :<br>
                <input type="email" name="mail" required="" <?php if(isset($_GET['mail']))echo 'value="'.$_GET['mail'].'"' ?> size="26"><br>
                répétez l'adresse E-mail* :<br>
                <input type="email" name="mail_repeated" required="" size="26"><br>
                Numéro de telephone (0X XX XX XX XX):<br>
                <input type="number" name="tel" pattern="0[0-9]( [0-9]{2}){4}" <?php if(isset($_GET['tel']))echo 'value="'.str_replace('+',' ',$_GET['tel']).'"' ?> size="11"><br>
                Date de naissance* :<br>
                <input type="date" name="birth_date" required="" <?php if(isset($_GET['birth_date']))echo 'value='.$_GET['birth_date'] ?> ><br>
                Participation aux commissions :<br>
                <?php
                    while($data_commission = $commissions->fetch()){
                        echo '<input type="checkbox" name ="'.$data_commission['name_commission'].'">'.$data_commission['name_commission'].'<br>';
                    }
                ?>
                Mot de passe* :<br>
                <input type="password" name="password" required="" size="26"><br>
                Répétez le mot de passe* :<br>
                <input type="password" name="password_repeated" required="" size="26"><br>
                <input type="checkbox" name="charte" required=""> J'ai pris connaissance et j'accepte la <a href="charte.html" target="_blank">Charte d'utilisation</a><br>
                <input type="submit" value="S'inscrire">
            </form>
            <?php
                if(isset($_GET['error'])){
                    if ($_GET['error'] == "notsame") {
                        echo "Oups, vous avez du faire une erreur en recopiant votre adresse mail ou votre mot de passe. Veuillez recommencer s'il vous plait<br>";
                    }else{
                        echo "Oups, cette adresse mail est déjà enregistrée. Une erreur dans l'adresse? Compte déjà créé?<br>";
                    }
                }
            ?>
            <a href="reception.php">Retourner à la page de connexion</a><br>
        </div>
    </body>
</html>
