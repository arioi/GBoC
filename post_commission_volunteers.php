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
                      $check_validation_account = $db->query('SELECT v.surname_volunteer, v.mail, c.name_commission
                        FROM volunteers v
                        INNER JOIN commissions_volunteers cv ON v.id_volunteer = cv.id_volunteer
                        INNER JOIN commissions c ON cv.id_commission = c.id_commission
                        WHERE hex(v.id_volunteer) = \''.$_POST['id_volunteer'].'\'
                        AND hex(cv.id_commission) = \''.$_POST['id_commission'].'\'
                        AND cv.volunteer_activ = 1');
                      if($check_validation_account->rowCount() > 0){
                       $volunteer = $check_validation_account->fetch();
                       $to      = $volunteer['mail'];
                       $subject = 'GBoC - Inscription Commission ' . $volunteer['name_commission'];
                       $message = nl2br("Bonjour " . $volunteer['surname_volunteer'] .
                       "\nTa demande d'inscription à la commission " . $volunteer['name_commission'] . " a été validée. Tu peux désormais t'engager sur les tâches de cette commission.
                       \n Pour information, voici les coordonnées du ou des responsable(s) de commission :
                         \n");
                       $message .= '<table>';
                       $rq = $db->query('SELECT CONCAT(v.name_volunteer, " ", v.surname_volunteer) AS Nom, v.mail AS Email, v.number_tel AS tel  FROM commissions_moderators cm INNER JOIN volunteers v ON cm.id_moderator = v.id_volunteer WHERE hex(cm.id_commission) = \''.$_POST['id_commission'].'\' ' );
                       while($tab = $rq->fetch()){
                        $message .= '<tr><td>'.$tab['Nom'].'</td><td>'.$tab['Email'].'</td><td>'.$tab['tel'].'</td></tr>';
                       }
                       $message .= '</table>';
                       $message .=nl2br("\nMerci pour ton implication \nBonne journée !");

                       $headers[] = 'MIME-Version: 1.0';
                       $headers[] = 'Content-type: text/html; charset=iso-8859-1';
                       $headers[] = 'From: GBoC@mbtav.bzh';

                       mail($to, $subject, $message, implode("\r\n", $headers));}
                } else {
                    $remove_volunteer = $db->query('DELETE FROM commissions_volunteers
                      WHERE hex(id_volunteer) = \''.$_POST['id_volunteer'].'\'
                      AND hex(id_commission) = \''.$_POST['id_commission'].'\'');
                      $check_devalidation_account = $db->query('SELECT v.surname_volunteer, v.mail
                        FROM volunteers v
                        LEFT JOIN commissions_volunteers cv ON v.id_volunteer = cv.id_volunteer
                        AND hex(cv.id_commission) = \''.$_POST['id_commission'].'\'
                        WHERE cv.id_volunteer IS NULL
                        AND hex(v.id_volunteer) = \''.$_POST['id_volunteer'].'\'');
                      $name_commission = $db->query('SELECT name_commission FROM commissions WHERE hex(id_commission) = \''.$_POST['id_commission'].'\'');
                      $name_commission = $name_commission->fetch();
                      if($check_devalidation_account->rowCount() > 0){
                       $volunteer = $check_devalidation_account->fetch();
                       $to      = $volunteer['mail'];
                       $subject = 'GBoC - Inscription Commission ' . $name_commission['name_commission'];
                       $message = nl2br("Bonjour " . $volunteer['surname_volunteer'] .
                       "\nTa demande d'inscription à la commission " . $name_commission['name_commission'] . " a été refusée.
                       \n Pour plus d'informations, voici les coordonnées du ou des responsable(s) de commission :
                         \n");
                       $message .= '<table>';
                       $rq = $db->query('SELECT CONCAT(v.name_volunteer, " ", v.surname_volunteer) AS Nom, v.mail AS Email, v.number_tel AS tel  FROM commissions_moderators cm INNER JOIN volunteers v ON cm.id_moderator = v.id_volunteer WHERE hex(cm.id_commission) = \''.$_POST['id_commission'].'\' ' );
                       while($tab = $rq->fetch()){
                        $message .= '<tr><td>'.$tab['Nom'].'</td><td>'.$tab['Email'].'</td><td>'.$tab['tel'].'</td></tr>';
                       }
                       $message .= '</table>';
                       $message .=nl2br("\nBonne journée !");

                       $headers[] = 'MIME-Version: 1.0';
                       $headers[] = 'Content-type: text/html; charset=iso-8859-1';
                       $headers[] = 'From: GBoC@mbtav.bzh';

                       mail($to, $subject, $message, implode("\r\n", $headers));}
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
