<?php
    session_start();
    include("functions.php");
    if(!user_verified()){
        header('location: reception.php?location=' . urlencode($_SERVER['REQUEST_URI']));
    }
    $db = connecting_db();
    $event = $db->query('SELECT * FROM events WHERE hex(id_event)=\''.$_GET['id_event'].'\'');
    $event = $event->fetch();
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8"/>
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>GBoC - Liste des tâches</title>
    </head>
    <body>
        <?php include("menus.php"); ?>
        <div id="corps">
            <h2>Liste des tâches</h2>
            <h3>Evénement</h3>
            <table>
                <tr>
                    <th>Nom</th>
                    <th>Description</th>
                    <th>Date et heure de début</th>
                    <th>Date et heure de fin</th>
                    <th>Lieux</th>
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
            $event_tasks = $db->query('SELECT DISTINCT c.name_commission, c.id_commission FROM tasks t INNER JOIN commissions c ON t.id_commission = c.id_commission WHERE hex(id_event)=\''.$_GET['id_event'].'\'
            AND t.id_commission IN (SELECT id_commission FROM commissions_volunteers WHERE hex(id_volunteer) = \''.bin2hex($_SESSION['uuid']).'\' AND volunteer_activ = TRUE)  ');
            while($task = $event_tasks->fetch()){
                //$tasks = $db->query('SELECT id_task, name_task, info_task, begin_time_task, end_time_task, places_task, max_volunteers, array_length(registered_volunteers,1) AS volunteers FROM tasks WHERE event = \''.$_GET['id_event'].'\' AND commission = \''.$data_commmission['id_commission'].'\' AND \''.$_SESSION['uuid'].'\' != ALL (registered_volunteers)')?>
                <h4>Commission <?php echo $task['name_commission']?></h4>
                <table>
                    <tr>
                        <th>Nom</th>
                        <th>Description</th>
                        <th>Date et heure de début</th>
                        <th>Date et heure de fin</th>
                        <th>Lieux</th>
                        <th>Nombre de bénévoles manquant</th>
						<th>&nbsp;&nbsp;</th>
                        <th>&nbsp;&nbsp;</th>
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
                        t.max_volunteers
                        ORDER BY t.begin_datetime_task ');
                    while($data_task=$commission_tasks->fetch()){?>
                        <tr>
                            <td><?php echo $data_task['name_task']?></td>
                            <td><?php echo $data_task['info_task']?></td>
                            <td><?php echo date("d/m/Y H:i", strtotime($data_task['begin_datetime_task']))?></td>
                            <td><?php echo date("d/m/Y H:i", strtotime($data_task['end_datetime_task']))?></td>
                            <td><?php echo $data_task['places_task']?></td>
                            <td><?php echo $data_task['max_volunteers']-$data_task['volunteers']?></td>
                            <td><a id="zoomIcon" href=<?php echo '"task.php?id_task='.$data_task['id_task'].'"'?> name="task" title="Voir la tâche" onclick="document.getElementById('formComTask').submit()"><i class="material-icons">zoom_in</i></a></td>
                            <td>
                                <form id="suscribeForm" class="myform" method="post" action="post_crud_task.php">
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
                                    if ($engagement == 1) { ?>
                                            <input type='image' src='img/cancel.png' width='23' height='23' title="Se désengager" onFocus='form.submit' name='btn_opentextbox'/>
                                          <!--  <a id="unsubscribe" href="#" name="unsubscribe" title="Se désengager" onclick="document.getElementById('suscribeForm').submit()">
                                                <i class="material-icons">cancel</i>
                                            </a>-->
                                            <input type="hidden" name="unsubscribe" value="Se désengager">
                                      <?php
                                    } else { ?>
                                        <input type='image' src='img/add.png' width='23' height='23' title="S'engager" onFocus='form.submit' name='btn_opentextbox'/>
                                        <!--<a id="undertaking" href="#" name="undertaking" title="S'engager" onclick="document.getElementById('suscribeForm').submit()">
                                            <i class="material-icons">add_circle</i>
                                        </a>-->
                                        <input type="hidden" name="undertaking" value="S'engager">
                                    <?php
                                    } ?>
                                </form>
                            </td>
                        </tr>
                    <?php } ?>
                </table>
            <?php } ?>
        </div>
    </body>
</html>
