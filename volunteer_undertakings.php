<?php
    session_start();
    include("functions.php");
    if(!user_verified()){
        header('location: reception.php');
    }
    $db = connecting_db();
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>GBoC - Liste des engagements</title>
    </head>
    <body>
        <?php include("menus.php"); ?>
        <div id="corps">
            <h1>Liste des engagements</h1>
            <table id="volunteersUndertakingTable">
                <tr>
                    <th>Commission</th>
                    <th>événement</th>
                    <th>tâche</th>
                    <th>&nbsp;&nbsp;</th>
                    <th>&nbsp;&nbsp;</th>
                </tr>
                <?php $tasks=$db->query('SELECT hex(t.id_task) id_task, name_task, name_commission, name_event
                  FROM tasks t
                  INNER JOIN task_volunteer tv ON t.id_task = tv.id_task
                  INNER JOIN commissions c ON t.id_commission = c.id_commission
                  INNER JOIN events e ON t.id_event = e.id_event
                  WHERE hex(tv.id_volunteer) = \''.bin2hex($_SESSION['uuid']).'\'
                  ORDER BY t.begin_datetime_task');
                while($data_task = $tasks->fetch()){?>
                    <tr>
                        <td><?php echo $data_task['name_commission']?></td>
                        <td><?php echo $data_task['name_event']?></td>
                        <td><?php echo $data_task['name_task']?></td>
                        <td><a id="zoomIcon" href=<?php echo '"task.php?id_task='.$data_task['id_task'].'"'?> name="task" title="Voir la tâche"><i class="material-icons">zoom_in</i></a></td>
                        <td>
                            <form id="suscribeVUForm" class="myform" method="post" action="post_crud_task.php">
                                <input type="hidden" name="id_task" value=<?php echo'"'.$data_task['id_task'].'"'?>>
                                <input type="hidden" name="id_volunteer" value=<?php echo'"'.bin2hex($_SESSION['uuid']).'"'?>>
                                <!-- <input type="submit" name="unsubscribe" value="Se désincrire de la tache"> -->
                                <input type='image' src='img/cancel.png' width='23' height='23' title="Se désengager" onFocus='form.submit' name='btn_opentextbox'/>
                                <input type="hidden" name="unsubscribe" value="Se désincrire de la tache">
                            </form>
                        </td>
                    </tr>
                <?php } ?>
            </table>
        </div>
    </body>
</html>
