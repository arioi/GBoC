<?php
include("functions.php");

$db = connecting_db();

/* Pour chaque commission, on va vérifier si un évènement a été créé ou si un bénévole a demandé à faire parti de la commission*/
$commissions = $db->query('SELECT * FROM commissions WHERE active');
while ($commission = $commissions->fetch()){
  /* Récupérer les infos du des modérateur(s) */
  $moderators = $db->query('SELECT v.surname_volunteer, v.name_volunteer, v.mail FROM commissions_moderators cm INNER JOIN volunteers v ON cm.id_moderator = v.id_volunteer WHERE cm.id_commission = \''.$commission['id_commission'].'\'');
  while ($moderator = $moderators->fetch()){
    $to = $moderator['mail'];
    $subject = 'GBoC - Rapport hebdomadaire - Responsable';
    $message = nl2br("<h3>Les infos concernant la commission " .$commission['name_commission']. "</h3>");
    $message .= nl2br("Bonjour " .$moderator['surname_volunteer'] .",\n\n");

    /* Récupérer les infos sur les nouveaux bénévoles */
    $volunteers_waiting = $db->query('SELECT CONCAT(v.surname_volunteer, " ", v.name_volunteer) AS nom, v.mail, v.number_tel FROM commissions_volunteers cv INNER JOIN volunteers v ON cv.id_volunteer = v.id_volunteer WHERE cv.id_commission = \''.$commission['id_commission'].'\' AND volunteer_activ = 0');
    if($volunteers_waiting->rowCount() != 0){
      $message .= nl2br("Voici les bénévoles ayant demandé à rejoindre la commission " .$commission['name_commission']. ", merci de prendre rapidement contact avec eux : \n" );
      $message .= '<table>';
      while($volunteer_waiting = $volunteers_waiting->fetch()){
        $message .= '<tr><td>'.$volunteer_waiting['nom'].'</td><td>'.$volunteer_waiting['mail'].'</td><td>'.$volunteer_waiting['number_tel'].'</td><td><a href= "http://gestionbenevolesmbtav.fr/commission_volunteers.php?id='.bin2hex($commission['id_commission']).'">Valider ou pas l\'inscription</a></td></tr>';
      }
      $message .= '</table>';
      $message .= nl2br("\n\n");
    }

    /* Récupérer les infos sur les nouveaux évènements */
    $new_events = $db->query('SELECT e.name_event, e.begin_datetime_event, hex(e.id_event) as id_event
      FROM events e
      INNER JOIN event_commission ec ON ec.id_event = e.id_event AND ec.id_commission = \''.$commission['id_commission'].'\'
      LEFT JOIN tasks t ON ec.id_event = t.id_event AND t.id_commission = ec.id_commission
      WHERE t.id_event IS NULL AND e.end_datetime_event >= Curdate()
      AND hex(e.id_event) <> \'8A561691A32D45CEB4354AC64E30BB66\'');
    if($new_events->rowCount() != 0){
      $message .= nl2br("Voici les évènements à venir pour lesquels il n'y a aucune tâche : \n" );
      $message .= '<table>';
      while($new_event = $new_events->fetch()){
        $message .= '<tr><td>'.$new_event['name_event'].'</td><td>'.date('d/m/y',strtotime($new_event['begin_datetime_event'])).'</td><td><a href= "http://gestionbenevolesmbtav.fr/commission_tasks.php?id_commission='.bin2hex($commission['id_commission']).'&id_event='.$new_event['id_event'].'">Créer ses tâches</a></td></tr>';
      }
      $message .= '</table>';
      $message .= nl2br("\n\n");
    }

    /* Récupérer les infos sur les tâches en cours */
    $tasks = $db->query('SELECT
        hex(t.id_task) AS id_task,
        e.name_event,
        t.name_task,
        t.begin_datetime_task,
        t.max_volunteers,
        GROUP_CONCAT(CONCAT(v.surname_volunteer, " ", v.name_volunteer)) AS benevole
      FROM tasks t
      INNER JOIN events e ON t.id_event = e.id_event
      LEFT JOIN task_volunteer tv ON t.id_task = tv.id_task
      LEFT JOIN volunteers v ON tv.id_volunteer = v.id_volunteer
      WHERE t.end_datetime_task >= Curdate() AND t.id_commission = \''.$commission['id_commission'].'\'
      GROUP BY hex(t.id_task),
      e.name_event,
      t.name_task,
      t.begin_datetime_task,
      t.max_volunteers
      ORDER BY t.end_datetime_task');
    if($tasks->rowCount() != 0){
      $message .= nl2br("Voici l'état des lieux des tâches à venir : \n" );
      $message .= '<table rules="all" style="border-color: #666;" cellpadding="10"><tr><th>Evenement</th><th>Tâche</th><th>Date</th><th>Nbre souhaité</th><th>Bénévole(s) inscrit(s)</th></tr>';
      while($task = $tasks->fetch()){
        $message .= '<tr><td>'.$task['name_event'].'</td><td>'.$task['name_task'].'</td><td>'.date('d/m/y',strtotime($task['begin_datetime_task'])).'</td><td>'.$task['max_volunteers'].'</td><td>'.$task['benevole'].'</td><td><a href= "http://gestionbenevolesmbtav.fr/task.php?id_task='.$task['id_task'].'">Voir la tâche</a></td></tr>';
      }
      $message .= '</table>';
      $message .= nl2br("\n");
    }
  }
  $headers = "MIME-Version: 1.0" . "\n";
  $headers .= "Content-type: text/html; charset=utf-8" . "\r\n";

  // En-têtes additionnels
  $headers .= 'From: GBoC@mbtav.fr' . "\r\n";

  // Envoie
if($volunteers_waiting->rowCount() != 0 || $new_events->rowCount() != 0 || $tasks->rowCount() != 0){
    $resultat = mail($to, utf8_decode($subject), $message, $headers);
  }
}

?>
