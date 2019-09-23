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
    }
    header('location: reception.php);
?>
