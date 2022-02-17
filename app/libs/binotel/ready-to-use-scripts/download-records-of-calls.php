<?php

/*
	Скрипт для массового скачивания записей звонков.
	Скачанные записи разговора будут в созданной директории: download-records-of-calls
*/

/*
	ВНИМАНИЕ! В bootstrap.php - прописаны данные для авторизации и инициализация API библиотеки.
	Пожалуйста ознакомьтесь с этим файлом.
*/
require_once(__DIR__ .'/../bootstrap.php');


$startDay = 20;
$startMonth = 12;
$startYear = 2014;

$stopDay = 24;
$stopMonth = 12;
$stopYear = 2014;


/*
	Диапазон запроса по времени. Стандартное значение: 24 часов.
	Если будет ошибка "Диапазон запроса по времени слишком большой", необходи его уменьшить на 30 процентов.
*/
$intervalofRequests = 24 * (60 * 60);



$requestStartTime = $startTime = mktime(0, 0, 0, $startMonth, $startDay, $startYear);
$stopTime = mktime(23, 59, 59, $stopMonth, $stopDay, $stopYear);

$listOfCallsForDownload = array();

while ($requestStartTime < $stopTime) {
	$requestStopTime = (($requestStartTime + $intervalofRequests) < $stopTime ? $requestStartTime + $intervalofRequests : $stopTime);


	$result = $api->sendRequest('stats/outgoing-calls-for-period', array(
		'startTime' => $requestStartTime,
		'stopTime' => $requestStopTime
	));

	if ($result['status'] === 'success') {
		if (count($result['callDetails']) >= 2000) {
			die(sprintf('Диапазон запроса по времени слишком большой. Вам нужно его уменьшить на 30 процентов. %s', PHP_EOL));
		} else {
			foreach ($result['callDetails'] as $callData) {
				if ($callData['disposition'] === 'ANSWER') {
					$listOfCallsForDownload[$callData['generalCallID']] = $callData;
				}
			}
		}
	} else {
		printf('Что-то пошло не так! %s', PHP_EOL);
		printf('REST API ошибка %s: %s %s', $result['code'], $result['message'], PHP_EOL);
		exit;
	}

	sleep(10);
	

	$result = $api->sendRequest('stats/incoming-calls-for-period', array(
		'startTime' => $requestStartTime,
		'stopTime' => $requestStopTime
	));

	if ($result['status'] === 'success') {
		if (count($result['callDetails']) >= 2000) {
			die(sprintf('Диапазон запроса по времени слишком большой. Вам нужно его уменьшить на 30 процентов. %s', PHP_EOL));
		} else {
			foreach ($result['callDetails'] as $callData) {
				if ($callData['disposition'] === 'ANSWER') {
					$listOfCallsForDownload[$callData['generalCallID']] = $callData;
				}
			}
		}
	} else {
		printf('Что-то пошло не так! %s', PHP_EOL);
		printf('REST API ошибка %s: %s %s', $result['code'], $result['message'], PHP_EOL);
		exit;
	}


	sleep(10);

	$requestStartTime = (($requestStartTime + $intervalofRequests) < $stopTime ? $requestStartTime + $intervalofRequests : $stopTime);
}


printf('Записей разговоров для скачивания: %s %s%s', count($listOfCallsForDownload), PHP_EOL, PHP_EOL);


if (count($listOfCallsForDownload)) {
	foreach ($listOfCallsForDownload as $callData) {
		$attempts = 0;

		while (TRUE) {
			$result = $api->sendRequest('stats/call-record', array(
				'callID' => $callData['callID']
			));

			if ($result['status'] === 'success') {
				$directoryForDownloads = __DIR__ . DIRECTORY_SEPARATOR .'records-of-calls'. DIRECTORY_SEPARATOR . date('Y-m', $callData['startTime']) . DIRECTORY_SEPARATOR;

				if (!is_dir($directoryForDownloads)) {
					mkdir($directoryForDownloads, 0777, TRUE);
				}

				$callRecordContent = file_get_contents($result['url']);
				$callRecordFileName = date('d-m-Y_H-i', $callData['startTime']) .'_'. ($callData['callType'] === '0' ? $callData['externalNumber'] .'_incoming' : $callData['historyData'][0]['internalNumber'] .'_outgoing') .'.mp3';

				file_put_contents($directoryForDownloads . $callRecordFileName, $callRecordContent);

				break;
			} else {
				printf('Что-то пошло не так! %s', PHP_EOL);
				printf('REST API ошибка %s: %s %s', $result['code'], $result['message'], PHP_EOL);

				$attempts++;
				sleep(5);

				if ($attempts >= 10) {
					exit;
				}
			}
		}
	}
}


printf('Все записи разговоров успешно скачаны! %s%s', PHP_EOL, PHP_EOL);

