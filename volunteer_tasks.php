<?php
    session_start();
    include("functions.php");
    if(!user_verified()){
        header('location: reception.php');
    }
    $db = connecting_db();
    $event = $db->query('SELECT * FROM events WHERE hex(id_event)=\''.$_GET['id_event'].'\'');
    $event = $event->fetch();
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8"/>
        <title>GBoC - Liste des tâches</title>
    </head>
    <body>
        <?php include("menus.php"); ?>
        <div id="corps">
            <h1>Liste des tâches</h1>
            <h3>Evénement</h3>
            <table>
                <tr>
                    <td>Nom</td>
                    <td>Description</td>
                    <td>Date et heure de début</td>
                    <td>Date et heure de fin</td>
                    <td>Lieux</td>
                </tr>
                <tr>
                    <td><?php echo $event['name_event']?></td>
                    <td><?php echo $event['info_event']?></td>
                    <td><?php echo date("d/m/Y H:i", strtotime($event['begin_datetime_event']))?></td>
                    <td><?php echo date("d/m/Y H:i", strtotime($event['end_datetime_event']))?></td>
                    <td><?php echo $event['places_event']?></td>
                </tr>
            </table>
            <h3>Tâches</h3>
            <?php /*$event['commissions'] = str_replace('{', '(\'', $event['commissions']);
            $event['commissions'] = str_replace(',', '\',\'', $event['commissions']);
            $event['commissions'] = str_replace('}', '\')', $event['commissions']);*/
            $event_tasks = $db->query('SELECT c.name_commission, c.id_commission FROM tasks t INNER JOIN commissions c ON t.id_commission = c.id_commission WHERE hex(id_event)=\''.$_GET['id_event'].'\'
            AND t.id_commission IN (SELECT id_commission FROM commissions_volunteers WHERE id_volunteer = \''.$_SESSION['uuid'].'\') ');
            while($task = $event_tasks->fetch()){
                //$tasks = $db->query('SELECT id_task, name_task, info_task, begin_time_task, end_time_task, places_task, max_volunteers, array_length(registered_volunteers,1) AS volunteers FROM tasks WHERE event = \''.$_GET['id_event'].'\' AND commission = \''.$data_commmission['id_commission'].'\' AND \''.$_SESSION['uuid'].'\' != ALL (registered_volunteers)')?>
                <h4>Commission <?php echo $task['name_commission']?></h4>
                <table>
                    <tr>
                        <td>Nom</td>
                        <td>Description</td>
                        <td>Date et heure de début</td>
                        <td>Date et heure de fin</td>
                        <td>Lieux</td>
                        <td>Nombre de bénévoles manquant</td>
                    </tr>
                    <?php
                    $commission_tasks = $db->query('SELECT hex(t.id_task) id_task,
                      t.name_task,
                      t.info_task,
                      t.begin_datetime_task,
                      t.end_datetime_task,
                      t.places_task,
                      t.max_volunteers,
                      COUNT(id_volunteer) AS volunteers
                      FROM tasks t
                      LEFT JOIN task_volunteer tv ON t.id_task = tv.id_task
                      WHERE hex(id_event)=\''.$_GET['id_event'].'\'
                      AND t.id_commission = \''.$task['id_commission'].'\'
                      GROUP BY t.id_task,
                        t.name_task,
                        t.info_task,
                        t.begin_datetime_task,
                        t.end_datetime_task,
                        t.places_task,
                        t.max_volunteers');
                    while($data_task=$commission_tasks->fetch()){?>
                        <tr>
                            <td><?php echo $data_task['name_task']?></td>
                            <td><?php echo $data_task['info_task']?></td>
                            <td><?php echo date("d/m/Y H:i", strtotime($data_task['begin_datetime_task']))?></td>
                            <td><?php echo date("d/m/Y H:i", strtotime($data_task['end_datetime_task']))?></td>
                            <td><?php echo $data_task['places_task']?></td>
                            <td><?php echo $data_task['max_volunteers']-$data_task['volunteers']?></td>
                            <td><form method="post" action="post_crud_task.php">
                                <input type="hidden" name="id_volunteer" value=<?php echo'"'.bin2hex($_SESSION['uuid']).'"'?>>
                                <input type="hidden" name="id_task" value=<?php echo'"'.$data_task['id_task'].'"'?>>
                                <input type="hidden" name="id_event" value=<?php echo'"'.$_GET['id_event'].'"'?>>
                                <input type="hidden" name="id_commission" value=<?php echo'"'.bin2hex($task['id_commission']).'"'?>>
                                <?php $volunteers = $db->query('SELECT * FROM task_volunteer WHERE hex(id_task) = \''.$data_task['id_task'].'\'');
                                $engagement = 0;
                                while ($volunteer=$volunteers->fetch()){
                                  if ($volunteer['id_volunteer'] == $_SESSION['uuid']){
                                    $engagement = 1;
                                  }
                                }
                                  if ($engagement == 1){ ?>
                                      <input type="submit" name="unsubscribe" value="Se désengager">
                                  <?php
                                } else { ?>
                                  <input type="submit" name="undertaking" value="S'engager">
                                <?php
                                } ?>
                            </form></td>
                        </tr>
                    <?php } ?>
                </table>
            <?php } ?>
        </div>
    </body>
</html>
