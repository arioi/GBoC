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
                    <h1>Liste des événements</h1>
                    <h3>Créer un événement</h3>
                    <form method="post" action="post_crud_event.php" id="create_event">
                        Nom de l'événement:<br>
                        <input type="text" name="name" class="browser-default" required=""<?php if(isset($_GET['name'])) echo 'value="'.str_replace('+',' ',$_GET['name']).'"' ?> ><br>
                        Description de l'événement:<br>
                        <textarea class="browser-default" rows="4" cols="50" name="info" form="create_event"><?php if(isset($_GET['info'])) echo str_replace('+',' ',$_GET['info'])?></textarea><br>
                        Date et heure de début:<br>
                        <input type="date" id="listEventBegDateInput" class="browser-default dateTimeInput" name="begin_date" required=""<?php if(isset($_GET['begin_date'])) echo 'value="'.$_GET['begin_date'].'"' ?>>
                        <input type="time" id="listEventBegTimeInput" class="browser-default dateTimeInput" name="begin_time" required=""<?php if(isset($_GET['begin_time'])) echo 'value="'.$_GET['begin_time'].'"' ?>><br>
                        Date et heure de fin:<br>
                        <input type="date" id="listEventFinDateInput" class="browser-default dateTimeInput" name="end_date" required=""<?php if(isset($_GET['end_date'])) echo 'value="'.$_GET['end_date'].'"' ?>>
                        <input type="time" id="listEventFinTimeInput" class="browser-default dateTimeInput" name="end_time" required=""<?php if(isset($_GET['end_time'])) echo 'value="'.$_GET['end_time'].'"' ?>><br>
                        Lieux de l'événement (à la mission bretonne par défaut):<br>
                        <input type="text" class="browser-default" name="places"<?php if(isset($_GET['places'])) echo 'value="'.str_replace('+',' ',$_GET['places']).'"' ?> ><br>
                        Nombre de personne attendu:<br>
                        <input type="number" id="max_volunteersListEvent" class="browser-default max_volunteers" name="expected"<?php if(isset($_GET['expected'])) echo 'value="'.$_GET['expected'].'"' ?>><br>
                        Commissions participantes:<br>
                        <?php
                            $commissions = $db->query('SELECT * FROM commissions WHERE active');
                            while($data_commission = $commissions->fetch()){
                                echo '<input type="checkbox" name ="'.$data_commission['name_commission'].'" value="'.bin2hex($data_commission['id_commission']).'">'.$data_commission['name_commission'].'<br>';
                            }
                        ?>
                        <input type="submit" name="create_event" value="Créer l'événement">
                    </form>
                    <?php if(isset($_GET['error']) && $_GET['error'] == 'date'){
                        echo "Attention, l'événement se termine avant qu'il ne commence";
                    }?>

                    <h3>Evénement(s) à venir</h3>
                    <table>
                        <tr>
                            <th>Nom</th>
                            <th>Description</th>
                            <th>Date et heure de début</th>
                            <th>Date et heure de fin</th>
                            <th>Lieu(x)</th>
                            <th>Nombre de personne attendu</th>
                            <th>Commissions participantes</th>
                            <th>&nbsp;&nbsp;</th>
                            <th>&nbsp;&nbsp;</th>
                        </tr>
                        <?php $events=$db->query('SELECT DISTINCT
                            e.id_event,
                            e.name_event,
                            e.info_event,
                            e.begin_datetime_event,
                            e.end_datetime_event,
                            e.places_event ,
                            e.expected_people,
                            GROUP_CONCAT(c.name_commission SEPARATOR" ; ") as commissions
                          FROM events AS e
                            INNER JOIN event_commission ec ON e.id_event = ec.id_event
                            INNER JOIN commissions c ON ec.id_commission = c.id_commission
                            WHERE end_datetime_event >= Curdate()
                            GROUP BY e.id_event,
                            e.name_event,
                            e.info_event,
                            e.begin_datetime_event,
                            e.end_datetime_event,
                            e.places_event ,
                            e.expected_people');
                        while($data_event = $events->fetch()){
                            /*$data_event['commissions'] = str_replace('{', '(\'', $data_event['commissions']);
                            $data_event['commissions'] = str_replace(',', '\',\'', $data_event['commissions']);
                            $data_event['commissions'] = str_replace('}', '\')', $data_event['commissions']);*/
                            //$commissions = $db->query('SELECT c.id_commission, c.name_commission FROM commissions c INNER JOIN event_commission ec ON c.id_commission = ec.id_commission WHERE id_event = \''.$data_event['id_event'].'\'' );?>
                            <tr>
                                <td><?php echo $data_event['name_event']?></td>
                                <td><?php echo $data_event['info_event']?></td>
                                <td><?php echo date("d/m/Y H:i", strtotime($data_event['begin_datetime_event']))?></td>
                                <td><?php echo date("d/m/Y H:i", strtotime($data_event['end_datetime_event']))?></td>
                                <td><?php echo $data_event['places_event']?></td>
                                <td><?php echo $data_event['expected_people']?></td>
                                <td>
                                    <?php echo $data_event['commissions']
                                    //$data_commission = $commissions->fetch();
                                    //echo $data_commission['name_commission'];
                                    //while($data_commission = $commissions->fetch()) echo ', '.$data_commission['name_commission']?>
                                </td>
                                <td><a id="zoomIcon" href=<?php echo '"event_tasks.php?id='.bin2hex($data_event['id_event']).'"';?> name="task" title="Voir les tâches"><i class="material-icons">zoom_in</i></a></td>
                            </tr>
                        <?php } ?>
                    </table>

                    <h3>Evénement(s) passé(s)</h3>
                    <table>
                        <tr>
                            <th>Nom</th>
                            <th>Description</th>
                            <th>Date et heure de début</th>
                            <th>Date et heure de fin</th>
                            <th>Lieu(x)</th>
                            <th>Nombre de personnes attendues</th>
                            <th>Commissions participantes</th>
                            <th>&nbsp;&nbsp;</th>
                            <th>&nbsp;&nbsp;</th>
                        </tr>
                        <?php $events=$db->query('SELECT DISTINCT
                            e.id_event,
                            e.name_event,
                            e.info_event,
                            e.begin_datetime_event,
                            e.end_datetime_event,
                            e.places_event ,
                            e.expected_people,
                            GROUP_CONCAT(c.name_commission SEPARATOR" ; ") as commissions
                          FROM events AS e
                            INNER JOIN event_commission ec ON e.id_event = ec.id_event
                            INNER JOIN commissions c ON ec.id_commission = c.id_commission
                            WHERE end_datetime_event < Curdate()
                            GROUP BY e.id_event,
                            e.name_event,
                            e.info_event,
                            e.begin_datetime_event,
                            e.end_datetime_event,
                            e.places_event ,
                            e.expected_people');
                        while($data_event = $events->fetch()){
                            /*$data_event['commissions'] = str_replace('{', '(\'', $data_event['commissions']);
                            $data_event['commissions'] = str_replace(',', '\',\'', $data_event['commissions']);
                            $data_event['commissions'] = str_replace('}', '\')', $data_event['commissions']);
                            $commissions = $db->query('SELECT id_commission, name_commission FROM commissions WHERE id_commission IN'.$data_event['commissions']);*/?>
                            <tr>
                              <td><?php echo $data_event['name_event']?></td>
                              <td><?php echo $data_event['info_event']?></td>
                              <td><?php echo date("d/m/Y H:i", strtotime($data_event['begin_datetime_event']))?></td>
                              <td><?php echo date("d/m/Y H:i", strtotime($data_event['end_datetime_event']))?></td>
                              <td><?php echo $data_event['places_event']?></td>
                              <td><?php echo $data_event['expected_people']?></td>
                              <td>
                                  <?php echo $data_event['commissions']
                                  //$data_commission = $commissions->fetch();
                                  //echo $data_commission['name_commission'];
                                  //while($data_commission = $commissions->fetch()) echo ', '.$data_commission['name_commission']?>
                              </td>
                                <td><a id="zoomIcon" href=<?php echo '"event_tasks.php?id='.bin2hex($data_event['id_event']).'"';?> name="task" title="Voir les tâches"><i class="material-icons">zoom_in</i></a></td>
                                <td>
                                    <form id="userWishlistComForm" class="myform" method="post" action="post_crud_event.php">
                                        <input type="hidden" name="id_event" value=<?php echo'"'.bin2hex($data_event['id_event']).'"'?>>
                                        <button type="submit" name="RemoveEvent" title = "Supprimer l'événement'" value=" "><img src="img/delete.png" width="23" height="23" alt="submit" border="0" /></button>
                                    </form>
                                </td>
                            </tr>
                        <?php } ?>
                    </table>
                </div>
            </body>
        </html>
        <?php
    }
?>
