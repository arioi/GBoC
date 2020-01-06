<?php

include("functions.php");
$db = connecting_db();

$req = "SELECT DISTINCT id_volunteer,
			surname_volunteer,
			name_volunteer
        FROM volunteers
        ORDER BY name_volunteer ASC";

$reponse = $db->query($req);
$res = $reponse->fetchAll();

$myJSON = "[";
$i = 0;

foreach($res as $result) {
	if ($i == 0) {
		$myJSON = $myJSON."{";
	} else {
		$myJSON = $myJSON.",{";
	}

	$myJSON = $myJSON."\"id\":\"".bin2hex($result["id_volunteer"])."\"";
	$myJSON = $myJSON.",\"label\":\"".utf8_encode($result["surname_volunteer"])." ".utf8_encode($result["name_volunteer"])."\"";
	$myJSON = $myJSON."}";

	$i++;
}

echo $myJSON."]";

?>