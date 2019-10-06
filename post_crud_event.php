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

        if($_POST['begin_date']>$_POST['end_date'] || ($_POST['begin_date']==$_POST['end_date'] && $_POST['begin_time']>$_POST['end_time'])){
            header('location: liste_evenements.php?name='.str_replace(' ', '+', $_POST['name']).'&info='.str_replace(' ', '+', $_POST['info']).'&begin_date='.$_POST['begin_date'].'&begin_time='.$_POST['begin_time'].'&end_date='.$_POST['end_date'].'&end_time='.$_POST['end_time'].'&places='.str_replace(' ', '+', $_POST['places']).'&expected='.$_POST['expected'].'&error=date');
        }else{
            if($_POST['places']=='') $_POST['places']='mission bretonne';
            if($_POST['expected']=='') $_POST['expected']=5;

            if(isset($_POST['create_event'])){
                $uuid=uuid();
                $event = $db->prepare('INSERT INTO events VALUES(:id, :name, :info, :begin_date, :end_date, :places, :expected)');
                $event->execute(array(
                    'id'=>hex2bin(str_replace('-','',$uuid)),
                    'name'=>$_POST['name'],
                    'info'=>$_POST['info'],
                    'begin_date'=>$_POST['begin_date'].' '.$_POST['begin_time'],
                    'end_date'=>$_POST['end_date'].' '.$_POST['end_time'],
                    'places'=>$_POST['places'],
                    'expected'=>$_POST['expected']));
                header('location: list_events.php');
/*********************************INSERT EVENT_COMMISSION**********************************************/
                $commissions = $db->query('SELECT * FROM commissions WHERE active');
                while($data_commission = $commissions->fetch()){
                    if(isset($_POST[str_replace(' ', '_', $data_commission['name_commission'])])){
                      $event = $db->prepare('INSERT INTO event_commission (id_commission,id_event) VALUES(:id_commission, :id_event)');
                      $event->execute(array(
                          'id_commission' =>$data_commission['id_commission'],
                          'id_event'=>hex2bin(str_replace('-','',$uuid))
                          ));;
                    }
                }

                header('location: list_events.php');
            }

            if(isset($_POST['update_event'])){
                $event = $db->prepare('UPDATE events SET name_event = :name, info_event = :info, begin_datetime_event = :begin_date, end_datetime_event = :end_date, places_event = :places, expected_people = :expected WHERE hex(id_event) = :id');
                $event->execute(array(
                    'id'=>$_POST['id'],
                    'name'=>$_POST['name'],
                    'info'=>$_POST['info'],
                    'begin_date'=>$_POST['begin_date'].' '.$_POST['begin_time'],
                    'end_date'=>$_POST['end_date'].' '.$_POST['end_time'],
                    'places'=>$_POST['places'],
                    'expected'=>$_POST['expected']));

                    $commissions = $db->query('SELECT c.id_commission as id_commission,
                        c.name_commission as name_commission,
                        ec.id_event as id_event,
                        ec.id_commission as event_commission
                        FROM commissions c
                        LEFT JOIN event_commission ec ON c.id_commission = ec.id_commission
                        	AND hex(ec.id_event) = \''.$_POST['id'].'\'
                        WHERE c.active');
                    while($data_commission = $commissions->fetch()){
                        if(isset($_POST[str_replace(' ', '_', $data_commission['name_commission'])])){
                          if(is_null($data_commission['event_commission'])){
                          $event = $db->prepare('INSERT INTO event_commission (id_commission , id_event) VALUES (:id_commission , :id_event)');
                          $event->execute(array(
                              'id_commission' =>$data_commission['id_commission'],
                              'id_event'=>hex2bin($_POST['id'])
                              ));
                            }
                        } else {
                          if(!is_null($data_commission['event_commission'])) {
                            $event = $db->prepare('DELETE FROM event_commission WHERE id_commission = :id_commission AND id_event = :id_event');
                            $event->execute(array(
                                'id_commission' =>$data_commission['id_commission'],
                                'id_event'=>hex2bin($_POST['id'])
                                ));
                          }
                        }
                    }
                header('location: event_tasks.php?id='.$_POST['id']);
            }
        }
    }
?>
