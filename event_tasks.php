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
                <title>GBoC - Liste des tâches</title>
            </head>
            <body>
                <?php include("menus.php"); ?>
                <div id="corps">
                    <?php
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
                        INNER JOIN commissions_volunteers cv ON ec.id_commission = cv.id_commission
                        INNER JOIN commissions c ON ec.id_commission = c.id_commission
                      WHERE hex(e.id_event) = \''.$_GET['id'].'\'');
                    $event = $event->fetch();
                    $tasks = $db->query('SELECT * FROM tasks WHERE hex(id_event) = \''.$_GET['id'].'\'');
                    if($event['end_datetime_event'] >= date("Y-m-d H:i")){
                    ?>
	                    <form method="post" action="post_crud_event.php" id="create_event">
	                        Nom de l'événement:<br>
	                        <input type="text" name="name" required="" <?php echo 'value="'.$event['name_event'].'"' ?> ><br>
	                        Description de l'événement:<br>
	                        <textarea rows="4" cols="50" name="info" form="create_event"><?php echo $event['info_event']?></textarea><br>
	                        <table>
	                        	<tr>
	                        		<td>Date et heure de début:</td>
	                        		<td>Date et heure de fin:</td>
	                        	</tr>
	                        	<tr>
	                        		<td><input type="date" name="begin_date" required=""<?php echo 'value="'.date('Y-m-d', strtotime($event['begin_datetime_event'])).'"' ?>>
	                        			<input type="time" name="begin_time" required=""<?php echo 'value="'.date('H:i', strtotime($event['begin_datetime_event'])).'"' ?>></td>
	                        		<td><input type="date" name="end_date" required=""<?php echo 'value="'.date('Y-m-d', strtotime($event['end_datetime_event'])).'"' ?>>
	                        			<input type="time" name="end_time" required=""<?php echo 'value="'.date('H:i', strtotime($event['end_datetime_event'])).'"' ?>></td>
	                        	</tr>
	                        </table>
	                        Lieux de l'événement (à la mission bretonne par défaut):<br>
	                        <input type="text" name="places"<?php echo 'value="'.$event['places_event'].'"' ?> ><br>
	                        Nombre de personne attendu:<br>
	                        <input type="number" name="expected"<?php echo 'value="'.$event['expected_people'].'"' ?>><br>
	                        Commissions participantes:<br>
	                        <?php
	                            $commissions = $db->query('SELECT c.name_commission, c.id_commission, ec.id_commission as event_commission, ec.id_event FROM commissions c LEFT JOIN event_commission ec ON c.id_commission = ec.id_commission AND hex(ec.id_event) = \''.$_GET['id'].'\'  WHERE active');

                              while($data_commission = $commissions->fetch()){
                                  echo '<input type="checkbox" name ="'.$data_commission['name_commission'].'" value="'.$data_commission['id_commission'].'"';
	                                if($data_commission['id_commission'] == $data_commission['event_commission']) echo "checked=\"\"";
	                                echo '>'.$data_commission['name_commission'];
	                            }
	                        ?>
	                        <br>
	                        <input type="hidden" name="id" value= <?php echo '"'.bin2hex($event['id_event']).'"'?>>
	                        <input type="submit" name="update_event" value="Modifier l'événement">
	                    </form>
	                <?php }else{ ?>
	                	<table>
	                        <tr>
	                            <td>Nom</td>
	                            <td>Description</td>
	                            <td>Date et heure de début</td>
	                            <td>Date et heure de fin</td>
	                            <td>Lieux</td>
	                            <td>Nombre de personne attendu</td>
	                            <td>Commissions participantes</td>
	                        </tr>
	                        <?php /*$event_commissions = str_replace('{', '(\'', $event['commissions']);
	                        $event_commissions = str_replace(',', '\',\'', $event_commissions);
	                        $event_commissions = str_replace('}', '\')', $event_commissions);
	                        $commissions = $db->query('SELECT id_commission, name_commission FROM commissions WHERE id_commission IN'.$event_commissions);*/?>
                          <tr>
                              <td><?php echo $event['name_event']?></td>
                              <td><?php echo $event['info_event']?></td>
                              <td><?php echo date("d/m/Y H:i", strtotime($event['begin_datetime_event']))?></td>
                              <td><?php echo date("d/m/Y H:i", strtotime($event['end_datetime_event']))?></td>
                              <td><?php echo $event['places_event']?></td>
                              <td><?php echo $event['expected_people']?></td>
                              <td>
                                  <?php echo $event['commissions']
                                  //$data_commission = $commissions->fetch();
                                  //echo $data_commission['name_commission'];
                                  //while($data_commission = $commissions->fetch()) echo ', '.$data_commission['name_commission']?>
                              </td>
	                        </tr>
                    	</table>
	                <?php } ?>

                    <h3>Liste des tâches</h3>
                    <?php /*$event['commissions'] = str_replace('{', '(\'', $event['commissions']);
		            $event['commissions'] = str_replace(',', '\',\'', $event['commissions']);
		            $event['commissions'] = str_replace('}', '\')', $event['commissions']);*/
		            $commissions = $db->query('SELECT c.id_commission, c.name_commission FROM commissions c INNER JOIN event_commission ec ON c.id_commission = ec.id_commission WHERE hex(id_event) = \''.$_GET['id'].'\'');
		            while($data_commmission = $commissions->fetch()){
		                $tasks = $db->query('SELECT id_task, name_task, info_task, begin_datetime_task, end_datetime_task, places_task, max_volunteers FROM tasks WHERE hex(id_event) = \''.$_GET['id'].'\' AND id_commission = \''.$data_commmission['id_commission'].'\'')?>
		                <h4>Commission <?php echo $data_commmission['name_commission']?></h4>
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
		                    while($data_task=$tasks->fetch()){?>
		                        <tr>
		                            <td><?php echo $data_task['name_task']?></td>
		                            <td><?php echo $data_task['info_task']?></td>
		                            <td><?php echo date("d/m/Y H:i", strtotime($data_task['begin_time_task']))?></td>
		                            <td><?php echo date("d/m/Y H:i", strtotime($data_task['end_time_task']))?></td>
		                            <td><?php echo $data_task['places_task']?></td>
		                            <td><?php echo $data_task['max_volunteers']-$data_task['volunteers']?></td>
		                            <td><form method="post" action=<?php echo "task.php?id_task=".$data_task['id_task'] ?>>
		                                <input type="submit" value="Voire la tache">
		                            </form></td>
		                        </tr>
		                    <?php } ?>
		                </table>
		            <?php } ?>
                </div>
            </body>
        </html>
        <?php
    }
?>
