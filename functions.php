<?php
    function uuid(){
        return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff), mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
    }

    function connecting_db(){
        try{
            $user = 'U3898583';
            $pass = 'AdminMBTav2229354456';
            $db = new PDO('mysql:host=rdbms.strato.de;dbname=DB3898583', $user, $pass, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
            //$db = new PDO('mysql:host=localhost;port=3306;dbname=gboc;user=root;password=');
            return $db;
        }catch (Exception $e){
            die('Erreur : ' . $e->getMessage());
        }
    }

    function urllink($content='') {
        $content = preg_replace('#(((https?://)|(w{3}\.))+[a-zA-Z0-9&;\#\.\?=_/-]+\.([a-z]{2,4})([a-zA-Z0-9&;\#\.\?=_/-]+))#i', '<a href="$0" target="_blank">$0</a>', $content);
        // Si on capte un lien tel que www.test.com, il faut rajouter le http://
        if(preg_match('#<a href="www\.(.+)" target="_blank">(.+)<\/a>#i', $content)) {
            $content = preg_replace('#<a href="www\.(.+)" target="_blank">(.+)<\/a>#i', '<a href="http://www.$1" target="_blank">www.$1</a>', $content);
            //preg_replace('#<a href="www\.(.+)">#i', '<a href="http://$0">$0</a>', $content);
        }

        $content = stripslashes($content);
        return $content;
    }

    function user_verified() {
        if(isset($_SESSION['uuid'])){
            $db = connecting_db();
            $volunteers = $db->prepare('SELECT id_volunteer FROM volunteers WHERE id_volunteer = :uuid');
            $volunteers->execute(array('uuid' => $_SESSION['uuid']));
            return ($volunteers->rowCount() > 0);
        }
        return false;
    }

    function commission_verified($id_commission) {
        $db = connecting_db();
        $id_user = bin2hex($_SESSION['uuid']);
        $moderator = $db->prepare('SELECT * FROM commissions_moderators WHERE id_commission = :id AND id_moderator = :id_user ');
        $moderator->execute(array(
            'id' => hex2bin($id_commission),
            'id_user' => hex2bin($id_user)));
        return ($_SESSION['role'] == 'ADMIN' || $moderator->rowCount() > 0);
    }

    function update_data(){
        $db = connecting_db();
        $update = $db->prepare('UPDATE volunteers SET name_volunteer=:name, surname_volunteer=:surname, number_tel=:number_tel, mail=:mail WHERE id_volunteer=:id');
        $update->execute(array(
            'id' => $_SESSION['uuid'],
            'name' => $_POST['name'],
            'surname' => $_POST['surname'],
            'number_tel' => $_POST['tel'],
            'mail' => $_POST['mail']
        ));
        $commissions = $db->query('SELECT * FROM commissions');
        //$add_volunteer = $db->prepare('UPDATE commissions_volunteers SET volunteers_waiting = array_append(volunteers_waiting, :uuid) WHERE id_commission=:id');
        //$remove_volunteer = $db->prepare('UPDATE commissions SET volunteers_waiting = array_remove(volunteers_waiting, :uuid), volunteers = array_remove(volunteers, :uuid) WHERE id_commission=:id');
        $check_inscription = $db->prepare('SELECT * FROM commissions_volunteers cv WHERE id_commission = :id_commission AND id_volunteer = :id_volunteer');
        $add_inscription = $db->prepare('INSERT INTO commissions_volunteers (id_commission, id_volunteer, volunteer_activ) VALUES(:id_commission, :id_volunteer, 0)');
        $delete_inscription = $db->prepare('DELETE FROM commissions_volunteers WHERE id_commission = :id_commission AND id_volunteer = :id_volunteer');
        while($data_commission = $commissions->fetch()){
            if(isset($_POST[str_replace(' ', '_',$data_commission['name_commission'])])) {
              $check_inscription->execute(array(
                'id_commission' => $data_commission['id_commission'],
                'id_volunteer' => $_SESSION['uuid']
              ));
              $nb_enregistrements = $check_inscription->fetch();
              if($nb_enregistrements['nb'] == 0) {
                $add_inscription->execute(array(
                  'id_commission' => $data_commission['id_commission'],
                  'id_volunteer' => $_SESSION['uuid']
                ));
              }
            }else{
              $check_inscription->execute(array(
                'id_commission' => $data_commission['id_commission'],
                'id_volunteer' => $_SESSION['uuid']
              ));
              $nb_enregistrements = $check_inscription->fetch();
              if($nb_enregistrements['nb'] > 0) {
                $delete_inscription->execute(array(
                  'id_commission' => $data_commission['id_commission'],
                  'id_volunteer' => $_SESSION['uuid']
                ));
              }
            }
        }
        return($_POST['name']);
    }
?>
