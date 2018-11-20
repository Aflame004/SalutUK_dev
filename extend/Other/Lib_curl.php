<?php
namespace Other;

class Lib_curl
{
	public function http($url, $method, $postfields = null, $headers = array(), $debug = false)
	{
		$ci = curl_init();
		/* Curl settings */
		curl_setopt($ci, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
		curl_setopt($ci, CURLOPT_CONNECTTIMEOUT, 30);
		curl_setopt($ci, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ci, CURLOPT_SSL_VERIFYHOST, FALSE);
		curl_setopt($ci, CURLOPT_TIMEOUT, 30);
		curl_setopt($ci, CURLOPT_RETURNTRANSFER, true);

		switch ($method)
		{
			case 'POST':
			curl_setopt($ci, CURLOPT_POST, true);
			if (!empty($postfields)) {
			curl_setopt($ci, CURLOPT_POSTFIELDS, $postfields);
			$this->postdata = $postfields;
		}
			break;
		}
		curl_setopt($ci, CURLOPT_URL, $url);
		curl_setopt($ci, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ci, CURLINFO_HEADER_OUT, true);

		$response = curl_exec($ci);
		$http_code = curl_getinfo($ci, CURLINFO_HTTP_CODE);

		if ($debug)
		{
			echo "=====post data======\r\n";
			var_dump($postfields);

			echo '=====info=====' . "\r\n";
			print_r(curl_getinfo($ci));

			echo '=====$response=====' . "\r\n";
			print_r($response);
		}
		curl_close($ci);
		return array($http_code, $response);
	}
}



?>