<?php
    session_start();
    include("functions.php");
    if(!user_verified()){
        header('location: reception.php?location=' . urlencode($_SERVER['REQUEST_URI']));
    }
    $db = connecting_db();
        if(!commission_verified($_GET['id'])){
            echo 'Vous n\'avez pas les droits pour accéder à cette page';
        }else{
            $commission = $db->query('SELECT * FROM commissions WHERE id_commission=\''.hex2bin($_GET['id']).'\'');
            $commission_volunteers = $db->query('SELECT * FROM commissions_volunteers WHERE id_commission=\''.hex2bin($_GET['id']).'\'');
            $commission_moderators = $db->query('SELECT * FROM commissions_moderators WHERE id_commission=\''.hex2bin($_GET['id']).'\'');
?>
        <!DOCTYPE html>
        <html>
            <head>
                <meta charset="utf-8" />
                <title>GBoC - Liste des bénévoles</title>
            </head>
            <body>
                <?php include("menus.php"); ?>
                <div id="corps">
                    <h1><?php
                        $data_commission = $commission->fetch();
                        echo"Liste des Bénévoles de la commission ".$data_commission['name_commission']?>
                    </h1>
                    <h3>Bénévoles voulant participer à la commission</h3>
                    <table>
                        <tr>
                            <th>Nom</th>
                            <th>Prénom</th>
                            <th>Mail</th>
                            <th>Numéro de Téléphone</th>
                            <th>Date de Naissance</th>
                            <th>Commissions</th>
                            <th>&nbsp;&nbsp;</th>
                        </tr>
                        <?php
                        $volunteers = $db->query('SELECT v.id_volunteer, v.name_volunteer, v.surname_volunteer, v.birth_date, v.number_tel, v.mail, v.role FROM volunteers v INNER JOIN commissions_volunteers cv ON v.id_volunteer = cv.id_volunteer WHERE cv.volunteer_activ = 0 AND cv.id_commission = \''.$data_commission['id_commission'].'\' ');
                        if ($volunteers->rowCount() > 0) {
                            while($data_volunteer = $volunteers->fetch()){
                              $id_volunteer = bin2hex($data_volunteer['id_volunteer']);
                                //$commissions = $db->query('SELECT name_commission FROM commissions c INNER JOIN commissions_volunteers cv ON c.id_commission = cv.id_commission  WHERE cv.id_volunteer = \''.$data_volunteer['id_volunteer'].'\' ');?>
                                <tr>
                               		<td><?php echo $data_volunteer['name_volunteer']?></td>
                                    <td><?php echo $data_volunteer['surname_volunteer']?></td>
                                    <td><?php echo $data_volunteer['mail']?></td>
                                    <td><?php echo $data_volunteer['number_tel']?></td>
                                    <td><?php echo date("d/m/Y", strtotime($data_volunteer['birth_date']))?></td>
                                    <td><?php //$data_commissions = $commissions->fetch();
                                    echo $data_commission['name_commission'];
                                    //while($data_commissions = $commissions->fetch()) echo ', '.$data_commissions['name_commission']?></td>
                                    <td>
                                        <form id="userWishlistComForm" class="myform" method="post" action="post_commission_volunteers.php">
                                            <input type="hidden" name="id_volunteer" value=<?php echo'"'.$id_volunteer.'"'?>>
                                            <input type="hidden" name="id_commission" value=<?php echo'"'.$_GET['id'].'"'?>>
                                            <!--<a id="waitingAdd" href="#" name="waitingAdd" title="Accepter" onclick="document.getElementById('userWishlistComForm').submit()">
                                                <i class="material-icons">add_circle</i>
                                            </a>
                                            <input type="hidden" name="waitingAdd" value="Accepter">-->
                                            <button type="submit" name="waitingAdd" value=" "><img src="img/valid.png" width="23" height="23" alt="submit" border="0" /></button>

                                            <!--<a id="waitingRemove" href="#" name="waitingRemove" title="Refuser" onclick="document.getElementById('userWishlistComForm').submit()">
                                                <i class="material-icons">cancel</i>
                                            </a>
                                            <input type="hidden" name="waitingRemove" value="Refuser">-->
                                            <button type="submit" name="waitingRemove" value=" "><img src="img/delete.png" width="23" height="23" alt="submit" border="0" /></button>
                                        </form>
                                    </td>
                                </tr><?php
                            }
                        } else { ?>
                            <tr>
                                <td colspan="7">Aucun bénévole ne souhaite rejoindre cette commission</td>
                            </tr><?php
                        }?>
                    </table>
                    <h3>Bénévoles participant à la commission</h3>
                    <table>
                        <tr>
                            <th>Nom</th>
                            <th>Prénom</th>
                            <th>Mail</th>
                            <th>Numéro de Téléphone</th>
                            <th>Date de Naissance</th>
                            <th>Commissions</th>
                            <th>&nbsp;&nbsp;</th>
                        </tr>
                        <?php
                        $volunteers = $db->query('SELECT v.id_volunteer, v.name_volunteer, v.surname_volunteer, v.birth_date, v.number_tel, v.mail, v.role FROM volunteers v INNER JOIN commissions_volunteers cv ON v.id_volunteer = cv.id_volunteer WHERE cv.volunteer_activ = 1 AND cv.id_commission = \''.$data_commission['id_commission'].'\' ');
                        if ($volunteers->rowCount() > 0) {
                            while($data_volunteer = $volunteers->fetch()){
                                //$commissions = $db->query('SELECT name_commission FROM commissions WHERE \''.$data_volunteer['id_volunteer'].'\' = ANY (volunteers)');?>
                                <tr>
                                    <td><?php echo $data_volunteer['name_volunteer']?></td>
                                    <td><?php echo $data_volunteer['surname_volunteer']?></td>
                                    <td><?php echo $data_volunteer['mail']?></td>
                                    <td><?php echo $data_volunteer['number_tel']?></td>
                                    <td><?php echo date("d/m/Y", strtotime($data_volunteer['birth_date']))?></td>
                                    <td><?php //$data_commissions = $commissions->fetch();
                                    echo $data_commission['name_commission'];
                                    //while($data_commissions = $commissions->fetch()) echo ', '.$data_commissions['name_commission']?></td>
                                    <td>
                                        <form id="unsuscribeComForm" class="myform" method="post" action="post_commission_volunteers.php">
                                            <input type="hidden" name="id_volunteer" value=<?php echo'"'.bin2hex($data_volunteer['id_volunteer']).'"'?>>
                                            <input type="hidden" name="id_commission" value=<?php echo'"'.$_GET['id'].'"'?>>
                                            <!-- <input type="submit" name="goodbye" value="Désincrire de la commission"> -->
                                            <input type='image' src='img/cancel.png' width='23' height='23' title="Désinscrire de la commission" onFocus='form.submit' name='btn_opentextbox'/>
                                            <input type="hidden" name="goodbye" value="Désincrire de la commission">
                                        </form>
                                    </td>
                                </tr><?php
                            }
                        } else { ?>
                            <tr>
                                <td colspan="7">Aucun bénévole n'a rejoint cette commission</td>
                            </tr><?php
                        }?>
                    </table>
                </div>
            </body>
        </html>
        <?php
    }
?>
