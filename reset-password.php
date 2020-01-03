<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
        <title>GBoC - Mot de passe perdu</title>
    </head>
    <body>
    <?php
    session_start();
    include("functions.php");
    $db = connecting_db();

    $redirect = NULL;
    if($_POST['location'] != '') {
      $redirect = $_POST['location'];
    }

  if (isset($_GET["key"]) && isset($_GET["email"]) && isset($_GET["action"]) && ($_GET["action"]=="reset") && !isset($_POST["action"])){
    $key = $_GET["key"];
    $email = $_GET["email"];
    $curDate = date("Y-m-d H:i:s");
    $keys = $db->prepare('SELECT * FROM password_reset_temp WHERE `key`=:key and email=:email');
    $keys->execute(array(
      ':key' => $key,
      ':email' => $email));
  if ($keys->rowCount() == 0){
    $error .= '<h2>Lien invalide</h2>
      <p>Le lien est invalide ou a expiré. Soit le lien a mal été copié depuis l\'email
      ou vous avez déjà réinitialisé votre mot de passe dans ce cas, la clé a été désactivée.</p>
      <p><a href="http://gestionbenevolesmbtav.fr">
      Cliquez ici</a> pour réinitialisé le mot de passe.</p>';
  }else{
    $result = $keys->fetch();
    $expDate = $result['expDate'];
    if ($expDate >= $curDate){
      ?>
      <br />
      <form method="post" action="" name="update">
        <input type="hidden" name="action" value="update" />
        <br /><br />
        <label><strong>Entrer un nouveau mot de passe :</strong></label><br />
        <input type="password" name="pass1" required />
        <br /><br />
        <label><strong>Confirmer le nouveau mot de passe :</strong></label><br />
        <input type="password" name="pass2" required/>
        <br /><br />
        <input type="hidden" name="email" value="<?php echo $email;?>"/>
        <input type="submit" value="Réinitialiser" />
      </form>
      <?php
    }else{
      $error .= "<h2>Lien expiré</h2>
        <p>Le lien a expiré. Vous essayé de d'utiliser un lien qui a expiré, celui-ci n'est valide que pendant 7 jours.<br /><br /></p>";
    }
  }
  if($error!=""){
    echo "<div class='error'>".$error."</div><br />";
  }
} // isset email key validate end


if(isset($_POST["email"]) && isset($_POST["action"]) && ($_POST["action"]=="update")){
  $error="";
  $pass1 = $_POST["pass1"];
  $pass2 = $_POST["pass2"];
  $email = $_POST["email"];
  $curDate = date("Y-m-d H:i:s");
  if ($pass1!=$pass2){
    $error.= "<p>Les mots de passe ne sont pas identiques.<br /><br /></p>";
  }
  if($error!=""){
    echo "<div class='error'>".$error."</div><br />";
  }else{
    $pass1 = password_hash($_POST["pass1"], PASSWORD_BCRYPT);;
    $update = $db->prepare('UPDATE volunteers SET password=:password WHERE mail=:email');
    $update->execute(array(
      ':password' => $pass1,
      ':email' => $email));

    $delete = $db->prepare('DELETE FROM password_reset_temp WHERE email=:email');
    $delete->execute(array(
      ':email' => $email));

echo '<div class="error"><p>Félicitation ! Votre mot de passe a été modifié.</p>
<p><a href="http://gestionbenevolesmbtav.fr">
Cliquez ici</a> pour vous connecter.</p></div><br />';
   }
}
?>
</body>
</html>
