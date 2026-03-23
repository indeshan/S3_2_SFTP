<?php
	require 'vendor/autoload.php';

	use Aws\S3\S3Client;
	use phpseclib3\Net\SFTP;

	function getS3Object() {
		global $config;
		try {
			echo "Connecting to S3.",PHP_EOL;
			$s3 = S3Client::factory([
				'version' => $config['s3']['version'],
				'region'  => $config['s3']['region'],
				'credentials' => [
					'key' => $config['s3']['key'],
					'secret'  => $config['s3']['secret'],
				]
			]);
			return $s3;
		} catch (Exception $e) {
			echo "Exception while creating factory object.";
			die("Error: " . $e->getMessage());
		}
	}

	function loginToSFTP() {
		global $config;
		echo "Connecting to SFTP.",PHP_EOL;
		$sftp = new SFTP($config['ftp']['ftp_host']);
		echo "Loging into SFTP.",PHP_EOL;
		if(!$sftp->login($config['ftp']['ftp_user'], $config['ftp']['ftp_pass'])) {
			die("SFTP Connection failed");
		}
		echo "Changed DIR on SFTP to ".$config['ftp']['ftp_dir'],PHP_EOL;
		$sftp->chdir($config['ftp']['ftp_dir']);
		//$sftp->login($config['ftp']['ftp_user'], $config['ftp']['ftp_pass']);
		return $sftp;
	}

	$config = [
		's3' =>	[
			'key'		=>	'AWS_S3_KEY',
			'secret'	=>	'AWS_S3_SECRET',
			'bucket'	=>	'AWS_S3_BUCKET',
			'version'	=>	'latest',
			'region'	=>	'us-west-2'
		],
		'ftp'=>	[
			'ftp_host'	=>	'SFTP_HOST',
			'ftp_user'	=>	'SFTP_USER',
			'ftp_pass'	=>	'SFTP_PASS',
			'ftp_dir'	=>	'SFTP_DIR'
		]
	];
?>