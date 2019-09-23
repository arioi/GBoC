<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
        <title>GBoC - Accueil</title>
    </head>
 
    <body>
        <div id="corps">
            <h1>Accueil du module de Gestion des Bénévoles Ou des Commissions</h1>
            <p>
                Bienvenue sur le module de Gestion des bénévoles Ou des Commissions !<br />
                Ce module est là pour permettre aux chargés de commissions de la mission bretonne de faire appel aux bénévoles afin d'aider lors d'événement et pouvoir communniquer avec eux directement.<br />
            </p>

            <?php 
                if(isset($_GET['error'])){
                    if ($_GET['error'] == "mailnotexist") {
                        echo "Cette adresse mail n'est pas enregistrée. Une erreur dans l'adresse? Compte pas créé?";
                    }else if($_GET['error'] == "password"){
                        echo "Mot de passe incorrecte";
                    }else{
                        echo "Votre compte a bien été créé, vous pouvez maintenant vous connecter avec l'adresse mail enregistrée.";
                    }
                    echo "<br>";
                }
            ?>

            <form method="post" action="post_reception.php">
                Adresse E-mail :<br>
                <input type="email" name="mail" required="" <?php if(isset($_COOKIE['mail'])) echo 'value='.$_COOKIE['mail']; ?> size="26"><br>
                Mot de passe :<br>
                <input type="password" name="password" required="" <?php if(isset($_COOKIE['password'])) echo 'value='.$_COOKIE['password']; ?> size="26"><br>
                <input type="checkbox" name="save" <?php if(isset($_COOKIE['save'])) echo 'checked'; ?>> Se souvenir de mon identifiant et de mon mot de passe<br>
                <input type="submit" value="Connexion">
            </form>
            <a href="create_account.php">Créer un compte</a>
        </div>
    </body>
</html>