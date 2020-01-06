<?php
    session_start();
    include("functions.php");
    if(!user_verified()){
        header('location: reception.php?location=' . urlencode($_SERVER['REQUEST_URI']));
    }
    $db = connecting_db();
    $task = $db->query('SELECT
      hex(t.id_task) id_task,
      t.name_task,
      t.info_task,
      t.begin_datetime_task,
      t.end_datetime_task,
      t.places_task,
      t.max_volunteers,
      hex(t.id_event) id_event,
      hex(t.id_commission) id_commission,
      COUNT(tv.id_volunteer) as nb_volunteer
    FROM tasks t
    LEFT JOIN task_volunteer tv ON t.id_task = tv.id_task
    LEFT JOIN volunteers v ON tv.id_volunteer = v.id_volunteer
    WHERE hex(t.id_task) = \''.$_GET['id_task'].'\'
    GROUP BY id_task,
    name_task,
    info_task,
    begin_datetime_task,
    end_datetime_task,
    places_task,
    max_volunteers,
    hex(t.id_event),
    hex(t.id_commission)');
    $task = $task->fetch();
    $moderators = $db->query('SELECT id_commission FROM commissions_moderators WHERE hex(id_commission) = \''.$task['id_commission'].'\' AND id_moderator = \''.$_SESSION['uuid'].'\' ');
    $moderator = ($moderators->rowCount() > 0 || $_SESSION['role'] == 'ADMIN')
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
        <title>GBoC - Tâche</title>
        <link rel="stylesheet" type="text/css" href="stylechat.css">
        <link rel="stylesheet" type="text/css" href="jquery-ui-1.12.1/jquery-ui.css">
        <script src="http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
        <script src="jquery-ui-1.12.1/jquery-ui.js"></script>
        <!-- <script src="functions.js"></script> -->
        <script src="chat.js"></script>
        <script>
            $(function() {
                var benevoleArray = [];

                $.ajax({
                    url: "autocomplet.php",
                    type: "POST",
                    success:function(data,s){
                        //console.log(data);

                        var res = JSON.parse(data);

                        //console.log(res);

                        for (var i = res.length - 1; i >= 0; i--) {
                            benevoleArray.push({label:res[i].label,id:res[i].id});
                        };

                        //console.log(benevoleArray);
                    }
                });

                $("#selVolonteer").autocomplete({ // autocomplete Jquery-UI
                    source:benevoleArray,
                    select: function (event, ui){
                        $("#id_volunteer").attr({"value":ui.item.id});
                    }
                });
            });
        </script>
    </head>
    <body>
        <?php include("menus.php"); ?>
        <div id="corps">
            <h1>Tâche</h1>
            <h3>Evénement</h3>
            <table>
                <tr>
                    <th>Nom</th>
                    <th>Description</th>
                    <th>Date et heure de début</th>
                    <th>Date et heure de fin</th>
                    <th>Lieux</th>
                </tr>
                <?php $event=$db->query('SELECT * FROM events WHERE hex(id_event)=\''.$task['id_event'].'\'');
                $event = $event->fetch()?>
                <tr>
                    <td><?php echo $event['name_event'] ?></td>
                    <td><?php echo $event['info_event'] ?></td>
                    <td><?php echo date("d/m/Y H:i", strtotime($event['begin_datetime_event'])) ?></td>
                    <td><?php echo date("d/m/Y H:i", strtotime($event['end_datetime_event'])) ?></td>
                    <td><?php echo $event['places_event'] ?></td>
            </table>

            <h3>Tâche</h3>
            <?php if($moderator){ ?>
                <form method="post" action="post_crud_task.php" id="create_task">
                    Nom de la tâche:<br><br>
                    <input type="text" class="browser-default" name="name" required="" value=<?php echo '"'.$task['name_task'].'"'?> ><br>
                    Description de la tâche:<br><br>
                    <textarea rows="4" class="browser-default" cols="50" name="info" form="create_task"><?php echo $task['info_task'] ?></textarea><br>
                    <table id="dateTaskTable">
                        <tr>
                            <td>Date et heure de début:</td>
                            <td>Date et heure de fin:</td>
                        </tr>
                        <tr>
                            <td>
                                <input type="date" class="dateTaskInput" name="begin_date" required="" value=<?php echo '"'.date('Y-m-d', strtotime($task['begin_datetime_task'])).'"' ?>>
                                <input type="time" class="dateTaskInput" name="begin_time" required="" value=<?php echo '"'.date('H:i', strtotime($task['begin_datetime_task'])).'"' ?>>
                            </td>
                            <td>
                                <input type="date" class="dateTaskInput" name="end_date" required="" value=<?php echo '"'.date('Y-m-d', strtotime($task['end_datetime_task'])).'"' ?>>
                                <input type="time" class="dateTaskInput" name="end_time" required="" value=<?php echo '"'.date('H:i', strtotime($task['end_datetime_task'])).'"' ?>>
                            </td>
                        </tr>
                    </table>
                    Lieux de la tache (à la mission bretonne par défaut):<br><br>
                    <input type="text" class="browser-default" name="places" value=<?php echo '"'.$task['places_task'].'"' ?>><br>
                    Nombre de bénévole:
                    <input type="number" id="max_volunteers" class="browser-default max_volunteers" name="max_volunteers" required="" value=<?php echo '"'.$task['max_volunteers'].'"' ?>><br><br>
                    <input type="hidden" name="id_commission" value=<?php echo '"'.$task['id_commission'].'"'?>>
                    <input type="hidden" name="id_task" value=<?php echo '"'.$_GET['id_task'].'"'?>>
                    <input type="submit" name="update" value="Modifier la tache">
                </form>
            <?php }else{ ?>
                <table>
                    <tr>
                        <th>Nom</th>
                        <th>Description</th>
                        <th>Date et heure de début</th>
                        <th>Date et heure de fin</th>
                        <th>Lieux</th>
                        <th>Nombre de bénévoles manquant</th>
                    </tr>
                    <tr>
                        <td><?php echo $task['name_task']?></td>
                        <td><?php echo $task['info_task']?></td>
                        <td><?php echo date("d/m/Y H:i", strtotime($task['begin_datetime_task']))?></td>
                        <td><?php echo date("d/m/Y H:i", strtotime($task['end_datetime_task']))?></td>
                        <td><?php echo $task['places_task']?></td>
                        <td><?php echo $task['max_volunteers']-$task['nb_volunteer']?></td>
                    </tr>
                </table>
            <?php } ?>
            <h3>Bénévoles</h3>
            <!-- autosuggest pour rechercher facilement les bénévoles -->
            <form id="volunForm" method="POST" action="post_crud_task.php" class="ui-widget">
                Trouver un bénévole :<br><br>
                <input type="text" id="selVolonteer" class="browser-default no-autoinit" name="selVolonteer"> <!-- no-autoinit : pour que l'autoincrement materialize ne se mélange pas avec celui de jQuery-UI -->
                <input type="hidden" name="id_commission" value=<?php echo '"'.$task['id_commission'].'"'?>>
                <input type="hidden" id="id_volunteer" name="id_volunteer" value="">
                <input type="hidden" name="id_task" value=<?php echo '"'.$task['id_task'].'"'?>>
                <input type="submit" id="updateListVolonteer" name="updateListVolonteer" value="OK" />
            </form>
            <table id="volunteersTaskTable">
                <tr>
                    <th>Nom</th>
                    <th>Prénom</th>
                    <th>&nbsp;&nbsp;</th>
                </tr>
                <?php $volunteers=$db->query('SELECT hex(v.id_volunteer) id_volunteer, name_volunteer, surname_volunteer FROM volunteers v INNER JOIN task_volunteer tv ON v.id_volunteer = tv.id_volunteer WHERE hex(tv.id_task)=\''.$task['id_task'] .'\' ');
                    if ($volunteers->rowCount() > 0) {
                        while($volunteer=$volunteers->fetch()){ ?>
                            <tr>
                                <td><?php echo $volunteer['name_volunteer']?></td>
                                <td><?php echo $volunteer['surname_volunteer']?></td>
                                <?php if($moderator) { ?>
                                    <td>
                                        <form method="POST" id="unsuscribeForm" class="myform" action="post_crud_task.php">
                                            <input type="hidden" name="id_volunteer" value=<?php echo '"'.$volunteer['id_volunteer'].'"'?>>
                                            <input type="hidden" name="id_task" value=<?php echo '"'.$task['id_task'].'"'?>>
                                            <!-- <input type="submit" name="unsubscribe" value="désincrire de la tâche"> -->
                                            <input type='image' src='img/cancel.png' width='23' height='23' title="Se désengager" onFocus='form.submit' name='btn_opentextbox'/>
                                            <input type="hidden" name="unsubscribe" value="désincrire de la tâcher">
                                        </form>
                                    </td><?php
                                } ?>
                            </tr><?php
                        }
                    } else { ?>
                        <tr>
                            <td colspan="3">Aucun bénévole n'est inscrit pour cette tâche</td>
                        </tr><?php
                    }?>
            </table>

            <h3>Chat</h3>
            <table class="chat"><tr>
            <!-- zone des messages -->
                <td valign="top" id="text-td">
                    <div id="text">
                        <script>
                            var id_task = <?php echo json_encode($task['id_task']); ?>;
                            getMessages(id_task)</script>
                    </div>
                </td>
            </tr></table>

            <!--  Enfin, nous affichons la barre contenant la zone de texte pour taper le message et le bouton : -->

            <!--Zone de texte ////////////////////////////////////////////////////////-->
            <a name="post"></a>
            <table class="post_message"><tr>
                <td>
                <form action="" method="" onsubmit="postMessage(id_task); return false;">
                    <input type="text" id="message" maxlength="255" />
                    <input type="button" onclick="postMessage(id_task)" value="Envoyer" id="post" />
                </form>
                        <div id="responsePost" style="display:none"></div>
                </td>
            </tr></table>
        </div>
    </body>
</html>
