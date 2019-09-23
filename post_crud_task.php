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
