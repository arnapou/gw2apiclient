<?php

/*
 * This file is part of the Arnapou GW2 API Client package.
 *
 * (c) Arnaud Buathier <arnaud@arnapou.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Arnapou\GW2Api\Tool;

class CurlResponse {

	/**
	 *
	 * @var resource
	 */
	protected $curl;

	/**
	 *
	 * @var array
	 */
	protected $info = array();

	/**
	 *
	 * @var array
	 */
	protected $headers = array();

	/**
	 *
	 * @var string
	 */
	protected $responseHeader = '';

	/**
	 *
	 * @var string
	 */
	protected $responseContent = '';

	/**
	 *
	 * @var array
	 */
	protected $curlErrorCodes = array(
		1 => 'CURLE_UNSUPPORTED_PROTOCOL',
		2 => 'CURLE_FAILED_INIT',
		3 => 'CURLE_URL_MALFORMAT',
		4 => 'CURLE_URL_MALFORMAT_USER',
		5 => 'CURLE_COULDNT_RESOLVE_PROXY',
		6 => 'CURLE_COULDNT_RESOLVE_HOST',
		7 => 'CURLE_COULDNT_CONNECT',
		8 => 'CURLE_FTP_WEIRD_SERVER_REPLY',
		9 => 'CURLE_REMOTE_ACCESS_DENIED',
		11 => 'CURLE_FTP_WEIRD_PASS_REPLY',
		13 => 'CURLE_FTP_WEIRD_PASV_REPLY',
		14 => 'CURLE_FTP_WEIRD_227_FORMAT',
		15 => 'CURLE_FTP_CANT_GET_HOST',
		17 => 'CURLE_FTP_COULDNT_SET_TYPE',
		18 => 'CURLE_PARTIAL_FILE',
		19 => 'CURLE_FTP_COULDNT_RETR_FILE',
		21 => 'CURLE_QUOTE_ERROR',
		22 => 'CURLE_HTTP_RETURNED_ERROR',
		23 => 'CURLE_WRITE_ERROR',
		25 => 'CURLE_UPLOAD_FAILED',
		26 => 'CURLE_READ_ERROR',
		27 => 'CURLE_OUT_OF_MEMORY',
		28 => 'CURLE_OPERATION_TIMEDOUT',
		30 => 'CURLE_FTP_PORT_FAILED',
		31 => 'CURLE_FTP_COULDNT_USE_REST',
		33 => 'CURLE_RANGE_ERROR',
		34 => 'CURLE_HTTP_POST_ERROR',
		35 => 'CURLE_SSL_CONNECT_ERROR',
		36 => 'CURLE_BAD_DOWNLOAD_RESUME',
		37 => 'CURLE_FILE_COULDNT_READ_FILE',
		38 => 'CURLE_LDAP_CANNOT_BIND',
		39 => 'CURLE_LDAP_SEARCH_FAILED',
		41 => 'CURLE_FUNCTION_NOT_FOUND',
		42 => 'CURLE_ABORTED_BY_CALLBACK',
		43 => 'CURLE_BAD_FUNCTION_ARGUMENT',
		45 => 'CURLE_INTERFACE_FAILED',
		47 => 'CURLE_TOO_MANY_REDIRECTS',
		48 => 'CURLE_UNKNOWN_TELNET_OPTION',
		49 => 'CURLE_TELNET_OPTION_SYNTAX',
		51 => 'CURLE_PEER_FAILED_VERIFICATION',
		52 => 'CURLE_GOT_NOTHING',
		53 => 'CURLE_SSL_ENGINE_NOTFOUND',
		54 => 'CURLE_SSL_ENGINE_SETFAILED',
		55 => 'CURLE_SEND_ERROR',
		56 => 'CURLE_RECV_ERROR',
		58 => 'CURLE_SSL_CERTPROBLEM',
		59 => 'CURLE_SSL_CIPHER',
		60 => 'CURLE_SSL_CACERT',
		61 => 'CURLE_BAD_CONTENT_ENCODING',
		62 => 'CURLE_LDAP_INVALID_URL',
		63 => 'CURLE_FILESIZE_EXCEEDED',
		64 => 'CURLE_USE_SSL_FAILED',
		65 => 'CURLE_SEND_FAIL_REWIND',
		66 => 'CURLE_SSL_ENGINE_INITFAILED',
		67 => 'CURLE_LOGIN_DENIED',
		68 => 'CURLE_TFTP_NOTFOUND',
		69 => 'CURLE_TFTP_PERM',
		70 => 'CURLE_REMOTE_DISK_FULL',
		71 => 'CURLE_TFTP_ILLEGAL',
		72 => 'CURLE_TFTP_UNKNOWNID',
		73 => 'CURLE_REMOTE_FILE_EXISTS',
		74 => 'CURLE_TFTP_NOSUCHUSER',
		75 => 'CURLE_CONV_FAILED',
		76 => 'CURLE_CONV_REQD',
		77 => 'CURLE_SSL_CACERT_BADFILE',
		78 => 'CURLE_REMOTE_FILE_NOT_FOUND',
		79 => 'CURLE_SSH',
		80 => 'CURLE_SSL_SHUTDOWN_FAILED',
		81 => 'CURLE_AGAIN',
		82 => 'CURLE_SSL_CRL_BADFILE',
		83 => 'CURLE_SSL_ISSUER_ERROR',
		84 => 'CURLE_FTP_PRET_FAILED',
		84 => 'CURLE_FTP_PRET_FAILED',
		85 => 'CURLE_RTSP_CSEQ_ERROR',
		86 => 'CURLE_RTSP_SESSION_ERROR',
		87 => 'CURLE_FTP_BAD_FILE_LIST',
		88 => 'CURLE_CHUNK_FAILED'
	);

	/**
	 *
	 * @var int
	 */
	protected $curlErrorCode = null;

	/**
	 *
	 * @var string
	 */
	protected $curlError = null;

	/**
	 * 
	 * @param resource $curl
	 */
	public function __construct($curl) {
		$this->curl = $curl;

		$response = curl_exec($this->curl);
		$this->parseResponse($response);

		$this->info = curl_getinfo($this->curl);
		$this->curlErrorCode = curl_errno($this->curl);
		if ($this->curlErrorCode) {
			$this->curlError = curl_error($this->curl);
		}
		@curl_close($this->curl);
	}

	/**
	 * 
	 * @param string $response
	 */
	protected function parseResponse(&$response) {
		if (preg_match('!^(.*?)\r?\n\r?\n(.*)$!si', $response, $m)) {
			$this->responseHeader = $m[1];
			foreach (explode("\n", $this->responseHeader) as $line) {
				if (preg_match('!^([^:]+):(.+)$!', $line, $mm)) {
					$k = strtolower(trim($mm[1]));
					$v = trim($mm[2]);
					if (isset($this->headers[$k])) {
						$this->headers[$k] .= ', ' . $v;
					}
					else {
						$this->headers[$k] = $v;
					}
				}
				else {
					$this->headers[] = $line;
				}
			}
			$this->responseContent = $m[2];
		}
		else {
			$this->responseContent = $response;
		}
	}

	/**
	 * 
	 * @return array
	 */
	public function getHeaders() {
		return $this->headers;
	}

	/**
	 * 
	 * @return string|null
	 */
	public function getHeader($directive) {
		$directive = strtolower($directive);
		if (isset($this->headers[$directive])) {
			return $this->headers[$directive];
		}
		return null;
	}

	/**
	 * 
	 * @return string
	 */
	public function getContent() {
		return $this->responseContent;
	}

	/**
	 * 
	 * @return int
	 */
	public function getErrorCode() {
		return $this->curlErrorCode ? $this->curlErrorCode : 0;
	}

	/**
	 * 
	 * @return string|null
	 */
	public function getErrorTitle() {
		if ($this->curlErrorCode) {
			if (isset($this->curlErrorCodes[$this->curlErrorCode])) {
				return $this->curlErrorCodes[$this->curlErrorCode];
			}
			return 'CURL_UNKOWN';
		}
		return null;
	}

	/**
	 * 
	 * @return string|null
	 */
	public function getErrorDetail() {
		return $this->curlError;
	}

	/**
	 * 
	 * @return string The request string sent. For this to work, add the CURLINFO_HEADER_OUT option to the handle by calling curl_setopt()
	 */
	public function getInfoRequestHeader() {
		return $this->getInfo('request_header');
	}

	/**
	 * 
	 * @return mixed 
	 */
	public function getInfoCertinfo() {
		return $this->getInfo('certinfo');
	}

	/**
	 * 
	 * @return mixed Time in seconds of all redirection steps before final transaction was started
	 */
	public function getInfoRedirectTime() {
		return $this->getInfo('redirect_time');
	}

	/**
	 * 
	 * @return mixed Time in seconds until the first byte is about to be transferred
	 */
	public function getInfoStartTransferTime() {
		return $this->getInfo('starttransfer_time');
	}

	/**
	 * 
	 * @return mixed Specified size of upload
	 */
	public function getInfoUploadContentLength() {
		return $this->getInfo('upload_content_length');
	}

	/**
	 * 
	 * @return mixed content-length of download, read from Content-Length: field
	 */
	public function getInfoDownloadContentLength() {
		return $this->getInfo('download_content_length');
	}

	/**
	 * 
	 * @return mixed Average upload speed
	 */
	public function getInfoSpeedUpload() {
		return $this->getInfo('speed_upload');
	}

	/**
	 * 
	 * @return mixed Average download speed
	 */
	public function getInfoSpeedDownload() {
		return $this->getInfo('speed_download');
	}

	/**
	 * 
	 * @return int Total number of bytes downloaded
	 */
	public function getInfoSizeDownload() {
		return $this->getInfo('size_download');
	}

	/**
	 * 
	 * @return int Total number of bytes uploaded
	 */
	public function getInfoSizeUpload() {
		return $this->getInfo('size_upload');
	}

	/**
	 * 
	 * @return mixed Time in seconds from start until just before file transfer begins
	 */
	public function getInfoPreTransferTime() {
		return $this->getInfo('pretransfer_time');
	}

	/**
	 * 
	 * @return mixed Time in seconds it took to establish the connection
	 */
	public function getInfoConnectTime() {
		return $this->getInfo('connect_time');
	}

	/**
	 * 
	 * @return mixed Time in seconds until name resolving was complete
	 */
	public function getInfoNamelookupTime() {
		return $this->getInfo('namelookup_time');
	}

	/**
	 * 
	 * @return mixed Total transaction time in seconds for last transfer
	 */
	public function getInfoTotalTime() {
		return $this->getInfo('total_time');
	}

	/**
	 * 
	 * @return mixed Number of redirects
	 */
	public function getInfoRedirectCount() {
		return $this->getInfo('redirect_count');
	}

	/**
	 * 
	 * @return mixed Result of SSL certification verification requested by setting CURLOPT_SSL_VERIFYPEER
	 */
	public function getInfoSslVerifyResult() {
		return $this->getInfo('ssl_verify_result');
	}

	/**
	 * 
	 * @return mixed Remote time of the retrieved document, if -1 is returned the time of the document is unknown
	 */
	public function getInfoFiletime() {
		return $this->getInfo('filetime');
	}

	/**
	 * 
	 * @return mixed Last effective URL
	 */
	public function getInfoEffectiveUrl() {
		return $this->getInfo('url');
	}

	/**
	 * 
	 * @return mixed Content-Type: of the requested document, NULL indicates server did not send valid Content-Type: header
	 */
	public function getInfoContentType() {
		return $this->getInfo('content_type');
	}

	/**
	 * 
	 * @return mixed Last received HTTP code
	 */
	public function getInfoHttpCode() {
		return (int) $this->getInfo('http_code');
	}

	/**
	 * 
	 * @return mixed Total size of all headers received
	 */
	public function getInfoHeaderSize() {
		return $this->getInfo('header_size');
	}

	/**
	 * 
	 * @return mixed Total size of issued requests, currently only for HTTP requests
	 */
	public function getInfoRequestSize() {
		return $this->getInfo('request_size');
	}

	/**
	 * 
	 * @param string $key
	 * @return string|null
	 */
	protected function getInfo($key) {
		if (isset($this->info[$key])) {
			return $this->info[$key];
		}
		return null;
	}

}
