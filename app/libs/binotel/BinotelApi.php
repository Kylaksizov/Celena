<?php

namespace app\libs\binotel;

class BinotelApi
{
	protected $key;
	protected $secret;

	protected $apiHost = 'https://api.binotel.com/api/';
	protected $apiVersion = '4.0';
	protected $apiFormat = 'json';

	protected $disableSSLChecks = false;

	public $debug = false;

	public function __construct($key, $secret, $apiHost = null, $apiVersion = null, $apiFormat = null) {
		$this->key = $key;
		$this->secret = $secret;

		if (!is_null($apiHost)) $this->apiHost = $apiHost;
		if (!is_null($apiVersion)) $this->apiVersion = $apiVersion;
		if (!is_null($apiFormat)) $this->apiFormat = $apiFormat;
	}

	public function sendRequest($url, array $params) {
		if ($this->debug) printf("[CLIENT] Send request: %s\n", json_encode($params));

		$params['key'] = $this->key;
		$params['secret'] = $this->secret;

		$postData = json_encode($params);

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $this->apiHost . $this->apiVersion .'/'. $url .'.'. $this->apiFormat);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			'Content-Length: '. mb_strlen($postData),
			'Content-Type: application/json'
		));
		curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

		if ($this->disableSSLChecks) {
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		}

		$result = curl_exec($ch);
		$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

		if (curl_errno($ch)) {
			if ($this->debug) printf("[CLIENT] curl_error: %s\n", curl_error($ch));
			return;
		}

		curl_close($ch);

		if ($this->debug) printf("[CLIENT] Server response code: %d\n", $code);

		if ($code !== 200) {
			if ($this->debug) printf("[CLIENT] Server error: %s\n", $result);
			return;
		}

		$decodeResult = json_decode($result, true);

		if (is_null($decodeResult)) {
			if ($this->debug) printf("[CLIENT] Server sent invalid data: %s\n", $result);
			return;
		}

		return $decodeResult;
	}


	/**
	 * Не используйте это. Очень НЕБЕЗОПАСНО. Отключения проверки подлинности SSL сертификата.
	 */
	public function disableSSLChecks() {
		$this->disableSSLChecks = true;
	}
}
