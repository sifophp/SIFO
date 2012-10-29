<?php
namespace Common;

include_once ROOT_PATH . '/instances/common/controllers/shared/commandLine.ctrl.php';
include_once ROOT_PATH . '/libs/Amazon/S3.php';

class ScriptsAmazonS3UploaderController extends SharedCommandLineController
{
	private $config_file;
	private $source;
	private $destination_uri = "";
	private $destination_bucket;

	private $ssl_default_value = false;
	private $acl = \S3::ACL_PRIVATE;

	private function uploadToBucket()
	{
		$this->showMessage( "Applying the {$this->config_file} Amazon configuration. ", self::VERBOSE );

		if ( !file_exists( $this->config_file ) )
		{
			return array(
				'result' => 'ERROR',
				'msg' => "File {$this->config_file} not found"
			);
		}

		include_once( $this->config_file );

		$config = isset( $config['S3'] ) ? $config['S3']:$config;

		if ( ( !isset( $config['awsAccessKey'] ) ) || ( !isset( $config['awsSecretKey'] ) ) )
		{
			return array(
				'result' => 'ERROR',
				'msg' => "File {$this->config_file} hasn't the required fields. Use the --help param for more info."
			);
		}

		if ( ( !isset( $this->destination_bucket ) ) && ( !isset( $config['defaultBucket'] ) ) )
		{
			return array(
				'result' => 'ERROR',
				'msg' => "Destination bucket was not found. Use --help for more info."
			);
		}

		$bucket = isset( $this->destination_bucket ) ? $this->destination_bucket : $config['defaultBucket'];
		$awsAccessKey = $config['awsAccessKey'];
		$awsSecretKey = $config['awsSecretKey'];
		$useSSL = isset( $config['useSSL'] )?$config['useSSL']:$this->ssl_default_value;
		$destination = implode( "/", array( $this->destination_uri, basename( $this->source ) ) );

		$censured_awsSecretKey = substr( $awsSecretKey, -5 );

		$this->showMessage( "Using '{$awsAccessKey}' acces key.", self::VERBOSE );
		$this->showMessage( "Using '...{$censured_awsSecretKey}' secret key.", self::VERBOSE );
		$this->showMessage( "Using bucket '{$bucket}'.", self::VERBOSE );
		$this->showMessage( "Destination file '{$destination}.", self::VERBOSE );

		if ( $useSSL )
		{
			$this->showMessage( "Using SSL.", self::VERBOSE );
		}
		else
		{
			$this->showMessage( "Without SSL.", self::VERBOSE );
		}

		$this->showMessage( "ACL '{$this->acl}'.", self::VERBOSE );


		$s3 = new \S3( $awsAccessKey, $awsSecretKey, false );
		if ( ( !$this->test) && ( !$s3->putObjectFile( $this->source, $bucket, $destination ) ) )
		{
			return array(
				'result' => 'ERROR',
				'msg' => "Some unexpected error uploading {$this->source}."
			);
		}

		$this->showMessage( "Really the file has not been uploaded because you're in TEST MODE.", self::TEST );
		return array(
			'result' => 'OK',
			'msg' => "File uploaded successfully."
		);

	}

	// ABSTRACTED METHODES:

	public function init()
	{

		$this->help_str = <<<HELP_TEXT
Use to upload a file to Amazon S3 bucket.
Specially useful to create dump files backups.
Is mandatoy define the config.

Config file expected format ( eg: amazon.php.config ):
<?php
	\$config['S3']['awsAccessKey'] = '<ACCESS_KEY>'; // Required,
	\$config['S3']['awsSecretKey'] = '<SECRET_KEY>'; // Required,
	\$config['S3']['useSSL'] = 'true/false'; // Default:{$this->ssl_default_value},
	\$config['S3']['defaultBucket'] = '<BUCKET_NAME>'; // Not required param.,
HELP_TEXT;

		$this->setNewParam( 'C', 'config', 'Define the necessary config file access path.', true, true );
		$this->setNewParam( 'S', 'source', 'Define the source file/directory.', true, true );
		$this->setNewParam( 'B', 'destination_bucket', 'Define the destination bucket. If it is not defined uses the default_bucket defined in the config file.', true, false );
		$this->setNewParam( 'U', 'destination_uri', 'Define the destination directory (in the bucket structure). Default: \'/\'', true, true );

		$acl_values = array( \S3::ACL_PRIVATE, \S3::ACL_AUTHENTICATED_READ, \S3::ACL_PUBLIC_READ, \S3::ACL_PUBLIC_READ_WRITE );
		$acl_values = implode( "|", $acl_values );
		$this->setNewParam( 'A', 'acl', "Access Control List. Default:'{$this->acl}'. Available values: {$acl_values}", true, false );
	}

	public function exec()
	{
		$this->showMessage( "Starting the script", self::VERBOSE );

		foreach ( $this->command_options as $option )
		{
			switch ( $option[0] )
			{
				case "C":
				case "config":
					$this->config_file = $option[1];
					break;
				case "S":
				case "source":
					$this->source = $option[1];
					break;
				case "B":
				case "destination_bucket":
					$this->destination_bucket = $option[1];
					break;
				case "U":
				case "destination_uri":
					$this->destination_uri = $option[1];
					break;
				case "A":
				case "acl":
					$this->acl = $option[1];
					break;
			}
		}

		$result = $this->uploadToBucket();

		$this->showMessage( implode( ":", $result ), self::ALL );

		$this->showMessage( "Finishing!", self::VERBOSE );
	}
}
?>