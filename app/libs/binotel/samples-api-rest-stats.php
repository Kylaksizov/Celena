<?php

/**
 * Примеры категории STATS.
 * Документация: http://developers.binotel.ua/#rest-api-stats
 */


/**
 * ВНИМАНИЕ! В bootstrap.php - прописаны данные для авторизации и инициализация API библиотеки.
 * Пожалуйста ознакомьтесь с этим файлом.
 */
require_once(__DIR__ .'/bootstrap.php');



/**
 * Пример 1: входящие или исходящие звонки за период времени.
 * Документация: http://developers.binotel.ua/#incoming-calls-for-period
 */

$result = $api->sendRequest('stats/outgoing-calls-for-period', array(
	'startTime' => 1370034000, // Sat, 01 Jun 2013 00:00:00 +0300
	'stopTime' => 1370120399 // Sat, 01 Jun 2013 23:59:59 +0300
));

if ($result['status'] === 'success') {
	var_dump($result['callDetails']);
} else {
	printf('REST API ошибка %s: %s %s', $result['code'], $result['message'], PHP_EOL);
}



/**
 * Пример 2: входящие или исходящие звонки с N времени по настоящее время.
 * Документация: http://developers.binotel.ua/#all-incoming-calls-since
 */

$lastRequestTimestamp = 1370034000; // Sat, 01 Jun 2013 00:00:00 +0300

$result = $api->sendRequest('stats/all-incoming-calls-since', array(
	'timestamp' => $lastRequestTimestamp
));

if ($result['status'] === 'success') {
	var_dump($result['callDetails']);
} else {
	printf('REST API ошибка %s: %s %s', $result['code'], $result['message'], PHP_EOL);
}



/**
 * Пример 3: звонки которые в онлайне.
 * Документация: http://developers.binotel.ua/#online-calls
 */

$result = $api->sendRequest('stats/online-calls', array());

if ($result['status'] === 'success') {
	var_dump($result['callDetails']);
} else {
	printf('REST API ошибка %s: %s %s', $result['code'], $result['message'], PHP_EOL);
}



/**
 * Пример 4: звонки за день (как входящие, так и исходящие).
 * Документация: http://developers.binotel.ua/#list-of-calls-per-day
 */

$result = $api->sendRequest('stats/list-of-calls-per-day', array(
	'dayInTimestamp' => mktime(0, 0, 0, 11, 25, 2015)
));

if ($result['status'] === 'success') {
	var_dump($result['callDetails']);
} else {
	printf('REST API ошибка %s: %s %s', $result['code'], $result['message'], PHP_EOL);
}



/**
 * Пример 5: звонки за период времени (как входящие, так и исходящие) (! но не больше 24 часов).
 * Документация: http://developers.binotel.ua/#list-of-calls-for-period
 */

$result = $api->sendRequest('stats/list-of-calls-for-period', array(
	'startTime' => 1370034000, // Sat, 01 Jun 2013 00:00:00 +0300
	'stopTime' => 1370048400 // Sat, 01 Jun 2013 04:00:00 +0300
));

if ($result['status'] === 'success') {
	var_dump($result['callDetails']);
} else {
	printf('REST API ошибка %s: %s %s', $result['code'], $result['message'], PHP_EOL);
}



/**
 * Пример 6: звонки по внутреннему номеру сотрудника за период времени (как входящие, так и исходящие).
 * Документация: http://developers.binotel.ua/#list-of-calls-by-internal-number-for-period
 */

$result = $api->sendRequest('stats/list-of-calls-by-internal-number-for-period', array(
	'internalNumber' => '901',
	'startTime' => 1370034000, // Sat, 01 Jun 2013 00:00:00 +0300
	'stopTime' => 1370638799 // Fri, 07 Jun 2013 23:59:59 +0300
));

if ($result['status'] === 'success') {
	var_dump($result['callDetails']);
} else {
	printf('REST API ошибка %s: %s %s', $result['code'], $result['message'], PHP_EOL);
}



/**
 * Пример 7: потерянные звонки за сегодня.
 * Документация: http://developers.binotel.ua/#list-of-lost-calls-for-today
 */

$result = $api->sendRequest('stats/list-of-lost-calls-today', array());

if ($result['status'] === 'success') {
	var_dump($result['callDetails']);
} else {
	printf('REST API ошибка %s: %s %s', $result['code'], $result['message'], PHP_EOL);
}



/**
 * Пример 8: звонки по номеру телефона (как входящие, так и исходящие).
 * Документация: http://developers.binotel.ua/#history-by-external-number
 */

$result = $api->sendRequest('stats/history-by-external-number', array(
	'externalNumbers' => array('0443334023', '0443334444')
));

if ($result['status'] === 'success') {
	var_dump($result['callDetails']);
} else {
	printf('REST API ошибка %s: %s %s', $result['code'], $result['message'], PHP_EOL);
}



/**
 * Пример 9: звонки по идентификатору клиента (как входящие, так и исходящие).
 * Документация: http://developers.binotel.ua/#history-by-customer-id
 */

$result = $api->sendRequest('stats/history-by-customer-id', array(
	'customerID' => '6611'
));

if ($result['status'] === 'success') {
	var_dump($result['callDetails']);
} else {
	printf('REST API ошибка %s: %s %s', $result['code'], $result['message'], PHP_EOL);
}



/**
 * Пример 10: недавние звонки по внутреннему номеру сотрудника (как входящие, так и исходящие). Используется для реализации функции "Мои недавние звонки" для сотрудника.
 * Документация: http://developers.binotel.ua/#recent-calls-by-internal-number
 */

$result = $api->sendRequest('stats/recent-calls-by-internal-number', array(
	'internalNumber' => '901'
));

if ($result['status'] === 'success') {
	var_dump($result['callDetails']);
} else {
	printf('REST API ошибка %s: %s %s', $result['code'], $result['message'], PHP_EOL);
}



/**
 * Пример 11: данные о звонке по идентификатору звонка.
 * Документация: http://developers.binotel.ua/#call-details
 */

$result = $api->sendRequest('stats/call-details', array(
	'generalCallID' => array('2255713', '2256039', '2252553')
));

if ($result['status'] === 'success') {
	var_dump($result['callDetails']);
} else {
	printf('REST API ошибка %s: %s %s', $result['code'], $result['message'], PHP_EOL);
}



/**
 * Пример 12: получение ссылки на запись разговора.
 * Документация: http://developers.binotel.ua/#call-record
 */

$result = $api->sendRequest('stats/call-record', array(
	'generalCallID' => '12501059'
));

if ($result['status'] === 'success') {
	var_dump($result['url']);
} else {
	printf('REST API ошибка %s: %s %s', $result['code'], $result['message'], PHP_EOL);
}

