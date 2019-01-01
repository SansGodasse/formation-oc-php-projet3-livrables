<?php

/*
	Envoi d'emails
	--------------
*/

/**
 *	Envoi un email à tous les visiteurs préinsrits à l'aide de $_POST
 *
 *	@return {Boolean} true si le mail a bien été envoyé
 */
function sendEmailToEverybody() {

	$contactEmail = getContactEmail();
	$to = implode(', ', getAllVisitorEmails());
	$header = createMailHeader($contactEmail, $to);
	$subject = htmlspecialchars($_POST['broadcast_mail_subject']);
	$message = htmlspecialchars($_POST['broadcast_mail_content']);

	if (!mail($to, $subject, $message, $header)) {
		myLog(array(
			'problem' => "La fonction mail() a échouée. L'email de la fonction sendEmailToEverybody() n'a pas été envoyé.",
			'to' => $to,
			'subject' => $subject,
			'header' => $header,
			'message' => $message
		));
		return false;
		
	} else {
		myLog(array(
			'success' => "La fonction mail() a fonctionné. L'email de la fonction sendEmailToEverybody() a été envoyé le " . date('D d/m/Y à H:i:s'),
			'to' => $to,
			'subject' => $subject,
			'header' => $header,
			'message' => $message
		));
		return true;
	}
}

/**
 *	Envoi un email contenant le lien pour accéder au compte du visiteur
 *
 *	@param {String} $email l'email du visiteur
 */
function sendAccountLink($email) {

	$contactEmail = getContactEmail();
	$to = $email;
	$subject = 'Festival des films de plein air - Modification de vos informations ou désinscription';
	$header = createMailHeader($contactEmail, $email);

	$message = 'Suivez ce lien pour accéder à votre compte : ' . URL_SITE . '?visitor_account=' . $email;

	if (!mail($to, $subject, $message, $header)) {
		myLog(array(
			'problem' => "La fonction mail() a échouée. L'email de la fonction sendAccountLink() n'a pas été envoyé.",
			'to' => $to,
			'subject' => $subject,
			'header' => $header,
			'message' => $message
		));
	} else {
		myLog('sendAccountLink() => Mail envoyé !');
	}
}

/**
 *	Envoi de l'email de confirmation au visiteur préinscris
 *
 *	@param {Array} $visitorData un tableau contenant les infos du visiteur
 */
function sendEmailToTheVisitor($visitorData) {

	$contactEmail = getContactEmail();

	$to = $visitorData['email'];
	$subject = 'Festival des films de plein air - Récapitulatif de préinscription';
	$header = createMailHeader($contactEmail, $visitorData['email']);

	$message = 'Merci ' . $visitorData['name'] . ' de vous être préinscrit au festival des films de plein air !' . "\r\n" . "\r\n" .
		"Pour rappel, l'évènement aura lieu au parc Monceau à Paris. Voici vos choix :" . "\r\n" .
		'Dates retenues et nombre de personnes : ' . "\r\n" . datesAndNumberToString($visitorData['datesAndNumber']) . "\r\n" . "\r\n" .
		'Si vous souhaitez modifier vos choix, rendez-vous à cette adresse : ' . URL_SITE . '?visitor_account=' . $visitorData['email'] . "\r\n" . "\r\n" . 
		'Si vous souhaitez vous désinscrire, rendez-vous à cette adresse : ' . URL_SITE . '?delete_visitor=' . $visitorData['email'] . "\r\n" . "\r\n" . 
		'Bonne journée !';

	if (!mail($to, $subject, $message, $header)) {
		myLog(array(
			'problem' => "La fonction mail() a échouée. L'email de la fonction sendEmailToTheVisitor() n'a pas été envoyé.",
			'to' => $to,
			'subject' => $subject,
			'header' => $header,
			'message' => $message
		));
	} else {
		myLog('sendEmailToTheVisitor() => Mail envoyé !');
	}
}

/**
 *	Envoi de l'email indiquant qu'un visiteur s'est préinscrit à la présidente
 *
 *	@param {Array} $visitorData un tableau contenant les infos du visiteur
 */
function sendEmailToTheBoss($visitorData) {

	$contactEmail = getContactEmail();
	$to = $contactEmail;
	$subject = 'Une préinscription pour FFPA !';
	$header = createMailHeader($contactEmail, $visitorData['email']);

	$message = 'Un visiteur du site web du festival vient de se préinscrire :' . "\r\n" .
		$visitorData['name'] . "\r\n" .
		$visitorData['email'] . "\r\n" .
		'Dates retenues et nombre de personnes : ' . "\r\n" . datesAndNumberToString($visitorData['datesAndNumber']);

	if (!mail($to, $subject, $message, $header)) {
		myLog(array(
			'problem' => "La fonction mail() a échouée. L'email de la fonction sendEmailToTheBoss() n'a pas été envoyé.",
			'to' => $to,
			'subject' => $subject,
			'header' => $header,
			'message' => $message
		));
	} else {
		myLog('sendEmailToTheBoss() => Mail envoyé !');
	}
}

/**
 *	Récupération de l'email de contact
 */
function getContactEmail() {

	$db = connectTheDb();
	$contactEmail = '';

	$query = 'SELECT a_email
		FROM ffpa_admin';

	$requestContactEmail = $db->query($query);
	$contactEmail = $requestContactEmail->fetch();

	return $contactEmail[0];
}

/**
 *	Récupération des emails des visiteurs préinscrits
 *
 *	@return {Array} un tableau contenant les emails
 */
function getAllVisitorEmails() {

	$visitorEmails = array();
	$i = 0;
	$db = connectTheDb();

	$query = 'SELECT v_email
		FROM ffpa_visitor';

	$requestVisitorEmails = $db->query($query);
	while ($visitorEmail = $requestVisitorEmails->fetch()) {
		$visitorEmails[$i] = $visitorEmail[0];
		$i++;
	}

	return $visitorEmails;
}

/**
 *	Construit un header pour un email
 *
 *	@param {String} $emailFrom l'email de l'expéditeur
 *	@param {String} $emailTo l'email du destinataire
 *	@return le header du mail
 */
function createMailHeader($emailFrom, $emailTo) {

	$header = 'From: ' . $emailFrom . "\r\n" .
	    'Reply-To: ' . $emailTo . "\r\n" .
	    'Content-type: text/html; charset= utf8' . "\r\n" .
    	'X-Mailer: PHP/' . phpversion();

    return $header;
}