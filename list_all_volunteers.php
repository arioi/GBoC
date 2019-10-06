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
                    <title>GBoC - Liste des bénévoles</title>
                </head>

                <body>
                    <?php include("menus.php"); ?>
                    <div id="corps">
                        <h1>Liste des bénévoles</h1>
                        <table>
                            <tr>
                                <th>Nom</th>
                                <th>Prénom</th>
                                <th>Mail</th>
                                <th>Numero de Téléphone</th>
                                <th>Date de Naissance</th>
                                <th>Commissions</th>
                                <th>rôle</th>
                            </tr>
                            <?php
                                $volunteers = $db->query('SELECT
                                  hex(v.id_volunteer) AS id_volunteer,
                                  name_volunteer,
                                  surname_volunteer,
                                  birth_date,
                                  number_tel,
                                  mail,
                                  role,
                                  GROUP_CONCAT(name_commission) name_commissions
                                  FROM volunteers v
                                  LEFT JOIN commissions_volunteers cv ON v.id_volunteer = cv.id_volunteer
                                  LEFT JOIN commissions c ON cv.id_commission = c.id_commission
                                  GROUP BY
                                    hex(v.id_volunteer),
                                    name_volunteer,
                                    surname_volunteer,
                                    birth_date,
                                    number_tel,
                                    mail,
                                    role
                                  ORDER BY name_volunteer, surname_volunteer');
                                while($data_volunteer = $volunteers->fetch()){
                                    //$commissions = $db->query('SELECT name_commission FROM commissions c INNER JOIN commissions_volunteers cv ON c.id_commission = cv.id_commission WHERE cv.id_volunteer = \''.$data_volunteer['id_volunteer'].'\'');?>
                                    <tr>
                                        <td><?php echo $data_volunteer['name_volunteer']?></td>
                                        <td><?php echo $data_volunteer['surname_volunteer']?></td>
                                        <td><?php echo $data_volunteer['mail']?></td>
                                        <td><?php echo $data_volunteer['number_tel']?></td>
                                        <td><?php echo date("d/m/Y", strtotime($data_volunteer['birth_date']))?></td>
                                        <td><?php echo $data_volunteer['name_commissions']; ?></td>
                                        <td><?php
                                        echo $data_volunteer['role'].'<br>';
                                        if($data_volunteer['role'] == 'MODERATOR' || $data_volunteer['role'] == 'ADMIN'){
                                            $moderator = $db->query('SELECT GROUP_CONCAT(name_commission) name_commission
                                            FROM commissions c
                                            INNER JOIN commissions_moderators cm
                                            ON c.id_commission = cm.id_commission
                                            WHERE hex(cm.id_moderator) = \''.$data_volunteer['id_volunteer'].'\' ');
                                            $data_moderator = $moderator->fetch();
                                            echo "(Responsable des commissions : ".$data_moderator['name_commission'];
                                            echo ')';
                                            $moderator->closeCursor();
                                        } ?>
                                        </td>
                                    </tr><?php
                                    $commissions->closeCursor();
                                }
                                $volunteers->closeCursor();
                            ?>
                        </table>
                    </div>
                    <footer id="pied_de_page"></footer>
                </body>
            </html>
            <?php
    }
?>
