<?php

/*
    Constantes
    ----------
*/
const PART1_DATE_ID = 'visitor_nb_date';
const URL_SITE = "https://formation-oc-php.sansgodasses.com/projet-3/site-wordpress/";

// Fonctions pour l'envoi d'email
require ("my_email.php");

// Pour m'aider à débugger
require ('my_log.php');


/*
	Connexion à la base de données locale ou en ligne
	-------------------------------------------------
*/

/**
 *	Lance la connexion à la base de données en local puis en ligne
 *
 *	@return {PDO} L'objet PDO de la base de données
 */
function connectTheDb() {

	// On essaye en ligne
	$db = @dbConnect('sansgodapfocphp.mysql.db', 'sansgodapfocphp', 'sansgodapfocphp', 'viveLeDev7');

	if (!$db) {

		// On essaye en local
		$db = @dbConnect('localhost', 'oc_projet3', 'root', '');

		if (!$db) {
			// On laisse tomber...
			echo "<h4>Pas moyen de se connecter à la base de données ! La tuile !</h4>";
		}
	}

	return $db;
}

/**
 *  Renvoie un objet PDO connecté à la base de donnée
 *
 *  @param {String} $host le serveur
 *  @param {String} $databaseName le nom de la base de données
 *  @param {String} $user le nom d'utilisateur
 *  @param {String} $password le mot de passe
 *  @param {Strong} $charset = 'utf8' l'encodage
 *	@return l'objet PDO ou false en cas de problème
 */
function dbConnect($host, $databaseName, $user, $password, $charset = 'utf8') {

    try {
        $db = new \PDO('mysql:host=' . $host . ';dbname=' . $databaseName . ';charset=' . $charset, $user, $password);
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING); // On émet une alerte à chaque fois qu'une requête a échoué.
    } catch(Exception $e) {
        return false;
    }
    return $db;
}


/*
	Vérification du formulaire de préinscription
	--------------------------------------------
*/

/**
 *	Teste si le formulaire de préinscription est correctement rempli
 */
function checkRegistrationForm() {

	if (checkNameEmail() AND checkdates()) {
		return true;
	}
	return false;
}

/**
 *	Vérifie que le nom et l'email sont bien remplis
 *
 *	@return {Boolean} true si tout est bien rempli
 */
function checkNameEmail() {

	if (isset($_POST['visitor_name'])
		AND isset($_POST['visitor_email'])
		AND !empty($_POST['visitor_name'])
		AND !empty($_POST['visitor_email'])) {

		return true;
	}
	return false;
}

/**
 *	Vérifie qu'une date est sélectionnée
 *
 *	@return {Boolean} true si au moins une case est cochée
 */
function checkdates() {

	$dateIds = getDatesIds();

	foreach ($dateIds as $key => $value) {
		if ($_POST[$value] > 0) {
			return true;
		}
	}
	return false;
}

/**
 *	Récupère les id des dates retenues
 *
 *	@return {Array} le tableau contenant les id des dates retenues
 */
function getDatesIds() {

	$dateIds = array();
	$i = 0;

    if ( have_posts() ) {
		while ( have_posts() ) {
			the_post();
	        if (get_the_category()[0]->slug === "film") {
	        	$dateIds[$i] = PART1_DATE_ID . $i;
	        	$i++;
	        }
	    }
    }

	return $dateIds;
}


/*
	Traitement d'une préinscription
	-------------------------------
*/

/**
 *	Enregistre le visiteur en base de données à partir de $_POST, envoi un mail de confirmation au visiteur et envoi un mail à la présidente
 *
 *	@return {Boolean} true si l'inscription a réussie
 */
function registerVisitor() {

	/* Sauvegarde des infos du visiteur dans la base de données */
	$db = connectTheDb();
	$visitorData = array (
		'name' => htmlspecialchars($_POST['visitor_name']),
		'email' => htmlspecialchars($_POST['visitor_email']),
		'datesAndNumber' => getDatesAndNumber()
	);

	// On vérifie que l'utilisateur n'est pas déjà présent dans la base
	if (!visitorInTheDB($db, $visitorData['email'])) {
		insertVisitorInDB($db, $visitorData);
	} else {
		return false;
	}

	/* Envoi d'un email de confirmation au visiteur */
	sendEmailToTheVisitor($visitorData);

	/* Envoi d'un email à la présidente */
	sendEmailToTheBoss($visitorData);

	return true;
}

/**
 *	Regarde dans la base si le visiteur existe
 *
 *	@param {PDO} $db la base de données
 *	@param {String} $email l'adresse email du visiteur
 *	@return {Boolean} true si le visiteur est déjà dans la base
 */
function visitorInTheDB($db, $email) {

	$query = 'SELECT v_email
		FROM ffpa_visitor
		WHERE v_email = :email';
	$requestVisitor = $db->prepare($query);
	$requestVisitor->execute(array(
		'email' => $email
	));

	$response = $requestVisitor->fetch();
	if ($response)
		return true;
	return false;
}

/**
 *	Enregistre un visiteur dans la base de données
 *
 *	@param {PDO} $db la base de données
 *	@param {Array} $visitorData un tableau contenant les infos du visiteur
 */
