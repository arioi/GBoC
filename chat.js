/*$.ajax({
	type: "GET",
	url: "page-a-appeler.php",
	data: "valeur="+valeur+"&nom="+nom,
	success: function(msg){
		$("#bloc").html(msg);
	}
});*/
/*function insertLogin(login){
	var $message = $("#message");
	$message.val($message.val() + login + '> ').focus();
}*/

var reloadTime = 1000;
function getMessages(){
	// On lance la requête ajax
	$.getJSON('get-message.php?id_task='+id_task, function(data) {
		// On intialise les variables pour le scroll jusqu'en bas
		// Pour voir les derniers messages
		var container = $('#text');
	  	var content = $('#messages_content');
		$("#text").html(data['messages']);
	  	content = $('#messages_content');
	});
}

function postMessage() {
	// On lance la requête ajax
	// type: POST > nous envoyons le message

	// On encode le message pour faire passer les caractères spéciaux comme +
	var message = encodeURIComponent($("#message").val());
	$.ajax({
		type: "POST",
		url: "post-message.php",
		data: "message="+message + "&id_task="+id_task,
		success: function(msg){
			// Si la réponse est true, tout s'est bien passé,
			// Si non, on a une erreur et on l'affiche
			if(msg == true){
				// On vide la zone de texte
				$("#message").val('');
				$("#responsePost").slideUp("slow").html('');
			}else $("#responsePost").html(msg).slideDown("slow");
			// on resélectionne la zone de texte, en cas d'utilisation du bouton "Envoyer"
			$("#message").focus();
		},
		error: function(msg){
			// On alerte d'une erreur
			alert('Erreur');
		}
	});
}

$(document).ready(function() {
	// On vérifie que la zone de texte existe
	// Servira pour la redirection en cas de suppression de compte
	// Pour ne pas rediriger quand on est sur la page de connexion
	if(document.getElementById('message')) {
		// actualisation des messages
		window.setInterval(getMessages, reloadTime);
		// on sélectionne la zone de texte
		$("#message").focus();
	}
});