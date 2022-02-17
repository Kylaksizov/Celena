<?php

/**
 * Примеры категории SETTINGS.
 * Документация: http://developers.binotel.ua/#rest-api-settings
 */

/**
 * ВНИМАНИЕ! В bootstrap.php - прописаны данные для авторизации и инициализация API библиотеки.
 * Пожалуйста ознакомьтесь с этим файлом.
 */
require_once(__DIR__ .'/bootstrap.php');



/**
 * Пример 1: выбор всех сотрудников.
 * Документация: http://developers.binotel.ua/#list-of-employees
 */

$result = $api->sendRequest('settings/list-of-employees', array());

if ($result['status'] === 'success') {
	var_dump($result['listOfEmployees']);
} else {
	printf('REST API ошибка %s: %s %s', $result['code'], $result['message'], PHP_EOL);
}



/**
 * Пример 2: выбор всех сценариев для входящих звонков.
 * Документация: http://developers.binotel.ua/#list-of-routes
 */

$result = $api->sendRequest('settings/list-of-routes', array());

if ($result['status'] === 'success') {
	var_dump($result['listOfRoutes']);
} else {
	printf('REST API ошибка %s: %s %s', $result['code'], $result['message'], PHP_EOL);
}



/**
 * Пример 3: выбор всех голосовых сообщений.
 * Документация: http://developers.binotel.ua/#list-of-voice-files
 */

$result = $api->sendRequest('settings/list-of-voice-files', array());

if ($result['status'] === 'success') {
	var_dump($result['listOfVoiceFiles']);
} else {
	printf('REST API ошибка %s: %s %s', $result['code'], $result['message'], PHP_EOL);
}
