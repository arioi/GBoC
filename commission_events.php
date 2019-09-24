<?php
    session_start();
    include("functions.php");
    if(!user_verified()){
        header('location: reception.php');
    }
    $db = connecting_db();
        if(!commission_verified($_GET['id'])){
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
                        <h3>Evénement(s) à venir</h3>
                        <table>
                            <tr>
                                <th>Nom</th>
                                <th>Description</th>
                                <th>Date et heure de début</th>
                                <th>Date et heure de fin</th>
                                <th>Lieux</th>
                                <th>Nombre de personne attendu</th>
                            </tr>
                            <?php $events=$db->query('SELECT DISTINCT
                                e.id_event,
                                e.name_event,
                                e.info_event,
                                e.begin_datetime_event,
                                e.end_datetime_event,
                                e.places_event ,
                                e.expected_people
                              FROM events AS e
                                INNER JOIN event_commission ec ON e.id_event = ec.id_event
                                WHERE end_datetime_event >= Curdate()
                                AND hex(ec.id_commission) = \''.$_GET['id'].'\' ');
                            while($data_event = $events->fetch()){
                                /*$data_event['commissions'] = str_replace('{', '(\'', $data_event['commissions']);
                                $data_event['commissions'] = str_replace(',', '\',\'', $data_event['commissions']);
                                $data_event['commissions'] = str_replace('}', '\')', $data_event['commissions']);
                                $commissions = $db->query('SELECT id_commission, name_commission FROM commissions WHERE id_commission IN'.$data_event['commissions']);
                                $data_commission = $commissions->fetch();*/?>
                                <tr>
                                    <td><?php echo $data_event['name_event'] ?></td>
                                    <td><?php echo $data_event['info_event'] ?></td>
                                    <td><?php echo date("d/m/Y H:i", strtotime($data_event['begin_datetime_event'])) ?></td>
                                    <td><?php echo date("d/m/Y H:i", strtotime($data_event['end_datetime_event'])) ?></td>
                                    <td><?php echo $data_event['places_event'] ?></td>
                                    <td><?php echo $data_event['expected_people'] ?></td>
                                    <td><form method="post" action=<?php echo '"commission_tasks.php?id_commission='.$_GET['id'].'&id_event='.bin2hex($data_event['id_event']).'"' ?>>
                                        <input class="table" type="submit" name="tasks" value="Voir les tâches">
                                    </form>
                                </tr>
                            <?php } ?>
                        </table>

                        <h3> Evénement passé</h3>
                        <table>
                            <tr>
                                <th>Nom</th>
                                <th>Description</th>
                                <th>Date et heure de début</td>
                                <th>Date et heure de fin</th>
                                <th>Lieux</th>
                                <th>Nombre de personne attendu</th>
                            </tr>
                            <?php $events=$db->query('SELECT DISTINCT
                                e.id_event,
                                e.name_event,
                                e.info_event,
                                e.begin_datetime_event,
                                e.end_datetime_event,
                                e.places_event ,
                                e.expected_people
                              FROM events AS e
                                INNER JOIN event_commission ec ON e.id_event = ec.id_event
                                WHERE end_datetime_event < Curdate()
                                AND hex(ec.id_commission) = \''.$_GET['id'].'\' ');
                            while($data_event = $events->fetch()){
                                /*$data_event['commissions'] = str_replace('{', '(\'', $data_event['commissions']);
                                $data_event['commissions'] = str_replace(',', '\',\'', $data_event['commissions']);
                                $data_event['commissions'] = str_replace('}', '\')', $data_event['commissions']);
                                $commissions = $db->query('SELECT id_commission, name_commission FROM commissions WHERE id_commission IN'.$data_event['commissions']);
                                $data_commission = $commissions->fetch();*/?>
                                <tr>
                                    <td><?php echo $data_event['name_event'] ?></td>
                                    <td><?php echo $data_event['info_event'] ?></td>
                                    <td><?php echo date("d/m/Y H:i", strtotime($data_event['begin_datetime_event'])) ?></td>
                                    <td><?php echo date("d/m/Y H:i", strtotime($data_event['end_datetime_event'])) ?></td>
                                    <td><?php echo $data_event['places_event'] ?></td>
                                    <td><?php echo $data_event['expected_people'] ?></td>
                                    <td><form method="post" action=<?php echo '"commission_tasks.php?id_commission='.$_GET['id'].'&id_event='.bin2hex($data_event['id_event']).'"' ?>>
                                        <input class="table" type="submit" name="tasks" value="Voir les tâches">
                                    </form>
                                </tr>
                            <?php } ?>
                        </table>
                    </div>
                </body>
            </html>
            <?php
    }
?>
