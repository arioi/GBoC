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
    $email = $_POST["mail"];
    $email = filter_var($email, FILTER_SANITIZE_EMAIL);
    $email = filter_var($email, FILTER_VALIDATE_EMAIL);
    if (!$email) {
      $error .="<p>Cette adresse mail est invalide</p>";
   }else{
    $sel_query = $db->prepare('SELECT * FROM volunteers WHERE mail=:mail');
    $sel_query->execute(array('mail'=>$email));
    if($sel_query->rowCount() == 0){
      $error .= "<p>Aucun n'utilisateur n'est enregistré avec cette adresse</p>";
    }
  }
  if($error!=""){
   echo "<div class='error'>".$error."</div>
   <br /><a href='javascript:history.go(-1)'>Revenir à la page précédente</a>";
   }else{
     $expFormat = mktime(
       date("H"), date("i"), date("s"), date("m") ,date("d")+7, date("Y")
     );
     $expDate = date("Y-m-d H:i:s",$expFormat);
     $key = md5(2418*2+$email);
     $addKey = substr(md5(uniqid(rand(),1)),3,10);
     $key = $key . $addKey;
     $newrecovery = $db->prepare('INSERT INTO password_reset_temp VALUES (:email, :key, :expDate)');
     $newrecovery->execute(array(
         ':email' => $email,
         ':key' => $key,
         ':expDate' => $expDate));

     $output='<p>Bonjour,</p>';
     $output.='<p>Merci de cliquer sur le lien suivant pour réinitialiser le mot de passe</p>';
     $output.='<p>-------------------------------------------------------------</p>';
     $output.='<p><a href="http://gestionbenevolesmbtav.fr/reset-password.php?key='.$key.'&email='.$email.'&action=reset" target="_blank">
    http://gestionbenevolesmbtav.fr/reset-password.php
    ?key='.$key.'&email='.$email.'&action=reset</a></p>';
    $output.='<p>-------------------------------------------------------------</p>';
    $output.='<p>Assurez vous de copier le lien complet dans votre navigateur.
    Ce lien expira dans 7 jours.</p>';
    $output.='<p>Si vous n\'avez pas solicité une réinitialisation de votre mot de passe, vous pouvez ignorer cet email.</p>';
    $output.='<p>Merci,</p>';
    $body = $output;
    $subject = "GBoC - Mot de passe oublié";

    $email_to = $email;

    $headers = "MIME-Version: 1.0" . "\n";
    $headers .= "Content-type: text/html; charset=utf-8" . "\r\n";
    $headers .= 'From: GBoC@mbtav.fr' . "\r\n";

    $resultat = mail($email_to, utf8_decode($subject), $output, $headers);
    echo "<div class='error'>
    <p>Un email vous a été envoyé pour réinitialiser votre mot de passe.</p>
    </div><br /><br /><br />";
   }
?>
</body>
</html>
