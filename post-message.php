<?php
	session_start();
	include('functions.php');
	$db=connecting_db();

	if(user_verified()){
		if(isset($_POST['message']) AND !empty($_POST['message'])){	
			/* On teste si le message ne contient que des espaces, ou s'il est vide. 
				^ -> début de la chaine - $ -> fin de la chaine
				[ ] -> espace, rien ou point 
				+ -> une ou plusieurs fois
			Si c'est le cas, alors on envoie pas le message */
			if(!preg_match("#^[ ]+$#", $_POST['message'])){
				$insert = $db->prepare('
					INSERT INTO messages VALUES(:id, :messanger, :recipient, :time, :message)');
				$insert->execute(array(
					'id' => uuid(),
					'messanger' => $_SESSION['uuid'],
					'recipient' => $_POST['id_task'],
					'time' => date('Y-m-d H:i:s'),
					'message' => $_POST['message']
				));
				echo true;
			}else echo 'Votre message est vide.';	
		}else echo 'Votre message est vide.';	
	}else echo 'Vous devez être connecté.';	
?>