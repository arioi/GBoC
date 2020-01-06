<?php
	//include("functions.php");
    $db = connecting_db();
	if($_SESSION['role'] != "VOLUNTEER"){
        $commissions = $db->query('SELECT id_commission, name_commission FROM commissions WHERE active');
		$commissions2 = $db->query('SELECT id_commission, name_commission FROM commissions WHERE active'); // pour le menu format mobile
	}
?>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <script type="text/javascript" src="https://code.jquery.com/jquery-2.1.1.min.js"></script>
    <script src="jquery-ui-1.12.1/jquery-ui.js"></script>
    <script type="text/javascript" src="materialize/js/materialize.js"></script>
    <link rel="stylesheet" type="text/css" href="GBoC.css" media="all"/>
    <link rel="stylesheet" type="text/css" href="materialize/css/materialize.css" />
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" />
</head>

<div id="divHeader">
    <img id="logoTav" src="img/Logo-MBTaV-Site.jpg" alt="logo TaV" />
    <h1 id="hello">Bonjour <?php echo $_SESSION["prenom"];?></h1>
</div>

<div id="menu">
        <ul class = "niveau1">
            <li class="liNiveau1"><a href="data_volunteer.php">Mes informations</a></li>
            <li class="liNiveau1"><a href="volunteer_events.php">Les événements à venir</a></li>
            <li class="liNiveau1"><a href="volunteer_undertakings.php">Mes engagements</a></li>
            <?php if($_SESSION["role"]!="VOLUNTEER"){
                echo "<li class='liNiveau1'><a> Commissions</a>";
                echo "<ul class = \"niveau2\">";
                while($data_commission = $commissions->fetch()){
                    if($_SESSION['role'] == "ADMIN" || in_array($_SESSION['uuid'], explode(",",substr($data_commission['moderators'],1,-1)))){
                        echo "<li class='liNiveau2'><a>Commission ".$data_commission['name_commission']."</a>";
                        echo "<ul class='niveau3'><li class='liNiveau3'><a href='commission_volunteers.php?id=".bin2hex($data_commission["id_commission"])."'>Liste des bénévoles</a></li>";
                        echo "<li class='liNiveau3'><a href='commission_events.php?id=".bin2hex($data_commission['id_commission'])."'>Liste des événements</a></li></ul></li>";
                    }
                }
                echo "</ul></li>";
            }
            if($_SESSION['role'] == 'ADMIN'){ ?>
                <li class="liNiveau1"><a>Administration du site</a>
                <ul class = "niveau2">
                    <li class="liNiveau2"><a href='list_all_volunteers.php'>Liste des bénévoles</a></li>
                    <li class="liNiveau2"><a href='list_commissions.php'>Liste des commissions</a></li>
                    <li class="liNiveau2"><a href='list_events.php'>Liste des événements</a></li>
                </ul></li>
            <?php } ?>
            <li class="liNiveau1"><a href="disconnection.php">Déconnexion</a></li>
        </ul>
</div>

<!-- sidenav pour format mobile -->
<div id="navbarDiv">
    <ul id="slide-out" class="sidenav accordion">
        <li class="niv1"><a href="data_volunteer.php">Mes informations</a></li>
        <li class="niv1"><a href="volunteer_events.php">Les événements à venir</a></li>
        <li class="niv1"><a href="volunteer_undertakings.php">Mes engagements</a></li>
        <?php if($_SESSION['role']!="VOLUNTEER"){
            echo "<li class='niv1'><a href='#'>Commissions <i class='material-icons right'>arrow_drop_down</i></a>";
            echo "<ul class='accordion'>";
            while($data_commission2 = $commissions2->fetch()){
                if($_SESSION['role'] == "ADMIN" || in_array($_SESSION['uuid'], explode(",",substr($data_commission2['moderators'],1,-1)))){
                    echo "<li class='niv2'><a href='#'>".$data_commission2['name_commission']."<i class='material-icons right'>arrow_drop_down</i></a>";
                    echo "<ul class='accordion'><li class='niv3'><a href='commission_volunteers.php?id=".bin2hex($data_commission2["id_commission"])."'>Liste des bénévoles</a></li>";
                    echo "<li class='niv3'><a href='commission_events.php?id=".bin2hex($data_commission2['id_commission'])."'>Liste des événements</a></li></ul></li>";
                }
            }
            echo "</ul></li>";
        }
        if($_SESSION['role'] == 'ADMIN'){ ?>
            <li class="niv1"><a>Admin. du site <i class="material-icons right">arrow_drop_down</i></a>
            <ul class="accordion">
                <li class="niv2"><a href='list_all_volunteers.php'>Liste des bénévoles</a></li>
                <li class="niv2"><a href='list_commissions.php'>Liste des commissions</a></li>
                <li class="niv2"><a href='list_events.php'>Liste des événements</a></li>
            </ul></li>
        <?php } ?>
        <li><a href="disconnection.php">Déconnexion</a></li>
    </ul>
    <a id="navbarIcon" href="#" data-target="slide-out" class="sidenav-trigger right"><i class="material-icons">menu</i></a>
</div>


<script type="text/javascript">
    $(document).ready(function(){
        $(".sidenav").sidenav();
        $(".accordion").accordion({
            collapsible: true,
            active: false,
            heightStyle:"content"
        });

        var width = ($(window).width()/6);

        $(".liNiveau3 a").css({
            "width":width
        });
    });

</script>
