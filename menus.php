<?php
	//include("functions.php");
    $db = connecting_db();
	if($_SESSION['role'] != "VOLUNTEER"){
		$commissions = $db->query('SELECT id_commission, name_commission FROM commissions WHERE active');
	}
?>
<head>
  <link rel="stylesheet" type="text/css" href="GBoC.css" media="all"/>
</head>
<nav id="menu">
    <div class="element_menu">
        <ul class = "niveau1">
        	<li><a href="data_volunteer.php">Mes informations</a></li>
            <li><a href="volunteer_events.php">Les événements à venir</a></li>
            <li><a href="volunteer_undertakings.php">Mes engagements</a></li>
            <?php if($_SESSION['role']!="VOLUNTEER"){
            	echo "<li><a> Commissions</a>";
            	echo "<ul class = \"niveau2\">";
            	while($data_commission = $commissions->fetch()){
            		if($_SESSION['role'] == "ADMIN" || in_array($_SESSION['uuid'], explode(",",substr($data_commission['moderators'],1,-1)))){
            			echo "<li><a>Commission ".$data_commission['name_commission']."</a>";
            			echo '<ul class = "niveau3"><li><a href="commission_volunteers.php?id='.bin2hex($data_commission['id_commission']).'">Liste des bénévoles</a></li>';
            			echo "<li><a href='commission_events.php?id=".bin2hex($data_commission['id_commission'])."'>Liste des événements</a></li></ul></li>";
            		}
            	}
            	echo "</ul></li>";
            }
            if($_SESSION['role'] == 'ADMIN'){ ?>
            	<li><a>Administration du site</a>
            	<ul class = "niveau2">
            		<li><a href='list_all_volunteers.php'>Liste des bénévoles</a></li>
            		<li><a href='list_commissions.php'>Liste des commissions</a></li>
            		<li><a href='list_events.php'>Liste des événements</a></li>
            	</ul></li>
        	<?php } ?>
            <li><a href="disconnection.php">Déconnexion</a></li>
        </ul>
    </div>
</nav>
