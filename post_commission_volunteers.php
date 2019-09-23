<?php
    session_start();
    include("functions.php");
    if(!user_verified()){
        header('location: reception.php');
    }
    $db = connecting_db();
    if(!commission_verified($_POST['id_commission'])){
        echo 'Vous n\'avez pas les droits pour accéder à cette page';
    }else{
      $id_commission = hex2bin($_POST['id_commission']);
      $id_volunteer = hex2bin($_POST['id_volunteer']);
        $waiting = $db->query('SELECT LOWER(GROUP_CONCAT(hex(id_volunteer))) as volunteers
          FROM commissions_volunteers
          WHERE volunteer_activ = 0
          AND id_commission=\''.$id_commission.'\'');
        $waiting = $waiting->fetch();
        if(isset($_POST['waiting'])){
            if(!in_array($_POST['id_volunteer'], explode(",",$waiting['volunteers']))){
                header('location: commission_volunteers.php?id='.$_POST['id_commission'].'&error=nowait');
            }else{
                if($_POST['waiting'] == "Accepter"){
                    $add_volunteer = $db->query('UPDATE commissions_volunteers
                      SET volunteer_activ = 1
                      WHERE hex(id_volunteer) = \''.$_POST['id_volunteer'].'\'
                      AND hex(id_commission) = \''.$_POST['id_commission'].'\'');
                } else {
                    $remove_volunteer = $db->query('DELETE FROM commissions_volunteers
                      WHERE hex(id_volunteer) = \''.$_POST['id_volunteer'].'\'
                      AND hex(id_commission) = \''.$_POST['id_commission'].'\'');
                }
                header('location: commission_volunteers.php?id='.$_POST['id_commission']);
            }
        }
        if(isset($_POST['goodbye'])){
          $remove_volunteer = $db->query('DELETE FROM commissions_volunteers
            WHERE hex(id_volunteer) = \''.$_POST['id_volunteer'].'\'
            AND hex(id_commission) = \''.$_POST['id_commission'].'\'');
            header('location: commission_volunteers.php?id='.$_POST['id_commission']);
        }
    }
?>
