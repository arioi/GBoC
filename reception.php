<!DOCTYPE html>

<html>
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>GBoC - Accueil</title>
        <link rel="stylesheet" type="text/css" href="GBoC.css">
        <script src="http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
    </head>

    <body>
        <div id="corps">
            <h1>Accueil du module de Gestion des Bénévoles Ou des Commissions</h1>
            <p>
                Bienvenue sur le module de Gestion des bénévoles Ou des Commissions !<br/><br/>
                Ce module est là pour permettre aux chargés de commissions de la mission bretonne de faire appel aux bénévoles afin d'aider lors d'événement et pouvoir communiquer avec eux directement.<br />
            </p>

            <form method="post" action="post_reception.php">
                <span>Adresse E-mail :</span><br/>
                <input type="email" name="mail" required="" <?php if(isset($_COOKIE['mail'])) echo 'value='.$_COOKIE['mail']; ?> size="26"><br/>
                <span>Mot de passe :</span><br/>
                <input type="password" name="password" required="" <?php if(isset($_COOKIE['password'])) echo 'value='.$_COOKIE['password']; ?> size="26"><br/>
                <?php
                    if(isset($_GET['error'])){
                        if ($_GET['error'] == "mailnotexist") {
                            echo "<span style='color:red;'>Cette adresse mail n'est pas enregistrée. Une erreur dans l'adresse? Compte pas créé ?</span>";
                        } else if($_GET['error'] == "password") {
                            //echo "Mot de passe incorrecte";
                            echo "<span style='color:red;'>Mot de passe incorrecte</span>";
                        } else {
                            echo "<span style='color:green;'>Votre compte a bien été créé, vous pouvez maintenant vous connecter avec l'adresse mail enregistrée.</span>";
                        }
                        echo "<br/><br/>";
                    }
                ?>
                <input type="checkbox" name="save" <?php if(isset($_COOKIE['save'])) echo 'checked'; ?>><span>Mémoriser mes identifiants</span><br/><br/>
                <input type="submit" id="login" value="Connexion"><br/><br/>
                <input type="submit" formaction="post_lost_password.php" value="Mot de passe oublié">
                <span><a href="create_account.php">Créer un compte</a></span>
            </form>
        </div>
    </body>
</html>
