<?php
// Envoi du mail de désincription
$visitorEmail = htmlspecialchars($_GET['send_account_link']);
sendAccountLink($visitorEmail);
?>
<p id="visitor_account_msg" class="center col-12 my_message neutral">
    <span class="validated">🗸</span> Un lien vous a été envoyé à l'adresse suivante : <?= $visitorEmail ?>.<br />Cliquez sur ce lien pour modifier vos informations ou pour vous désinscrire.
</p>
<p class="center col-12 my_message neutral">
	Pour revenir au site, <a href="index.php">cliquez ici.</a>
</p>