<?php
namespace Common;


class HomeIndexController extends SharedFirstLevelController
{
	protected $include_classes = array( 'FlashMessages', 'Pagination' );

	/**
	 * If you expect this controller to output a json response.
	 *
	 * @var boolean
	 */
	public $is_json = false;

	/**
	 * Because this class is extending from SharedFirstLevelController instead of Controller,
	 * some modules are executed in first place, like: head, header or footer.
	 *
	 * See template for inclusion.
	 */
	public function buildCommon()
	{

		$filtering_post = \Sifo\FilterPost::getInstance();

		// EXAMPLE OF HOW MODULES WORK. Add an advertising module:
		$this->addModule( 'ads_google_skyscrapper' , 'SharedAdvertising' );

		// TEST FORM SENT
		if ( $filtering_post->isSent( 'testform' ) )
		{
			$form = \Sifo\Form::getInstance( $filtering_post );

			if ( !$form->validateElements( 'forms/example.form' ) )
			{
				// Basic validation: The data sent has an invalid form.
				$errors = $form->getErrors();
				\Sifo\FlashMessages::set( $errors, \Sifo\FlashMessages::MSG_KO );

			}
			else
			{
				\Sifo\FlashMessages::set( "Validated data. Mai inglish is parfect!", \Sifo\FlashMessages::MSG_OK );
			}

			$this->assign( "form_fields", $form->getFields() );

		}

		// INVITE  SENT
		if ( $filtering_post->isSent( 'inviteform' ) )
		{
			if ( ( $account_email = $filtering_post->getEmail( 'account_email' ) ) && ( $account_password = $filtering_post->getString( 'account_password' ) ) )
			{
				if ( strstr( $account_email, '@yahoo.' ) || strstr( $account_email, '@ymail.' ) )
				{
					$account_provider = 'yahoo';
				}
				elseif ( strstr( $account_email, '@hotmail.' ) || strstr( $account_email, '@live.' ) || strstr( $account_email, '@msn.' ) )
				{
					$account_provider = 'hotmail';
				}
				else
				{
					$account_provider = 'gmail';
				}
			}
		}

		$smileys = array(
			':-)',
			':-(',
			'¬¬',
			'xD',
			':_(',
			':-0',
			'=)'
		);

		// Set a system message
		\Sifo\FlashMessages::set( '<strong>Installation correct!</strong> <small>(This is an example OK message.)</small>', \Sifo\FlashMessages::MSG_OK );

		//\Sifo\FlashMessages::set( 'Installation failed!', \Sifo\FlashMessages::MSG_KO );
		//\Sifo\FlashMessages::set( 'For your information...!', \Sifo\FlashMessages::MSG_INFO );
		//\Sifo\FlashMessages::set( 'Your account is incomplete', \Sifo\FlashMessages::MSG_WARNING );

		// Same message translated (include the message in messages_xx_XX.config.php first):
		// \Sifo\FlashMessages::set( $this->translate( 'Installation correct!' ) );

		// Same message translated with variable strings
		// \Sifo\FlashMessages::set( $this->translate( 'Installation correct! %1', $var1 ) );

		// Pass to the template a random smiley:
		$rand = array_rand( $smileys, 1 );
		$this->assign( 'mood', $smileys[$rand] );

		// Parameters in the application
		// var_dump( $this->getParams() );

		// SAMPLE: Get data from the database without a \SifoModel:
		// $this->assign( 'data', Db::getInstance()->getAll( 'SELECT * FROM accounts where id_account < ?', 20 ) );

		// With a \Sifo\Model
		// $user = new UserDataModel();
		// $user->getMyDataInsideMyClass();

		// Add another module (execute a controller and capture the output)
		// $this->addModule( 'name_used_in_tpl', 'Class' );

		// Add pagination
		// @See:  Quick reference for usage in pagination class.
		$pagination = new Pagination();

		$pagination->setNumTotalResults(2500);								// Set total number of items
		$pagination->setCurrentPage(5);										// Set current page.
		$pagination->setUrlBase( 'http://www.test.com/pag');				// Set url base & template.
		$pagination->setTemplate( 'home/pagination.tpl' );					// Set pagination template.

		// Parameters optionals:
		// $pagination->setItemsPerPage(5);									// Set items per page. By default 10.
		// $pagination->setMaxPages(20);									// Set maxim number of pages to show.
		// $pagination->setTemplateParam( 'title', 'pagination test' );		// Set a parameter assigned to the pagination template.
		// $pagination->setSeparatorLink( '-' );							// Set the default separator for the page link.
		// $pagination->setDisplayItemsPerPage( array( 10, 25, 50, 100 ) );	// Set display items per page.
		// $pagination->setWindowPage( 'default', 20 );						// Set page range ( window ). [ 'default'=> 15,	'short'	=> 12,'very_short'	=> 10]

		// Get pagination data and pass data to the template.
		$result = $pagination->getLinks();
		$this->assign( 'pagination_data', $result );

		// Note: To show the paginator, you should use the following smarty function in your template: {pagination data=$result}
		$this->setLayout( 'home/index.tpl' );

	}

	public function getCacheDefinition()
	{
		// No caching:
		return false;

		// The language is added automatically in the cache name from the Controller.
		$cache_name = 'home';

		// Caching for the default time:
		return  $cache_name;

		// Caching with custom expiration , 900 seconds.
		return array( 'name' => $cache_name, 'expiration' => 900 );
	}
}
?>