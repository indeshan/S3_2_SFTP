<?php
include 'init/init.php';
use phpseclib3\Net\SFTP;

function getS3FilesToLocal($s3, $bucket, $key) {
	echo "Checking for file ".$key. " on S3 bucket.",PHP_EOL;
	if($s3->doesObjectExist($bucket, $key)) {
		try {
			$cmd = $s3->getCommand('GetObject', [
				'Bucket' => $bucket,
				'Key'    => $key
			]);
			echo "Generating presigned URL for file ".$key, PHP_EOL;
			$request = $s3->createPresignedRequest($cmd, '+20 minutes');
			$url = (string) $request->getUri();
		} catch (Exception $e) {
			echo "Exception in getS3FilesToLocal";
			die("Error: " . $e->getMessage());
		}
		$filename = explode('/', $key);
		$filename = $filename[count($filename)-1];
		echo "Saving file".$key." on local.",PHP_EOL;
		file_put_contents('files/'.$filename, fopen($url, 'r'));
		echo $key." saved on local.",PHP_EOL;
	} else {
		echo $key.' File does not exist on S3.',PHP_EOL;
	}
}

function uploadFilesToSFTP() {
	echo "Getting local file list.",PHP_EOL;
	$path = "files/";
	$files = array_diff(scandir($path), array('.', '..'));
	$sftp = loginToSFTP();
	echo "Uploading files to SFTP.",PHP_EOL;
	foreach ($files as &$value) {
		echo $value,PHP_EOL;
		$localFilename = 'files/'.$value;
		$sftp->put($value, $localFilename, SFTP::SOURCE_LOCAL_FILE);
		unlink($localFilename);
	}
	echo "Uploading to SFTP is completed.",PHP_EOL;
}


$s3 = getS3Object();
$date = date("Ymd");
echo "Started downloading files form S3 to local.",PHP_EOL;
getS3FilesToLocal($s3, $config['s3']['bucket'], "FOLDER/PATH/WITH/FILENAME.EXTENTION");
getS3FilesToLocal($s3, $config['s3']['bucket'], "FOLDER/PATH/WITH/FILENAME.EXTENTION");
echo "Downloading files form S3 to local completed.",PHP_EOL;
uploadFilesToSFTP();
?>