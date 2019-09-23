<?php
	session_start();
	include('functions.php');
	$db=connecting_db();
	/* On effectue la requête sur la table contenant les messages. Enfin, on affiche le tout. */
	$messages = $db->query("SELECT m.*, name_volunteer, surname_volunteer FROM messages AS m, volunteers WHERE recipient='".$_GET['id_task']."' AND messenger = id_volunteer ORDER BY time_message DESC");
	if($messages->rowCount() != 0){
		$json['messages'] = '<div id="messages_content">';
		// On crée un tableau qui continendra notre...tableau
		// Afin de placer les emssages en bas du chat
		// On triche un peu mais c'est plus simple :D
		$json['messages'] .= '<table><tr><td style="height:500px;" valign="bottom">';
		$json['messages'] .= '<table style="width:100%">';

		$i = 1;
		$e = 0;
		$prev = 0;
		$text ='';
		$prev = '0';
		while ($data_message = $messages->fetch()){
			// Change la couleur dès que l'ID du membre est différent du précédent
			if($i != 1) {
				$idNew = $data_message['messenger'];		
				if($idNew != $id){
					if($colId == 1) {
						$color = '#1b8e40';
						$colId = 0;
					}else{
						$color = '#64030b';
						$colId = 1;
					}
					$id = $idNew;
				}else $color = $color;
			}else{
				$color = '#64030b';
				$id = $data_message['messenger'];
				$colId = 1;
			}


			$text .= '<tr><td style="width:15%" valign="top">';
			// Si le dernier message est du même membre, on écrit pas de nouveau son pseudo
			if($prev != $data_message['messenger']){
				// contenu du message	
				//$text .= '<a href="#post" onclick="insertLogin(\''.addslashes($data_message['nom'].' '.$data_message['prenom']).'\')" style="color:black">';
				$text .= date('[d/m/Y H:i:s]', strtotime($data_message['time_message']));
				$text .= '&nbsp;<span style="color:'.$color.'">'.$data_message['name_volunteer'].' '.$data_message['surname_volunteer'].'</span>';
				$text .= '</a>';	
			}else
			$text .= '</td>';			
			$text .= '<td style="width:85%;padding-left:10px;" valign="top">';

				
			// On supprime les balises HTML
			$message = htmlspecialchars($data_message['message']); 

			// On transforme les liens en URLs cliquables
			$message = urllink($message);
				
			// On ajoute le message en remplaçant les liens par des URLs cliquables
			$text .= $message.'<br />';
			$text .= '</td></tr>';

			$i++;
			$prev = $data_message['messenger'];
		}
			
		/* On crée la colonne messages dans le tableau json
		qui contient l'ensemble des messages */
		$json['messages'] = $text;

		$json['messages'] .= '</table>';
		$json['messages'] .= '</td></tr></table>';
		$json['messages'] .= '</div>';
	} else {
		$json['messages'] = 'Aucun message n\'a été envoyé pour le moment.';
	}
	$messages->closeCursor();
	echo json_encode($json);
?>