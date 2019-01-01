<?php
/*

Plugin Name: Préinscription
Description: Permet d'afficher les données des visiteurs préinscris au festival des films de plein air
Version: 1.0
Author: ZeWebAgency
License: GPL2

*/


/**
 *	Affichage du plugin dans l'interface WP
 */
function addAdminMenu() {

	add_menu_page('Préinscriptions au Festival', 'Préinscriptions FFPA', 'manage_options', 'ffpa', 'menuHtml');
}

/**
 *	Affichage du menu
 */
function menuHtml() {

	// Mise en forme et titre
?>
	<style type="text/css">

		table {
			border-collapse: collapse;
		}

		tr {
			border: 1px solid grey;
		}

		td, th {
			padding: 10px;
			text-align: center;
		}

		textarea {
			width: 600px;
			height: 300px;
		}

		li {
			font-size: 1.5em;
		}

		#preinscription_total {
			font-size: 1.5em;
		}
		
		.my_input {
			width: 350px;
		}
	</style>
	
	<h1><?= get_admin_page_title() ?></h1>
<?php

	$db = connectTheDb();
	$messages = array();

	if (isset($_POST['contact_email'])) {
		// Modifie l'adresse email de contact
		changeContactEmail($db, htmlspecialchars($_POST['contact_email']));
		$messages[] = '🗸 Adresse email de contact modifiée. Nouvelle adresse : ' . htmlspecialchars($_POST['contact_email']);

	} elseif (isset($_POST['broadcast_mail_content'])) {
		// Envoi un message à tous les visiteurs préinscrits
		if (sendEmailToEverybody()) {
			$messages[] = '🗸 Message envoyé à tous les visiteurs préinscrits.';
		} else {
			$messages[] = '⚠ L\'envoi du message à tous les visiteurs préinscrits a échoué.';
		}
	}

	// Affichage des stats
    showRegisteredVisitors($db);
    // Affichage des messages d'info
    showMessages($messages);
    // Affichage des options
    showOptions();
    // Affichage de la zone d'envoi d'email à tous les visiteurs préinscrits
    showContactEverybody();
}


/*
	Statistiques
	------------
*/

/**
 *	Affiche le nombre de visiteurs préinscrits par soir
 *
 *	@param {PDO} $db la base de données
 */
function showRegisteredVisitors($db) {

	$projectionDates = getProjectionDates($db);
	$numbersOfVisitors = getNumbersOfVisitors($db, $projectionDates);
?>
	<table>
		<thead>
			<tr>
				<th>Date</th>
				<th>Nombre de personnes préinscrites</th>
			</tr>
		</thead>
		<tbody>
<?php
	for ($i=0; $i < count($projectionDates); $i++) { 
		showDayNumber($projectionDates[$i], $numbersOfVisitors[$i]);
	}
?>
		</tbody>
	</table>

	<p id="preinscription_total">Nombre total de visiteurs préinscrits : <strong><?= getTotal($db) ?></strong></p>
<?php
}

/**
 *	Compte le nombre total de visiteurs préinscrits
 *
 *	@param {PDO} $db la base de données
 *	@return {Interger} le nombre total de visiteurs
 */
function getTotal($db) {

	$query = 'SELECT SUM(vp_number)
		FROM ffpa_visitor_projection';
	$requestTotal = $db->query($query);
	$response = $requestTotal->fetch();

	return $response[0];
}

/**
 *	Affiche les statistiques d'une date dans une ligne d'un tableau
 *
 *	@param {String} $day le jour
 *	@param {Integer} $number le nombre de personnes attendues
 */
function showDayNumber($day, $number) {

	$myDateTime = DateTime::createFromFormat('Y-m-d', $day);
	$newDateString = $myDateTime->format('d/m/Y');
?>
	<tr>
		<td><?= $newDateString ?></td>
		<td><?= $number ?></td>
	</tr>
<?php
}

/**
 *	Récupère les nombres des visiteurs par jour dans un tableau
 *
 *	@param {PDO} $db la base de données
 *	@param {Array} $dates les dates du festival
 *	@return {Array} les nombres des visiteurs
 */
function getNumbersOfVisitors($db, $dates) {

	$numbersOfVisitors = array();
	$stats;
	$i = 0;

	foreach ($dates as $date) {
		$numbersOfVisitors[$i] = getDayNumber($db, $date);
		$i++;
	}

	return $numbersOfVisitors;
}

/**
 *	Récupère les dates du festival dans un tableau
 *
 *	@param {PDO} $db la base de données
 *	@return {Array} les dates du festival
 */
function getProjectionDates($db) {

	$projectionDates = array();
	$i = 0;

	$query = 'SELECT p_date
		FROM ffpa_projection';

	$requestDates = $db->query($query);
	while ($response = $requestDates->fetch()) {
		$projectionDates[$i] = $response[0];
		$i++;
	}

	return $projectionDates;
}


/**
 *	Récupère le nombre de visiteurs préinscris d'une journée
 *
 *	@param {PDO} $db la base de données
 *	@param {String} $day le jour
 *	@return {Integer} le nombre de visiteurs
 */
function getDayNumber($db, $day) {

	$query = "SELECT SUM(vp_number)
		FROM ffpa_visitor_projection
		WHERE vp_projection_date = :day";

	$requestDayNumber = $db->prepare($query);
	$requestDayNumber->execute(array(
		'day' => $day
	));
	$response = $requestDayNumber->fetch();

	return $response[0];
}


/*
	Options
	-------
*/

/**
 *	Affiche les options
 */
function showOptions() {

?>
	<h3>Contact</h3>

	<form method="post" action="index.php/wp-admin/admin.php?page=ffpa">
		<p>
			<label for="contact_email">Adresse email de contact : </label>
			<input class="my_input" type="text" name="contact_email" id="contact_email" <?= 'value="' . getContactEmail() . '"' ?> />
			<input type="submit" value="Enregistrer" />
		</p>
	</form>
<?php
}

/**
 *	Modifie l'adresse email de contact
 *
 *	@param {PDO} $db la base de données
 *	@param {String} $email l'adresse email
 */
function changeContactEmail($db, $email) {

	$query = 'UPDATE ffpa_admin
		SET a_email = :email';

	$requestContactEmail = $db->prepare($query);
	$requestContactEmail->execute(array(
		'email' => $email
	));
}


/*
	Contacter les visiteurs préinscrits
	-----------------------------------
*/

function showContactEverybody() {

?>
	<h3>Envoi d'un message à tous les visiteurs préinscrits</h3>

	<form method="post" action="index.php/wp-admin/admin.php?page=ffpa">
		<p>
			<label for="broadcast_mail_subject">Objet du message :</label><br />
			<input class="my_input" type="text" name="broadcast_mail_subject" id="broadcast_mail_subjectbroadcast_mail_subject" value="Festival des films de plein air" />
		</p>
		<p>
			<label for="broadcast_mail_content">Message à envoyer aux visiteurs préinscrits :</label><br />
			<textarea id="broadcast_mail_content" name="broadcast_mail_content"></textarea>
		</p>

		<input type="submit" value="Envoyer le message" />
	</form>
<?php
}


/* 
	Messages d'info
	---------------
*/

/**
 *	Affiche les messages d'info
 *
 *	@param {Array} $messages un tableau contenant les messages d'info
 */
function showMessages($messages) {

?>
	<div class="my_messages_wrapper">
		<ul>
<?php
	foreach ($messages as $message) {
		echo '<li>' . $message . '</li>';
	}
?>
		</ul>
	</div>
<?php
}


/*
	Instructions
	------------
*/

add_action('admin_menu', 'addAdminMenu');