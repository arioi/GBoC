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
                            <td>Nom</td>
                            <td>Prénom</td>
                            <td>Mail</td>
                            <td>Numero de Téléphone</td>
                            <td>Date de Naissance</td>
                            <td>Commissions</td>
                        </tr>
                        <?php
                        $volunteers = $db->query('SELECT v.id_volunteer, v.name_volunteer, v.surname_volunteer, v.birth_date, v.number_tel, v.mail, v.role FROM volunteers v INNER JOIN commissions_volunteers cv ON v.id_volunteer = cv.id_volunteer WHERE cv.volunteer_activ = 0 AND cv.id_commission = \''.$data_commission['id_commission'].'\' ');
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
                                <td><form method="post" action="post_commission_volunteers.php">
                                    <input type="hidden" name="id_volunteer" value=<?php echo'"'.$id_volunteer.'"'?>>
                                    <input type="hidden" name="id_commission" value=<?php echo'"'.$_GET['id'].'"'?>>
                                    <input type="submit" name="waiting" value="Accepter">
                                    <input type="submit" name="waiting" value="Refuser">
                                </form></td>
                            </tr><?php
                        } ?>
                    </table>
                    <h3>Bénévoles participant à la commission</h3>
                    <table>
                        <tr>
                            <td>Nom</td>
                            <td>Prénom</td>
                            <td>Mail</td>
                            <td>Numero de Téléphone</td>
                            <td>Date de Naissance</td>
                            <td>Commissions</td>
                        </tr>
                        <?php
                        $volunteers = $db->query('SELECT v.id_volunteer, v.name_volunteer, v.surname_volunteer, v.birth_date, v.number_tel, v.mail, v.role FROM volunteers v INNER JOIN commissions_volunteers cv ON v.id_volunteer = cv.id_volunteer WHERE cv.volunteer_activ = 1 AND cv.id_commission = \''.$data_commission['id_commission'].'\' ');
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
                                <td><form method="post" action="post_commission_volunteers.php">
                                    <input type="hidden" name="id_volunteer" value=<?php echo'"'.bin2hex($data_volunteer['id_volunteer']).'"'?>>
                                    <input type="hidden" name="id_commission" value=<?php echo'"'.$_GET['id'].'"'?>>
                                    <input type="submit" name="goodbye" value="Désincrire de la commission">
                                </form></td>
                            </tr><?php
                        } ?>
                    </table>
                </div>
            </body>
        </html>
        <?php
    }
?>
