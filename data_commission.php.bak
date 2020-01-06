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
        $id_commission = hex2bin($_GET['id_commission']);
        $commission = $db->query('SELECT * FROM commissions WHERE id_commission=\''.$id_commission.'\'');

        $volunteers = $db->query('SELECT * FROM commissions_volunteers WHERE id_commission =\''.$id_commission.'\'');
        if($commission->rowCount() == 0){
            header('location: list_commissions.php');
        }
        $commission = $commission->fetch();
?>
        <!DOCTYPE html>
        <html>
            <head>
                <meta charset="utf-8" />
                <title>GBoC - Commission</title>
            </head>
            <body>
                <?php include("menus.php"); ?>
                <div id="corps">
                    <table>
                        <tr>
                            <th>Chargé de commission</th>
                            <th>Mail</th>
                            <th>Numero de Téléphone</th>
                        </tr>
                        <?php
                        $moderators = $db->query('SELECT * FROM commissions_moderators WHERE id_commission =\''.$id_commission.'\'');
                        while($data_moderator = $moderators->fetch()){
                          $moderator = $db->query('SELECT * FROM volunteers WHERE id_volunteer =\''.$data_moderator['id_moderator'].'\'');
                          while ($info_moderator = $moderator->fetch()) {
                            ?>
                              <tr>
                                  <td><?php echo $info_moderator['name_volunteer'].' '.$info_moderator['surname_volunteer']?></td>
                                  <td><?php echo $info_moderator['mail']?></td>
                                  <td><?php echo $info_moderator['number_tel']?></td>
                                  <?php if($moderators->rowcount() > 1){?>
                                      <td><form method="post" action="post_crud_commission.php">
                                          <input type="hidden" name="id_moderator" value=<?php echo'"'.bin2hex($info_moderator['id_volunteer']).'"'?>>
                                          <input type="hidden" name="id_commission" value=<?php echo'"'.bin2hex($commission['id_commission']).'"'?>>
                                          <input class="table" type="submit" name="remove_moderator" value="Retirer moderateur">
                                      </form></td>
                                    }
                          <?php } ?>
                                <?php } ?>
                            </tr>
                        <?php } ?>
                    </table>

                    <?php $volunteers = $db->query('SELECT v.id_volunteer, v.name_volunteer, v.surname_volunteer, v.mail
                      FROM volunteers v
                      INNER JOIN commissions_volunteers cv ON v.id_volunteer = cv.id_volunteer
                        AND cv.volunteer_activ = TRUE
                      INNER JOIN commissions_moderators cm ON cv.id_commission = cm.id_commission
                      AND v.id_volunteer != cm.id_moderator
                      WHERE cv.id_commission = \''.$id_commission.'\' ');
                      ?>
                    <form method="post" action="post_crud_commission.php">
                        <input list="volunteers" name="moderator" size="40" required=""><br>
                        <datalist id="volunteers">
                            <?php
                            while($data_volunteer=$volunteers->fetch()){
                                echo '<option value="'.$data_volunteer['name_volunteer'].' '.$data_volunteer['surname_volunteer'].' ('.$data_volunteer['mail'].')"></option>';
                            } ?>
                        </datalist>
                        <input type="hidden" name="id_commission" value=<?php echo '"'.bin2hex($commission['id_commission']).'"'?>>
                        <input class = "form" type="submit" name="add_moderator" value="Ajouter un moderateur">
                    </form>

                    <?php if($commission['active']){ ?>
                        <form method="post" action=<?php echo '"commission_volunteers.php?id='.bin2hex($commission['id_commission']).'"'?>>
                            <input class="form" type="submit" value="Voir la liste des bénévoles participants">
                        </form>
                        <form method="post" action=<?php echo '"commission_events.php?id='.$commission['id_commission'].'"'?>>
                            <input class="form" type="submit" value="Voir la liste des tâches créées">
                        </form>
                    <?php } ?>
                    <form method="post" action="post_crud_commission.php">
                        <input type="hidden" name="id_commission" value=<?php echo '"'.bin2hex($commission['id_commission']).'"'?>>
                        <input class="form" type="submit" <?php if($commission['active']) echo 'name="disable_commission" value="Désactiver la commission"'; else echo 'name="reactivate_commission" value="Réactiver la commission"'?>>
                    </form>
                </div>
            </body>
        </html>
        <?php
    }
?>
