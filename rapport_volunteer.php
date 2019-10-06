<?php
include("functions.php");

$db = connecting_db();

/* Pour chaque volontaire, récupérer les commissions auquel il appartient*/
$volunteers = $db->query('SELECT * FROM volunteers');
while ($volunteer = $volunteers->fetch()){
  $x = 0;
  $to = $volunteer['mail'];
  $subject = 'GBoC - Rapport hebdomadaire - Bénévole';
  $message = nl2br("Bonjour " .$volunteer['surname_volunteer'] .",");
  /* Récupérer la liste des commissions */
  $commissions = $db->query('SELECT c.* FROM commissions_volunteers cv INNER JOIN commissions c ON cv.id_commission = c.id_commission WHERE hex(cv.id_volunteer) = \''.bin2hex($volunteer['id_volunteer']).'\'');
    if($commissions->rowCount() != 0){
  while ($commission = $commissions->fetch()){
//    print_r($commission);
    /* Récupérer les tâches en cours */
    $tasks = $db->query('SELECT e.name_event, t.name_task, t.begin_datetime_task, t.max_volunteers, hex(e.id_event) AS id_event, hex(t.id_task) id_task,
      COUNT(tv.id_volunteer),
      GROUP_CONCAT(CONCAT(v.surname_volunteer, " ", v.name_volunteer)) AS nom
      FROM tasks t
      INNER JOIN events e ON t.id_event = e.id_event
      LEFT JOIN task_volunteer tv ON t.id_task = tv.id_task
      LEFT JOIN volunteers v ON tv.id_volunteer = v.id_volunteer
      WHERE t.id_commission = \''.$commission['id_commission'].'\'
      AND t.end_datetime_task >= curdate()
      GROUP BY e.name_event, t.name_task, t.begin_datetime_task, t.max_volunteers, hex(e.id_event), hex(t.id_task)
      HAVING COUNT(tv.id_volunteer) < t.max_volunteers
      ORDER BY t.end_datetime_task');
    if($tasks->rowCount() != 0){
      $x = 1;
      $message .= nl2br("<h4>Les infos concernant la commission " .$commission['name_commission']. "</h4>");
      $message .= '<table rules="all" style="border-color: #666;" cellpadding="10"><tr><th>Evenement</th><th>Tâche</th><th>Date</th><th>Nbre souhaité</th><th>Bénévole(s) inscrit(s)</th></tr>';
      while($task = $tasks->fetch()){
        $volunteer_task = $db->query('SELECT * FROM task_volunteer WHERE hex(id_task) = \''.$task['id_task'].'\' AND hex(id_volunteer) = \''.bin2hex($volunteer['id_volunteer']).'\' ');
        if($volunteer_task->rowCount() != 0){
          $message .= '<tr><td>'.$task['name_event'].'</td><td>'.$task['name_task'].'</td><td>'.date('d/m/y',strtotime($task['begin_datetime_task'])).'</td><td>'.$task['max_volunteers'].'</td><td>'.$task['nom'].'</td><td><a href= "http://gestionbenevolesmbtav.fr/task.php?id_task='.$task['id_task'].'">Voir la tâche</a></td></tr>';
        } else {
            $message .= '<tr><td>'.$task['name_event'].'</td><td>'.$task['name_task'].'</td><td>'.date('d/m/y',strtotime($task['begin_datetime_task'])).'</td><td>'.$task['max_volunteers'].'</td><td>'.$task['nom'].'</td><td><a href= "http://gestionbenevolesmbtav.fr/volunteer_tasks.php?id_event='.$task['id_event'].'">S\'engager sur la tâche</a></td></tr>';
        }

      }
      $message .= '</table>';
      $message .= nl2br("\n");
    }
  }
  }
  $headers = "MIME-Version: 1.0" . "\n";
  $headers .= "Content-type: text/html; charset=utf-8" . "\r\n";

  // En-têtes additionnels
  $headers .= 'From: GBoC@mbtav.fr' . "\r\n";

  // Envoie
if($x == 1){
    $resultat = mail($to, utf8_decode($subject), $message, $headers);
  }
}

?>
