<?php
    session_start();
    include("functions.php");
    if(!user_verified()){
        header('location: reception.php');
    }
    $db = connecting_db();

    if($_SESSION['role'] != 'ADMIN'){
        echo 'Vous n\'avez pas les droits pour accéder à cette page';
    }else{

        if(isset($_POST['add_commission'])){
            $moderator = explode(" ",$_POST['moderator']);
            $moderator = substr($moderator[count($moderator)-1],1,-1);
            $moderator = $db->query('SELECT id_volunteer FROM volunteers WHERE mail=\''.$moderator.'\'');
            $moderator = $moderator->fetch();
            $id_moderator = bin2hex($moderator['id_volunteer']);
            $edit_role = $db->query('UPDATE volunteers SET role=\'MODERATOR\' WHERE role = \'VOLUNTEER\' AND id_volunteer=\''.hex2bin($id_moderator).'\'');
            $uuid = uuid();
            $add_commission = $db->prepare('INSERT INTO commissions (id_commission, name_commission) VALUES(:id_commission,:name_commission)');
            $add_commission->execute(array(
                ':id_commission' => hex2bin(str_replace('-','',$uuid)),
                ':name_commission' => ucwords ($_POST['name']," -'_/")
            ));
            $add_commission_moderator = $db->prepare('INSERT INTO commissions_moderators VALUES(:id_commission,:moderator)');
            $add_commission_moderator->execute(array(
                ':id_commission' => hex2bin(str_replace('-','',$uuid)),
                ':moderator' => $moderator['id_volunteer']
            ));
            $add_commission_volunteers = $db->prepare('INSERT INTO commissions_volunteers VALUES(:id_commission,:volunteer,TRUE)');
            $add_commission_volunteers->execute(array(
                ':id_commission' => hex2bin(str_replace('-','',$uuid)),
                ':volunteer' => $moderator['id_volunteer']
            ));
        }

        if(isset($_POST['add_moderator'])){
            $id_commission = hex2bin($_POST['id_commission']);
            $moderator = explode(" ",$_POST['moderator']);
            $moderator = substr($moderator[count($moderator)-1],1,-1);
            $moderator = $db->query('SELECT id_volunteer FROM volunteers WHERE mail=\''.$moderator.'\'');
            $commission = $db->query('SELECT active FROM commissions WHERE id_commission=\''.$id_commission.'\'');
            $commission = $commission->fetch();
            $moderator = $moderator->fetch();
            if($commission['active']) $edit_role = $db->query('UPDATE volunteers SET role=\'MODERATOR\' WHERE role = \'VOLUNTEER\' AND id_volunteer=\''.$moderator[id_volunteer].'\'');
            $add_commission_moderator = $db->prepare('INSERT INTO commissions_moderators VALUES(:id_commission,:moderator)');
            $add_commission_moderator->execute(array(
                ':id_commission' => $id_commission,
                ':moderator' => $moderator['id_volunteer']
            ));
        }

        if(isset($_POST['disable_commission'])){
            $disable_commission = $db->query('UPDATE commissions SET active = FALSE WHERE hex(id_commission) =\''.$_POST['id_commission'].'\'');
            $disable_commission_volunteers = $db->query('UPDATE commissions_volunteers SET volunteer_activ = FALSE WHERE hex(id_commission) =\''.$_POST['id_commission'].'\'');
            $delete_commission_moderator = $db->query('DELETE FROM commissions_moderators WHERE hex(id_commission) =\''.$_POST['id_commission'].'\'');
        }

        if(isset($_POST['reactivate_commission'])){
          $reactivate_commission = $db->query('UPDATE commissions SET active = TRUE WHERE hex(id_commission) =\''.$_POST['id_commission'].'\'');
          $reactivate_commission_volunteers = $db->query('UPDATE commissions_volunteers SET volunteer_activ = TRUE WHERE hex(id_commission) =\''.$_POST['id_commission'].'\'');

        }

        if(isset($_POST['remove_moderator'])){
            //$remove_moderator = $db->query('UPDATE commissions SET moderators = array_remove(moderators,\''.$_POST['id_moderator'] .'\') WHERE id_commission=\''.$_POST['id_commission'].'\'');
            $id_moderator = hex2bin($_POST['id_moderator']);
            $id_commission = hex2bin($_POST['id_commission']);
            $remove_moderator = $db->query('DELETE FROM commissions_moderators WHERE id_moderator = \''.$id_moderator.'\' AND id_commission = \''.$id_commission.'\'');
            $moderators = $db->query('SELECT COUNT(1) AS nb FROM commissions_moderators WHERE id_moderator =\''.$id_moderator.'\'');
            $nb_enregistrements = $moderators->fetch();
            if($nb_enregistrements['nb'] == 0){
              $edit_role = $db->query('UPDATE volunteers SET role=\'VOLUNTEER\' WHERE role = \'MODERATOR\' AND id_volunteer = \''.$id_moderator.'\'');
            }
        }
        header('location:'. $_SERVER['HTTP_REFERER']);
    }
?>
