<?php
    session_start();
    include("functions.php");
    if(!user_verified()){
        header('location: reception.php');
    }
    $db = connecting_db();
    if(!commission_verified($_GET['id_commission'])){
        echo 'Vous n\'avez pas les droits pour accéder à cette page';
    }else{
      $commission = $db->query('SELECT name_commission FROM commissions WHERE hex(id_commission) = \''.$_GET['id_commission'].'\'');
      $commission = $commission->fetch();

        $event = $db->query('SELECT DISTINCT
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
            WHERE hex(e.id_event) = \''.$_GET['id_event'].'\'');
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
                            <th>Nom</th>
                            <th>Description</th>
                            <th>Date et heure de début</th>
                            <th>Date et heure de fin</th>
                            <th>Lieux</th>
                            <th>Nombre de personne attendu</th>
                            <th>Commissions participantes</th>
                        </tr>
                        <?php /*$event['commissions'] = str_replace('{', '(\'', $event['commissions']);
                        $event['commissions'] = str_replace(',', '\',\'', $event['commissions']);
                        $event['commissions'] = str_replace('}', '\')', $event['commissions']);
                        $commissions = $db->query('SELECT id_commission, name_commission FROM commissions WHERE id_commission IN'.$event['commissions']);*/?>
                        <tr>
                            <td><?php echo $event['name_event']?></td>
                            <td><?php echo $event['info_event']?></td>
                            <td><?php echo date("d/m/Y H:i", strtotime($event['begin_datetime_event']))?></td>
                            <td><?php echo date("d/m/Y H:i", strtotime($event['end_datetime_event']))?></td>
                            <td><?php echo $event['places_event']?></td>
                            <td><?php echo $event['expected_people']?></td>
                            <td><?php echo $event['commissions']?></td>
                        </tr>
                    </table>

                    <h3>Tâches créées</h3>
                    <table>
                        <tr>
                            <th>Commission</th>
                            <th>Nom</th>
                            <th>Description</th>
                            <th>Date et heure de début</th>
                            <th>Date et heure de fin</th>
                            <th>Lieux</th>
                            <th>Nombre de bénévoles max</th>
                            <th>Nombre de bénévole inscrits</th>
                        </tr>
                        <?php
                        $tasks = $db->query('SELECT
                          t.id_task,
                          t.name_task,
                          t.info_task,
                          t.begin_datetime_task,
                          t.end_datetime_task,
                          t.places_task,
                          t.max_volunteers,
                          COUNT(tv.id_volunteer) as nb_volunteer
                        FROM tasks t
                        LEFT JOIN task_volunteer tv ON t.id_task = tv.id_task
                        WHERE hex(t.id_event) = \''.$_GET['id_event'].'\' AND hex(`id_commission`) = \''.$_GET['id_commission'].'\'
                        GROUP BY id_task,
                        name_task,
                        info_task,
                        begin_datetime_task,
                        end_datetime_task,
                        places_task,
                        max_volunteers ');
                        while($data_task=$tasks->fetch()){
                            if($data_task['nb_volunteer'] == NULL) $data_task['nb_volunteer'] = 0?>
                            <tr>
                                <td><?php echo $commission['name_commission'] ?></td>
                                <td><?php echo $data_task['name_task']?></td>
                                <td><?php echo $data_task['info_task']?></td>
                                <td><?php echo date("d/m/Y H:i", strtotime($data_task['begin_datetime_task']))?></td>
                                <td><?php echo date("d/m/Y H:i", strtotime($data_task['end_datetime_task']))?></td>
                                <td><?php echo $data_task['places_task']?></td>
                                <td><?php echo $data_task['max_volunteers']?></td>
                                <td><?php echo $data_task['nb_volunteer']?></td>
                                <td><form method="post" action=<?php echo '"task?id_task='.bin2hex($data_task['id_task']).'"'?>>
                                    <input class = "table" type="submit" value="Voir la tâche">
                                </form></td>
                            </tr>
                        <?php } ?>
                    </table>

                    <?php if($event['end_datetime_event'] >= date("Y-m-d H:i")){ ?>
                        <h3>Créer une nouvelle tâche <?php echo strtolower($commission['name_commission']) ?> pour l'événement <?php echo strtolower($event['name_event']) ?></h3>
                        <form method="post" action="post_crud_task.php" id="create_task">
                            Nom de la tâche:<br>
                            <input type="text" name="name" required=""<?php if(isset($_GET['name'])) echo 'value="'.str_replace('+',' ',$_GET['name']).'"' ?> ><br>
                            Description de la tâche:<br>
                            <textarea rows="4" cols="50" name="info" form="create_task"><?php if(isset($_GET['info'])) echo str_replace('+',' ',$_GET['info'])?></textarea><br>
                            Date et heure de début:<br>
                            <input type="date" name="begin_date" required=""<?php if(isset($_GET['begin_date'])){
                              echo 'value="'.$_GET['begin_date'].'"';
                            }  else {
                              echo 'value="'.date('Y-m-d', strtotime($event['begin_datetime_event'])).'"';
                            } ?>>
                            <input type="time" name="begin_time" required=""<?php if(isset($_GET['begin_time'])){
                              echo 'value="'.$_GET['begin_time'].'"';
                            }  else {
                              echo 'value="'.date('H:i', strtotime($event['begin_datetime_event'])).'"';
                            }  ?>><br>
                            Date et heure de fin:<br>
                            <input type="date" name="end_date" required=""<?php if(isset($_GET['end_date'])) {
                              echo 'value="'.$_GET['end_date'].'"';
                            }  else {
                              echo 'value="'.date('Y-m-d', strtotime($event['end_datetime_event'])).'"';
                            }  ?>>
                            <input type="time" name="end_time" required=""<?php if(isset($_GET['end_time'])) {
                              echo 'value="'.$_GET['end_time'].'"';
                            }  else {
                              echo 'value="'.date('H:i', strtotime($event['end_datetime_event'])).'"';
                            }  ?>><br>
                            Lieux de la tache (à la mission bretonne par défaut):<br>
                            <input type="text" name="places"<?php if(isset($_GET['places'])) echo 'value="'.str_replace('+',' ',$_GET['places']).'"' ?> ><br>
                            Nombre de bénévole:<br>
                            <input type="number" name="max_volunteers" required=""<?php if(isset($_GET['max_volunteers'])) echo 'value="'.$_GET['max_volunteers'].'"' ?>><br>
                            <input type="hidden" name="id_commission" value=<?php echo '"'.$_GET['id_commission'].'"'?>>
                            <input type="hidden" name="id_event" value=<?php echo '"'.$_GET['id_event'].'"'?>>
                            <input class="form" type="submit" name="create" value="Créer la tache">
                        </form>
                        <?php if(isset($_GET['error']) && $_GET['error'] == 'date'){
                            echo "Attention, la tâche se termine avant qu'elle ne commence";
                        }
                    }?>
                </div>
            </body>
        </html>
        <?php
    }
?>
