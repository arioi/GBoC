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
        <title>GBoC - Liste des événements</title>
    </head>
    <body>
        <?php include("menus.php"); ?>
        <div id="corps">
            <h2 id="h2ve">Liste des événements</h2>
            <h3>Evénement à venir</h3>
            <table id="tableVe">
                <tr>
                    <th>Nom</th>
                    <th>Description</th>
                    <th>Date et heure de début</th>
                    <th>Date et heure de fin</th>
                    <th>Lieux</th>
                    <th>Commission(s)</th>
                    <th>&nbsp;&nbsp;</th>
                </tr>
                <?php $events=$db->prepare('SELECT DISTINCT
                    e.id_event,
                    e.name_event,
                    e.info_event,
                    e.begin_datetime_event,
                    e.end_datetime_event,
                    e.places_event ,
                    GROUP_CONCAT(DISTINCT c.name_commission SEPARATOR" ; ") AS Commissions
                  FROM events AS e
                    INNER JOIN tasks t ON e.id_event = t.id_event
                    INNER JOIN event_commission ec ON e.id_event = ec.id_event
                    INNER JOIN commissions_volunteers cv ON ec.id_commission = cv.id_commission
                    INNER JOIN commissions c ON ec.id_commission = c.id_commission
                  WHERE end_datetime_event > :today_date
                    AND hex(cv.id_volunteer) = :id_volunteer
                    GROUP BY e.id_event,
                    e.name_event,
                    e.info_event,
                    e.begin_datetime_event,
                    e.end_datetime_event,
                    e.places_event');
                $events->execute(array(
                    'today_date' => date("Y-m-d H:i"),
                    'id_volunteer' => bin2hex($_SESSION['uuid'])));
                while($data_event = $events->fetch()){ ?>
                    <tr>
                        <td><?php echo $data_event['name_event'] ?></td>
                        <td><?php echo $data_event['info_event'] ?></td>
                        <td><?php echo date("d/m/Y H:i", strtotime($data_event['begin_datetime_event'])) ?></td>
                        <td><?php echo date("d/m/Y H:i", strtotime($data_event['end_datetime_event'])) ?></td>
                        <td><?php echo $data_event['places_event'] ?></td>
                        <td><?php echo $data_event['Commissions'] ?></td>
                        <td><a id="zoomIcon" href=<?php echo '"volunteer_tasks.php?id_event='.bin2hex($data_event['id_event']).'"' ?> title="Voir la tâche"><i class="material-icons">zoom_in</i></a> <!-- pour format mobile-->
                            <!--<form id="myform" class="myform" method="post" action=<?php //echo '"volunteer_tasks.php?id_event='.bin2hex($data_event['id_event']).'"' ?>>
                                <a id="zoomIcon" href="#" title="Voir la tâche" onclick="document.getElementById('myform').submit()"><i class="material-icons">zoom_in</i></a>
                                <input id="seeTask" type="submit" name="taches" value="Voir les tâches">!-->
                            </form>
                        </td>
                    </tr>
                <?php } ?>
            </table>
        </div>
    </body>
</html>
