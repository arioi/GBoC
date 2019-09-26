<?php
    session_start();
    include("functions.php");
    if(!user_verified()){
        header('location: reception.php');
    }
    $db = connecting_db();
    $volunteer = $db->query('SELECT * FROM volunteers WHERE id_volunteer=\''.$_SESSION['uuid'].'\'');
    $data_volunteer = $volunteer->fetch();

    if(isset($_POST['update_data'])){
        if($_POST['mail'] != $data_volunteer['mail']){
            $mailexist = $db->prepare('SELECT * FROM volunteers WHERE mail=:mail');
            $mailexist->execute(array('mail'=>$_POST['mail']));
            if($mailexist->rowCount() > 0){
                header('location: data_volunteer.php?name='.str_replace(' ','+',$_POST['name']).'&surname='.$_POST['surname'].'&tel='.str_replace(' ','+',$_POST['tel']).'&birth_date='.$_POST['birth_date'].'&error=mailexist');
            }else{
               update_data();
               header('location: data_volunteer.php?statut=success');
            }
            $mailexist->closeCursor();
        }else{
            update_data();
            header('location: data_volunteer.php?statut=success');
        }
    }

    if(isset($_POST['update_password'])){
        if($_POST['new_password'] != $_POST['new_password_repeated']){
            header('location: data_volunteer.php?&error=samepassword');
        }else{
            if(!password_verify($_POST['old_password'],$data_volunteer['password'])){
                header('location: data_volunteer.php?&error=password');
            }else{
                $new_password = password_hash($_POST['new_password'], PASSWORD_BCRYPT);
                $req = $db->prepare('UPDATE volunteers SET password=:password WHERE id_volunteer=:id');
                $req->execute(array(
                    'id' => $_SESSION['uuid'],
                    'password' => $new_password
                ));
                header('location: data_volunteer.php?statut=success');
            }
        }
    }
    if(isset($_POST['unsubscribe'])){
        $unsubscribe = $db->query('DELETE FROM messages WHERE messenger =\''.$_SESSION['uuid'].'\'');
        $unsubscribe = $db->query('DELETE FROM task_volunteer WHERE id_volunteer = \''.$_SESSION['uuid'].'\'');
        $unsubscribe = $db->query('DELETE FROM commissions_volunteers WHERE id_volunteer = \''.$_SESSION['uuid'].'\'');
        $unsubscribe = $db->query('DELETE FROM commissions_moderators WHERE id_moderator = \''.$_SESSION['uuid'].'\'');
        $unsubscribe = $db->query('DELETE FROM volunteers WHERE id_volunteer =\''.$_SESSION['uuid'].'\'');
        header('location: reception.php');
    }
    $volunteer->closeCursor();
?>
