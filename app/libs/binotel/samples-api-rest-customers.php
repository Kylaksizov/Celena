<?php

/**
 * Примеры категории CUSTOMERS.
 * Документация: http://developers.binotel.ua/#rest-api-customers
 */

/**
 * ВНИМАНИЕ! В bootstrap.php - прописаны данные для авторизации и инициализация API библиотеки.
 * Пожалуйста ознакомьтесь с этим файлом.
 */
require_once(__DIR__ .'/bootstrap.php');



/**
 * Пример 1: выбор всех клиентов с Binotel CRM.
 * Документация: http://developers.binotel.ua/#list
 */
	
$result = $api->sendRequest('customers/list', array());

if ($result['status'] === 'success') {
	var_dump($result['customerData']);
} else {
	printf('REST API ошибка %s: %s %s', $result['code'], $result['message'], PHP_EOL);
}



/**
 * Пример 2: выбор клиентов с Binotel CRM по идентификатору клиента.
 * Документация: http://developers.binotel.ua/#take-by-id
 */

$result = $api->sendRequest('customers/take-by-id', array(
	'customerID' => array('6611')
));

if ($result['status'] === 'success') {
	var_dump($result['customerData']);
} else {
	printf('REST API ошибка %s: %s %s', $result['code'], $result['message'], PHP_EOL);
}



/**
 * Пример 3: выбор клиентов с Binotel CRM по метке.
 * Документация: http://developers.binotel.ua/#take-by-label
 */

$result = $api->sendRequest('customers/take-by-label', array(
	'labelID' => '146'
));

if ($result['status'] === 'success') {
	var_dump($result['customerData']);
} else {
	printf('REST API ошибка %s: %s %s', $result['code'], $result['message'], PHP_EOL);
}



/**
 * Пример 4: поиск клиентов в Binotel CRM по имени или по номеру телефона.
 * Документация: http://developers.binotel.ua/#search
 */

$result = $api->sendRequest('customers/search', array(
	'subject' => 'Генадий'
));

if ($result['status'] === 'success') {
	var_dump($result['customerData']);
} else {
	printf('REST API ошибка %s: %s %s', $result['code'], $result['message'], PHP_EOL);
}



/**
 * Пример 5: создание клиента.
 * Документация: http://developers.binotel.ua/#create
 */

$result = $api->sendRequest('customers/create', array(
	'name' => 'New client',
	'numbers' => array(
		'0970003322', '0939990099'
	),
	'description' => 'Информаиця о клиенте!',
	'email' => 'new.client@gmail.com',
	'assignedToEmployeeNumber' => '904',
));

if ($result['status'] === 'success') {
	var_dump($result);
} else {
	printf('REST API ошибка %s: %s %s', $result['code'], $result['message'], PHP_EOL);
}



/**
 * Пример 6: редактирование клиента.
 * Документация: http://developers.binotel.ua/#update
 */

$result = $api->sendRequest('customers/update', array(
	'id' => '6611',
	'name' => 'Sales Binotel',
	'numbers' => array(
		'0971553605', '0939990099'
	),
	'description' => '',
	'assignedToEmployeeNumber' => '',
	'labels' => array()
));

if ($result['status'] === 'success') {
	var_dump($result);
} else {
	printf('REST API ошибка %s: %s %s', $result['code'], $result['message'], PHP_EOL);
}



/**
 * Пример 7: удаление клиента.
 * Документация: http://developers.binotel.ua/#delete
 */

$result = $api->sendRequest('customers/delete', array(
	'customerID' => array('270334')
));

if ($result['status'] === 'success') {
	var_dump($result['status']);
} else {
	printf('REST API ошибка %s: %s %s', $result['code'], $result['message'], PHP_EOL);
}



/**
 * Пример 8: выбор всех меток с Binotel CRM.
 * Документация: http://developers.binotel.ua/#listoflabels
 */
	
$result = $api->sendRequest('customers/listOfLabels', array());

if ($result['status'] === 'success') {
	var_dump($result['listOfLabels']);
} else {
	printf('REST API ошибка %s: %s %s', $result['code'], $result['message'], PHP_EOL);
}