function insertVisitorInDB($db, $visitorData) {

	// Table ffpa_visitor
	$queryVisitor = 'INSERT INTO ffpa_visitor(v_name, v_email, v_registration_date)
		VALUES (:name, :email, :registration_date)';
	$requestVisitor = $db->prepare($queryVisitor);
	$requestVisitor->execute(array(
		'name' => $visitorData['name'],
		'email' => $visitorData['email'],
		'registration_date' => date('Y-m-d H:i:s')
	));

	// Table ffpa_visitor_projection
	$queryDatesAndNumbers = 'INSERT INTO ffpa_visitor_projection(vp_visitor_email, vp_projection_date, vp_number)
		VALUES (:visitor_email, :projection_date, :visitor_number)';
	$requestVisitorNumber = $db->prepare($queryDatesAndNumbers);

	foreach ($visitorData['datesAndNumber'] as $dateAndNumber) {
		$queryArray = array(
			'visitor_email' => $visitorData['email'],
			'projection_date' => formatProjectionDate($dateAndNumber['date']),
			'visitor_number' => intval($dateAndNumber['number'])
		);

		$requestVisitorNumber->execute($queryArray);
	}
}

/**
 *	Formate une date de projection en DATE MySQL
 * 	Exemple : 'Lundi 5 août' -> '2019-08-05'
 */
function formatProjectionDate($projectionDate) {

	$date = explode(' ', $projectionDate);
	return '2019-08-0' . $date[1];
}

/**
 *	Donne les dates retenues et le nombre de personnes en fonction des id fournis par le formulaire
 *
 *	@return {Array} un tableau contenant les dates retenues et le nombre de personnes
 */
function getDatesAndNumber() {

	$datesAndNumber = array();
	$selectedDates = array();
	$i = 0;

    /* On regarde quelles dates ont été sélectionnées */
    $dateIds = getDatesIds();

	foreach ($dateIds as $key => $value) {
		if ($_POST[$value] > 0) {

			$datesAndNumber[$i] = array(
				'date' => '',
				'number' => $_POST[$value]
			);
			$selectedDates[$i] = intval($value[strlen($value) - 1]);
			$i++;
		}
	}

	/* On retrouve les dates sélectionnées à partir des index */
	$i = 0;
	if (have_posts()) {
		while (have_posts()) {
			the_post();
			if (get_the_category()[0]->slug === "film") {
				for ($j=0; $j < count($datesAndNumber); $j++) { 
					if ($selectedDates[$j] === $i) {
	        			$datesAndNumber[$j]['date'] = get_the_title();
	        		}
				}
	        	$i++;
	        }
		}
	}

    return $datesAndNumber;
}

/**
 *	Créé une chaîne à partir d'un tableau de getDatesAndNumber()
 *
 *	@param {Array} datesAndNumber le tableau issu de getDatesAndNumber()
 *	@return {String} La chaîne correspondant au tableau
 */
function datesAndNumberToString($datesAndNumber) {

	$data = '';

	foreach ($datesAndNumber as $dateAndNumber) {
		if ($dateAndNumber['number'] > 1)
			$day = $dateAndNumber['date'] . ' ' . ' : ' . $dateAndNumber['number'] . ' personnes';
		else
			$day = $dateAndNumber['date'] . ' ' . ' : ' . $dateAndNumber['number'] . ' personne';
		$data = $day . "\r\n" . $data;
	}

	return $data;
}


/*
	Suppression d'un visiteur
	-------------------------
*/

/**
 *	Supprime un visiteur de la base de données
 *
 *	@param {String} $email l'adresse email du visiteur
 */
function deleteVisitor($email) {

	$db = connectTheDb();
	if (visitorInTheDB($db, $email)) {
		$query = 'DELETE FROM ffpa_visitor
			WHERE v_email = :email';
		$requestDelete = $db->prepare($query);
		$requestDelete->execute(array(
			'email' => $email
		));
	}
}


/*
	Mise à jour d'un visiteur
	-------------------------
*/

/**
 *	Met à jour les infos d'un visiteur à partir de $_POST et envoi un email de confirmation
 */
function updateVisitor() {

	$visitorEmail = htmlspecialchars($_POST['visitor_email']);

	deleteVisitor($visitorEmail);

	registerVisitor();
}


/*
	Récupération des infos du visiteur
	----------------------------------
*/

/**
 *	Récupère le nom et le prénom d'un visiteur
 *
 *	@param {String} $email l'email du visiteur
 *	@return {String} le nom et le prénom du visiteur
 */
function getVisitorName($email) {

	$visitorName = "";

	$query = 'SELECT v_name
		FROM ffpa_visitor
		WHERE v_email = :email';

	$db = connectTheDb();
	$requestVisitorName = $db->prepare($query);
	$requestVisitorName->execute(array(
		'email' => $email
	));

	$visitorName = $requestVisitorName->fetch();

	return $visitorName[0];
}


/*
	Widget
	------
*/

/**
 * Register our sidebars and widgetized areas.
 *
 */
function arphabet_widgets_init() {

	register_sidebar( array(
		'name'          => 'Carte footer',
		'id'            => 'footer_map',
		'before_widget' => '<div>',
		'after_widget'  => '</div>',
		'before_title'  => '<h2 class="rounded">',
		'after_title'   => '</h2>',
	) );
}
add_action( 'widgets_init', 'arphabet_widgets_init' );