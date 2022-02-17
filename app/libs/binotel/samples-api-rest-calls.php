<?php

/**
 * Примеры категории CALLS.
 * Документация: http://developers.binotel.ua/#rest-api-calls
 */


/**
 * ВНИМАНИЕ! В bootstrap.php - прописаны данные для авторизации и инициализация API библиотеки.
 * Пожалуйста ознакомьтесь с этим файлом.
 */
require_once(__DIR__ .'/bootstrap.php');



/**
 * Пример 1: инициирование двустороннего звонка с внутренней линией и внешним номером.
 * Документация: http://developers.binotel.ua/#internal-number-to-external-number
 */

$result = $api->sendRequest('calls/internal-number-to-external-number', array(
	'internalNumber' => '910',
	'externalNumber' => '0443334023',
));

if ($result['status'] === 'success') {
	var_dump($result['generalCallID']);
} else {
	printf('REST API ошибка %s: %s %s', $result['code'], $result['message'], PHP_EOL);
}



/**
 * Пример 2: инициирование двустороннего звонка с внутренней линией и внешним номером с последующим отслеживанием статуса.
 * Документация: http://developers.binotel.ua/#internal-number-to-external-number
 */

$result = $api->sendRequest('calls/internal-number-to-external-number', array(
	'internalNumber' => '901',
	'externalNumber' => '0443334023'
));

if ($result['status'] === 'success') {
	$numberOfAttempts = 10;
	$delayBetweenAttempts = 10;

	for ($i=0; $i<$numberOfAttempts; $i++) {
		$result = $api->sendRequest('stats/call-details', array(
			'generalCallID' => $result['generalCallID']
		));

		if ($result['status'] !== 'success') {
			printf('REST API ошибка %s: %s %s', $result['code'], $result['message'], PHP_EOL);

			break;
		}

		if (count($result['callDetails'][$generalCallID])) {
			if ($result['callDetails'][$generalCallID]['disposition'] === 'ONLINE') {
				printf('Сотрудник говорит с клиентом! %s', PHP_EOL);

				break;
			} elseif ($result['callDetails'][$generalCallID]['disposition'] !== '') {
				printf('Звонок завершился! %s', PHP_EOL);

				break;
			}
		}

		sleep($delayBetweenAttempts);
	}
} else {
	printf('REST API ошибка %s: %s %s', $result['code'], $result['message'], PHP_EOL);
}



/**
 * Пример 3: инициирование двустороннего звонка c двумя внешними номерами.
 * Документация: http://developers.binotel.ua/#internal-number-to-external-number
 */

$result = $api->sendRequest('calls/external-number-to-external-number', array(
	'externalNumber1' => '0970002233',
	'externalNumber2' => '0443334333',
	'phoneNumber' => '0443334023',
	'limitCallTime' => '120'
));

if ($result['status'] === 'success') {
	var_dump($result['generalCallID']);
} else {
	printf('REST API ошибка %s: %s %s', $result['code'], $result['message'], PHP_EOL);
}



/**
 * Пример 4: перевод звонка с участием.
 * Документация: http://developers.binotel.ua/#attended-call-transfer
 */

$result = $api->sendRequest('calls/attended-call-transfer', array(
	'generalCallID' => '22661563',
	'externalNumber' => '912'
));

if ($result['status'] === 'success') {
	var_dump($result);
} else {
	printf('REST API ошибка %s: %s %s', $result['code'], $result['message'], PHP_EOL);
}



/**
 * Пример 5: завершение звонка.
 * Документация: http://developers.binotel.ua/#hangup-call
 */

$result = $api->sendRequest('calls/hangup-call', array(
	'generalCallID' => '22661891'
));

if ($result['status'] === 'success') {
	var_dump($result);
} else {
	printf('REST API ошибка %s: %s %s', $result['code'], $result['message'], PHP_EOL);
}



/**
 * Пример 6: звонок с оповещением голосового файла.
 * Документация: http://developers.binotel.ua/#call-with-announcement
 */

$result = $api->sendRequest('calls/call-with-announcement', array(
	'externalNumber' => '0443334023',
	'voiceFileID' => '4'
));

if ($result['status'] === 'success') {
	var_dump($result);
} else {
	printf('REST API ошибка %s: %s %s', $result['code'], $result['message'], PHP_EOL);
}



/**
 * Пример 7: звонок с голосовым меню.
 * Документация: http://developers.binotel.ua/#call-with-interactive-voice-response
 */

$result = $api->sendRequest('calls/call-with-interactive-voice-response', array(
	'externalNumber' => '0443334023',
	'ivrName' => 'confirmation-call'
));

if ($result['status'] === 'success') {
	var_dump($result);
} else {
	printf('REST API ошибка %s: %s %s', $result['code'], $result['message'], PHP_EOL);
}
