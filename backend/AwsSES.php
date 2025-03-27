<?php
require __DIR__ . '../../vendor/autoload.php';
require_once __DIR__ . '../../config.php';
require_once __DIR__ . '/../backend/lib/lib.php';

use Aws\Exception\AwsException;
use Aws\Ses\SesClient;

class AwsSES
{

	private static $sesClient = null;

	public function __construct() {}

	public static function getSesClient()
	{
		if (self::$sesClient === null) {
			self::$sesClient = new SesClient([
				'version' => AWS_SES_VERSION,
				'region' => AWS_REGION,
				'credentials' => [
					'key'    => AWS_ACCESS_KEY,
					'secret' => AWS_SECRET_KEY,
				],
			]);
		}

		return self::$sesClient;
	}

	public function sendSesEmail()
	{

		$email = $this->validation_request();

		try {
			$sesClient = self::getSesClient();

			$sesClient->sendEmail(
				$this->getMailObject($email)
			);

			echo json_encode(['success' => 'An email was sent to address:'. $email['to'][0] ]);

		} catch (AwsException $e) {
			// output error message if fails
			echo  json_encode(['error' => $e->getAwsErrorMessage()]);
		}
	}

	private function validation_request()
	{
		$handle_request = new HandleRequest();
		$payload = $handle_request
		->is_post()
		->is_contentType('application/json')
		->get_payload()
		->accept();

		if(!empty($payload['error_message']))
		{
			echo json_encode(['error' => $payload['error_message']]);
			die;
		}

		return $payload['data'];

	}

	private function getMailObject($email)
	{
		return [
			'Destination' => [
				'ToAddresses' => $email['to'] ?? [],
				'CcAddresses' => $email['cc'] ?? [],
				'BccAddresses' => $email['bcc'] ?? [],
			],
			'ReplyToAddresses' =>  [$email['from']] ?? [],
			'Source' =>  $email['from'] ?? '',
			'Message' => [
				'Body' => [

					'Text' => [
						'Charset' => CHAR_SET,
						'Data' => $email['body'] ?? '',
					],
				],
				'Subject' => [
					'Charset' => CHAR_SET,
					'Data' => $email['subject'] ?? '',
				],
			],

		];
	}
}
