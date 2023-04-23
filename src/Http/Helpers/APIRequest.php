<?php
namespace Saidtech\Zpay\Http\Helpers;

use phpseclib3\Crypt\RSA;
use phpseclib3\Crypt\PublicKeyLoader;
use Exception;

class APIRequest{
	
	var $context;
	private $session;
	// Constructer context
	function __construct($context) {
		$this->context = $context;
		$this->session = $context->getSessionToken();
	}
	
	// Does the HTTP Request
	function execute() {
		if ($this->context == null) {
			throw new Exception('Context cannot be null');
		} 
		$this->create_default_headers();
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_VERBOSE, 1);
		curl_setopt($ch, CURLOPT_HEADER, 1);
		
		switch ($this->context->get_method_type()) {
			case APIMethodType::GET:
				return $this->__get($ch);
			case APIMethodType::POST:
				return $this->__post($ch);
			case APIMethodType::PUT:
				return $this->__put($ch);
			default:
				return null;
		}
	}

	function executePostRequest() {
		if ($this->context == null) {
			throw new Exception('Context cannot be null');
		} 
		$this->create_headers();
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_VERBOSE, 1);
		curl_setopt($ch, CURLOPT_HEADER, 1);
		
		switch ($this->context->get_method_type()) {
			case APIMethodType::GET:
				return $this->__get($ch);
			case APIMethodType::POST:
				return $this->__post($ch);
			case APIMethodType::PUT:
				return $this->__put($ch);
			default:
				return null;
		}
	}

	
	// Creates the Authorisation bearer token using the RSA public key provided
	function create_bearer_token() {
		// Need to do these lines to create a 'valid' formatted RSA key for the openssl library
		$rsa = PublicKeyLoader::load($this->context->get_public_key());

		$publickey = $rsa;
		$api_encrypted = '';
		$encrypted = '';
		
		if (openssl_public_encrypt($this->context->get_api_key(), $encrypted, $publickey)) {
			$api_encrypted = base64_encode($encrypted);
		}
		return $api_encrypted;
	}

	function encryptKey($key): String
	{
		$rsa = PublicKeyLoader::load($this->context->get_public_key());
		openssl_public_encrypt($key, $encrypted, $rsa);
        return base64_encode($encrypted);
	}
	
	// Add the default headers
	function create_default_headers() {
		$this->context->add_header('Authorization', 'Bearer ' . $this->create_bearer_token());
		$this->context->add_header('Content-Type','application/json');
		$this->context->add_header('Host', $this->context->get_address());
	}

	function create_headers() {
		$this->context->add_header('Authorization', 'Bearer ' . $this->encryptKey($this->context->getSessionToken()));
		$this->context->add_header('Content-Type','application/json');
	}


	
	// Do a GET request
	function __get($ch) {
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
		curl_setopt($ch, CURLOPT_URL, $this->context->get_url().'?'.http_build_query($this->context->get_parameters()));
		curl_setopt($ch, CURLOPT_HTTPHEADER, $this->context->get_headers());
		$response = curl_exec($ch);

        
		$status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		$header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
		$headers = substr($response, 0, $header_size);
		$body = substr($response, $header_size);
		curl_close($ch);
		return new APIResponse($status_code, $headers, $body);
	}
	
	// Do a POST request
	function __post($ch) {
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
		curl_setopt($ch, CURLOPT_URL, $this->context->get_url());
		curl_setopt($ch, CURLOPT_HTTPHEADER, $this->context->get_headers());
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($this->context->get_parameters()));
		$response = curl_exec($ch);
		$status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		$header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
		$headers = substr($response, 0, $header_size);
		$body = substr($response, $header_size);
		curl_close($ch);
		return new APIResponse($status_code, $headers, $body);
	}
	
	// Do a PUT request
	function __put($ch) {
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
		curl_setopt($ch, CURLOPT_URL, $this->context->get_url());
		curl_setopt($ch, CURLOPT_HTTPHEADER, $this->context->get_headers());
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($this->context->get_parameters()));
		$response = curl_exec($ch);
		echo $response;
		echo '<br>';
		$status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		$header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
		$headers = substr($response, 0, $header_size);
		$body = substr($response, $header_size);
		curl_close($ch);
		return new APIResponse($status_code, $headers, $body);
		
	}
	
	function __unknown() {
		throw new Exception('Unknown method');
	}
}