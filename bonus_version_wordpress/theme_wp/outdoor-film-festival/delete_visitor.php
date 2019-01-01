<?php
// Suppression du visiteur de la base de données
deleteVisitor($_GET['delete_visitor']);
?>
<p id="visitor_deleted_msg" class="center col-12 my_message neutral">
    <span class="validated">🗸</span> Vous avez été désinscrit du festival. Pour vous réinscrire, <a href="index.php#registration">cliquez ici.</a>
</p>

<!-- Retour à l'accueil -->
<p class="center col-12">
	<a href="index.php">Revenir au site du festival</a>
</p>