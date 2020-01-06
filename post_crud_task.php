<?php
    session_start();
    include("functions.php");
    if(!user_verified()){
        header('location: reception.php');
    }
    $db = connecting_db();

    if(!commission_verified($_POST['id_commission'])){
        echo 'Vous n\'avez pas les droits pour accéder à cette page';
    }else if (isset($_POST['update']) || isset($_POST['create'])){
        if($_POST['begin_date']>$_POST['end_date'] || ($_POST['begin_date']==$_POST['end_date'] && $_POST['begin_time']>$_POST['end_time'])){
            header('location: commission_taches.php?id_event='.$_POST['id_event'].'&id_commission='.$_POST['id_commission'].'&name='.str_replace(' ', '+', $_POST['name']).'&info='.str_replace(' ', '+', $_POST['info']).'&begin_date='.$_POST['begin_date'].'&begin_time='.$_POST['begin_time'].'&end_date='.$_POST['end_date'].'&end_time='.$_POST['end_time'].'&places='.str_replace(' ', '+', $_POST['places']).'&max_volunteers='.$_POST['max_volunteers'].'&error=date');
        }else{
            if(isset($_POST['create'])){
                $uuid=uuid();
                if($_POST['places']=='') $_POST['places']='mission bretonne';
                $evenement = $db->prepare('INSERT INTO tasks VALUES(:id, :event, :commission, :name, :info, :begin_date, :end_date, :places, :max_volunteers)');
                $evenement->execute(array(
                    'id'=>hex2bin(str_replace('-','',$uuid)),
                    'event' => hex2bin($_POST['id_event']),
                    'commission' => hex2bin($_POST['id_commission']),
                    'name'=>$_POST['name'],
                    'info'=>$_POST['info'],
                    'begin_date'=>$_POST['begin_date'].' '.$_POST['begin_time'],
                    'end_date'=>$_POST['end_date'].' '.$_POST['end_time'],
                    'places'=>$_POST['places'],
                    'max_volunteers'=>$_POST['max_volunteers']));

                    $volunteers = $db->query('SELECT v.surname_volunteer, v.name_volunteer, v.mail FROM commissions_volunteers cv INNER JOIN volunteers v ON cv.id_volunteer = v.id_volunteer WHERE cv.id_commission = \''.hex2bin($_POST['id_commission']).'\'');
                    while ($volunteer = $volunteers->fetch()){
                      $message = '';
                      $to = $volunteer['mail'];
                      $subject = 'GBoC - Nouvelle tâche créée';
                      $message .= nl2br("Bonjour " .$volunteer['surname_volunteer'] .",\n\n");
                      $message .= nl2br("Une nouvelle tâche a été créée en urgence, merci d'en prendre connaissance : \n" );
                      $message .= '<table>';
                      $message .= '<tr><td>'.$_POST['name'].'</td><td>'.date('d/m/y',strtotime($_POST['begin_date'].' '.$_POST['begin_time'])).'</td><td><a href= "http://gestionbenevolesmbtav.fr/volunteer_tasks.php?id_event='.$_POST['id_event'].'">Voir la tâche</a></td></tr>';
                      $message .= '</table>';
                      $message .= nl2br("\n\n");

                    $headers = "MIME-Version: 1.0" . "\n";
                    $headers .= "Content-type: text/html; charset=utf-8" . "\r\n";

                    // En-têtes additionnels
                    $headers .= 'From: GBoC@mbtav.fr' . "\r\n";
                    if( ((strtotime($_POST['begin_date']) - time()) / (60 * 60 * 24)) < 8 ){
                        $resultat = mail($to, utf8_decode($subject), $message, $headers);
                    }
                  }
                header('location: commission_tasks.php?id_event='.$_POST['id_event'].'&id_commission='.$_POST['id_commission']);
            }

            if(isset($_POST['update'])){
                $update_task = $db->prepare('UPDATE tasks SET name_task = :name, info_task = :info, begin_datetime_task = :begin_date, end_datetime_task = :end_date, places_task = :places, max_volunteers = :max_volunteers WHERE hex(id_task) = :id');
                $update_task->execute(array(
                    'id'=>$_POST['id_task'],
                    'name'=>$_POST['name'],
                    'info'=>$_POST['info'],
                    'begin_date'=>$_POST['begin_date'].' '.$_POST['begin_time'],
                    'end_date'=>$_POST['end_date'].' '.$_POST['end_time'],
                    'places'=>$_POST['places'],
                    'max_volunteers'=>$_POST['max_volunteers']));
                header('location: task.php?id_task='.$_POST['id_task']);
            }
        }
    }
    if (isset($_POST['RemoveTask'])) {
      $delete_volunteers = $db->prepare('DELETE FROM task_volunteer WHERE hex(id_task) = :task');
      $delete_volunteers->execute(array(
        'task' => $_POST['id_task']));
      $delete_task = $db->prepare('DELETE FROM tasks WHERE hex(id_task) = :task');
      $delete_task->execute(array(
        'task' => $_POST['id_task']));
      header('location: commission_tasks.php?id_commission='.$_POST['id_commission'].'&id_event='.$_POST['id_event']);
    }

    if(isset($_POST['undertaking'])){
        $add_volunteer = $db->query('INSERT INTO task_volunteer (id_volunteer, id_task) VALUES (UNHEX(\''.$_POST['id_volunteer'].'\'), UNHEX(\''.$_POST['id_task'].'\'))');
        header('location: volunteer_tasks.php?id_event='.$_POST['id_event']);
    }

    if(isset($_POST['unsubscribe'])){
        $remove_volunteer = $db->query('DELETE FROM task_volunteer WHERE hex(id_volunteer) = \''.$_POST['id_volunteer'].'\' AND HEX(id_task) = \''.$_POST['id_task'].'\'');
        header('location:'. $_SERVER['HTTP_REFERER']);
        //header('location: volunteer_undertakings.php');
    }

?>
