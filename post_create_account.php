<?php
    include("functions.php");
    $db = connecting_db();

    if($_POST['mail'] != $_POST['mail_repeated'] || $_POST['password'] != $_POST['password_repeated']){
        header('location: create_account.php?name='.str_replace(' ','+',$_POST['name']).'&surname='.$_POST['surname'].'&mail='.$_POST['mail'].'&tel='.str_replace(' ','+',$_POST['tel']).'&birth_date='.$_POST['birth_date'].'&error=notsame');
    }else{
        $volunteers = $db->prepare('SELECT * FROM volunteers WHERE mail=:mail');
        $volunteers->execute(array('mail'=>$_POST['mail']));
        if($volunteers->rowCount() > 0){
            header('location: create_account.php?name='.str_replace(' ','+',$_POST['name']).'&surname='.$_POST['surname'].'&mail='.$_POST['mail'].'&tel='.str_replace(' ','+',$_POST['tel']).'&birth_date='.$_POST['birth_date'].'&error=mailexist');
        }else{
            $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
            $uuid = uuid();

            $addvolunteer = $db->prepare('INSERT INTO volunteers VALUES(:id,:name,:surname,:birth_date,:tel,:mail,:password,\'VOLUNTEER\')');

            $addvolunteer->execute(array(
                ':id' =>  hex2bin(str_replace('-','',$uuid)),
                ':name' => strtoupper($_POST['name']),
                ':surname' =>ucwords ($_POST['surname']," -'_/"),
                ':birth_date' => $_POST['birth_date'],
                ':tel' => $_POST['tel'],
                ':mail' => $_POST['mail'],
                ':password' => $password
            ));
            $commissions = $db->query('SELECT * FROM commissions');
            $addcom = $db->prepare('INSERT INTO commissions_volunteers VALUES (:commission,:volunteer,FALSE)');
            while($data_commission = $commissions->fetch()){
                if(isset($_POST[$data_commission['name_commission']])){
                    $addcom->execute(array(
                        ':commission' =>$data_commission['id_commission'],
                        ':volunteer' => hex2bin(str_replace('-','',$uuid))
                    ));
                }
            }
            $commissions->closeCursor();
        }
        $volunteers->closeCursor();
        $check_account_create = $db->query('SELECT * FROM volunteers WHERE
        id_volunteer = \''.hex2bin(str_replace('-','',$uuid)).'\'');
        if($check_account_create->rowCount() > 0){
         $volunteer = $check_account_create->fetch();
         $to      = $volunteer['mail'];
         $subject = 'GBoC - Création de compte';
         $message = nl2br("Bonjour " . $volunteer['surname_volunteer'] . "\nTon inscription a bien été prise en compte. Tu recevras un mail prochainement quand ta demande pour une commission aura été validée par le responsable. \nBonne journée !");
         $headers[] = 'MIME-Version: 1.0';
         $headers[] = 'Content-type: text/html; charset=iso-8859-1';
         $headers[] = 'From: GBoC@mbtav.bzh';

         mail($to, $subject, $message, implode("\r\n", $headers));
        }
    }
    header('location: reception.php');
?>
