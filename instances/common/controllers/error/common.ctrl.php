<?php

namespace Common;

class ErrorCommonController extends \Sifo\Controller
{

	public function build()
	{

		$params = $this->getParams();
		// Params contain the exception in an array: 'code' => $e->http_code, 'msg' => $e->getMessage(), 'trace' => $e->getTraceAsString()
		switch ( $params['code'] )
		{
			// Codes that we have in templates:
			case ( $params['code'] >= 300 && $params['code'] <= 307 ):
				$this->setLayout( 'error/30x_redirect.tpl' );
				break;

			case 404:
				$this->setLayout( 'error/' . $params['code'] . '.tpl' );
				break;

			case 401:
			case 403:
				$this->setLayout( 'error/40x.tpl' );

				// Pass the referer for coming back after login:
				$this->assign( 'referer', $this->params['current_url'] );
				break;

			case 500:
				$this->setLayout( 'error/500.tpl' );
				break;
			default:
				$this->setLayout( 'error/common.tpl' );
		}

		// Assign error_code and error_code_msg so we can use it
		// eventually in the template.
		$this->assign( 'error_code', $params['code'] );
		$this->assign( 'error_code_msg', $params['code_msg'] );
		$this->assign( 'metadata', $this->getErrorMetadata() );

		// The error controller should not load modules to prevent major disasters.
		if ( $this->hasDebug() )
		{
			$this->assign( 'error', $params );
		}
	}

	/**
	 * Assign selected error code metadata to page to allow error translations.
	 *
	 * @return array
	 */
	public function getErrorMetadata()
	{
		try
		{
			$metadata = \Sifo\Config::getInstance()->getConfig( 'lang/metadata_' . $this->getParam( 'lang' ) );
		}
		catch ( Exception_Configuration $e )
		{
			$metadata = \Sifo\Config::getInstance()->getConfig( 'lang/metadata_en_US' );
		}

		$error_code = $this->getParam( 'code' );

		return ( isset( $metadata[$error_code] ) ) ? $metadata[$error_code] : false;
	}
}