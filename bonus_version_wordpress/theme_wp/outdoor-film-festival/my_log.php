<?php

/**
 *	Ajoute un message dans ffpa_log
 *
 *	@param {String ou Array} $content le contenu du message ou un tableau contenant les éléments à enregistrer
 */
function myLog($content) {

	$db = connectTheDb();
	$logContent = '';

	$query = 'INSERT INTO ffpa_log(log_content)
		VALUES (:content)';

	if (is_array($content)) {
		foreach ($content as $key => $value) {
			$logContent .= $key . ":\n" . $value . "\n\n";
		}
	} else {
		$logContent = $content;
	}

	$requestNewLog = $db->prepare($query);
	$requestNewLog->execute(array(
		'content' => $logContent
	));
}