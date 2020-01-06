<?php
    session_start();
    include("functions.php");
    $db = connecting_db();

    $redirect = NULL;
    if($_POST['location'] != '') {
      $redirect = $_POST['location'];
    }

    $volunteers = $db->prepare('SELECT * FROM volunteers WHERE mail=:mail');
    $volunteers->execute(array('mail'=>$_POST['mail']));
    if($volunteers->rowCount() == 0){
        header('location: reception.php?error=mailnotexist');
    }else{
        $data_volunteer = $volunteers->fetch();
        if(!password_verify($_POST['password'],$data_volunteer['password'])){
            header('location: reception.php?mail='.$_POST['mail'].'&error=password');
        }else{
            $_SESSION['uuid'] = $data_volunteer['id_volunteer'];
            $_SESSION['role'] = $data_volunteer['role'];
            $_SESSION['prenom'] = $data_volunteer['surname_volunteer'];
            if($redirect) {
              header('Location:'. $redirect);
            } else {
              header('location: volunteer_events.php');
            }
            setcookie('mail', '', time(), null, null, false, true);
            setcookie('password', '', time(), null, null, false, true);
            setcookie('save', '', time(), null, null, false, true);
            if(isset($_POST['save'])){
                setcookie('mail', $_POST['mail'], time() + 365*24*3600, null, null, false, true);
                setcookie('password', $_POST['password'], time() + 365*24*3600, null, null, false, true);
                setcookie('save', $_POST['save'], time() + 365*24*3600, null, null, false, true);
            }
        }
    }
    $volunteers->closeCursor();
?>
