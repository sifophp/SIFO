<?php
namespace Common;

namespace Common;
/**
 * Class Pagination.
 *
 * This class encapsulates pagination functionality.
 *
 * @package SEOFramework
 * @subpackage Pagination
 * @author Manuel Carballar <manu.carballar@gmail.com>
 * @copyright 2010 SEOFramework
 * @version 0.1
 * @link SEOFramework/docs/index.html#pagination
 */

/**
 * Class Pagination.
 *
 * This class is created to automate the process of get pagination data. The class
 * uses methods to set the total registers number, items per page, current page, the URL base
 * to put in the page links, set the maximun number of pages to show and other methods.
 *
 * <b>===Quick reference for usage===</b>
 * <code>
 *
 * // New object Pagination:
 * $pagination = new Pagination(); ( Equivalent to new Pagination() in a controller ).
 *
 * // Set total number of items
 * $pagination->setNumTotalResults(150);
 *
 * // Set current page.
 * $pagination->setCurrentPage(2);
 *
 * // Set url base.
 * $pagination->setUrlBase( 'htttp://www.test.com ')
 *
 * // Set the pagination template.
 * $pagination->setTemplate( 'search/pagination.tpl' );
 *
 * // Get pagination data.
 * $result = $pagination->getLinks()
 *
 * To show the paginator, you should use the following smarty function in your template:
 * {pagination data=$result}
 * </code>
 *
 * @package SEOFramework
 * @subpackage Pagination
 * @author Manuel Carballar <manu.carballar@sgmail.com>
 */
class Pagination
{
	/**
	 * Number of items per page.
	 *
	 * @var integer
	 */
	protected $items_per_page = 10;

	/**
	 * Values to display items per page.
	 *
	 * @var array
	 */
	protected $display_items_per_page = array();

	/**
	 * The total number of items.
	 *
	 * @var integer.
	 */
	protected $items_total;

	/**
	 * Current page number (starting from 1).
	 *
	 * @var integer
	 */
	protected $current_page_number = 1;

	/**
	 * Number of pages.
	 *
	 * @var integer
	 */
	protected $num_pages;

	/**
	 * Maxim number of pages to show.
	 *
	 * @var integer
	 */
	protected $max_num_pages = null;

	/**
	 * Previous page number.
	 *
	 * @var ingeter
	 */
	protected $prev_num_page;

	/**
	 * Next page number.
	 *
	 * @var integer
	 */
	protected $next_num_page;

	/**
	 * The URL base for the page link.
	 *
	 * @var string
	 */
	protected $url_base = null;

	/**
	 * The default separator for the page link.
	 *
	 * @var string
	 */
	protected $separator_link_page = '/';

	/**
	 * Number of local pages (i.e., the number of discrete page numbers that will be displayed, including the current page number).
	 *
	 * @var integer
	 */
	protected $page_range;

	/**
	 * Values availables to set page_range ( window ).
	 *
	 * @var integer
	 */
	protected $availables_page_range = array(
		'default' 		=> 15,
		'short'			=> 12,
		'very_short'	=> 10
	);

	/**
	 * Start page number to page range.
	 *
	 * @var integer
	 */
	protected $start_page_range;

	/**
	 * End page number to page range.
	 *
	 * @var integer
	 */
	protected $end_page_range;

	/**
	 * Template name.
	 *
	 * @var string
	 */
	protected $layout_template;

	/**
	 * Config object.
	 *
	 * @var \Sifo\Config
	 */
	protected $config;

	/**
	 * Array of params to use in template (keywords, brand names, etc).
	 *
	 * @var array
	 */
	public $params_template;

	/**
	 * Init pagination data.
	 */
	public function __construct()
	{
		// Init data.
		$this->current_page_number 	= 1;
		$this->items_total 			= 0;
		$this->num_pages 			= 0;
		$this->page_range 			= $this->availables_page_range['default'];
		$this->start_page_range 	= 0;
		$this->end_page_range 		= 0;
		$this->params_template 		= array();
		$this->layout_template		= null;
		$this->setDisplayItemsPerPage( null );
		$this->config = \Sifo\Config::getInstance();
	}

	/**
	 * Must-call method. "Real" constructor.
	 *
	 * @param Config $config Config object.
	 * @param CacheInterface $cache Cache object.
	 */
/*	public function setInstance( Config $config, CacheInterface $cache )
	{
		$this->config = $config;
		$this->cache = $cache;
	}
*/

	/**
	 * Get pagination data.
	 *
	 * @return array
	 */
	public function getLinks()
	{
		if ( is_null( $this->getUrlBase() ) )
		{
			trigger_error( 'Pagination getLinks: Is necessary to set URL base for the page link with \'setUrlBase( $url )\' method.' );
			return false;
		}

		if ( is_null( $this->layout_template ) )
		{
			trigger_error( 'Pagination getLinks: Is necessary to set the pagination template for the page link with \'setTemplate( $layout_template )\' method.' );
			return false;
		}

		// Set number of pages to show.
		$this->num_pages = (int)ceil( ( $this->items_total / $this->items_per_page ) );

		// Check for max_page ( for Google ).
		if ( !is_null( $this->max_num_pages ) && $this->num_pages > $this->max_num_pages )
		{
			$this->num_pages = $this->max_num_pages;
		}

		// Throw exception when page is higher than current page.
		if ( $this->current_page_number > $this->num_pages )
		{
			throw new Sifo\Exception_404( 'Pagination error: trying to retrieve an unexisting page' );
		}

		return $this->getPaginationData();
	}

	/**
	 * Get pagination data.
	 *
	 * @return array
	 */
	protected function getPaginationData()
	{
		// Choose page range ( window ) and set start/end page range.
		$this->setPageRange();

		// Set previous/next page number.
		$this->setPrevNumPage();
		$this->setNextNumPage();

		$pagination['template']		= $this->layout_template;
		$pagination['items_total'] 	= $this->getNumTotalResults();
		$pagination['item_start']		= ( ( $this->prev_num_page * $this->items_per_page ) + 1 );
		$pagination['item_end']		= ( $this->current_page_number * $this->items_per_page );
		$pagination['params']			= $this->params_template;
		if ( $pagination['item_end'] > $this->getNumTotalResults() )
		{
			$pagination['item_end'] = $this->getNumTotalResults();
		}
		$pagination['num_pages']		= $this->num_pages;
		$pagination['current_page']	= $this->current_page_number;

		// Links.
		for ( $i = $this->start_page_range; $i<= $this->end_page_range; $i++ )
		{
			$pagination['pages'][$i]['number']		= (int)$i;
		 	$pagination['pages'][$i]['is_current'] 	= ( $i == $this->current_page_number );

		 	$pagination['pages'][$i]['link'] 		= $this->url_base;
		 	if ( 1 != $i )
		 	{
		 		$pagination['pages'][$i]['link'] 	= $this->url_base . $this->separator_link_page . $i;
		 	}

		}

		if ( $this->prev_num_page > 0 )
		{
			$pagination['previous_page']['number'] 	= $this->prev_num_page;

			$pagination['previous_page']['link'] 		= $this->url_base;
			if ( 1 != $this->prev_num_page )
			{
				$pagination['previous_page']['link'] = $this->url_base . $this->separator_link_page . $this->prev_num_page;
			}
		}

		if ( $this->next_num_page > 0 )
		{
			$pagination['next_page']['number'] 		= $this->next_num_page;
			$pagination['next_page']['link'] 			= $this->url_base . $this->separator_link_page . $this->next_num_page;;
		}

		if ( 1 != $this->current_page_number )
		{
			$pagination['first_page']['number'] 		= 1;
			$pagination['first_page']['link'] 		= $this->url_base;
		}

		if ( $this->num_pages != $this->current_page_number )
		{
			$pagination['last_page']['number']	 	= $this->num_pages;
			$pagination['last_page']['link'] 			= $this->url_base . $this->separator_link_page . $this->num_pages;
		}

		$pagination['items_per_page'] 		= $this->items_per_page;
		$pagination['display_items_per_page'] = $this->display_items_per_page;

		return $pagination;
	}

	/**
	 * Get the url variable.
	 *
	 * @return string
	 */
	protected function getUrlBase()
	{
		return $this->url_base;
	}

	/**
	 * Get the total number of items.
	 *
	 * @return integer
	 */
	protected function getNumTotalResults()
	{
		return $this->items_total;
	}

	/**
	 * Set previous page number.
	 */
	protected function setPrevNumPage()
	{
		$this->prev_num_page = 0;
		if ( $this->current_page_number > 1 )
		{
			$this->prev_num_page = ( $this->current_page_number - 1 );
		}
	}

	/**
	 * Set next page number.
	 */
	protected function setNextNumPage()
	{
		$this->next_num_page = 0;
		if ( $this->current_page_number < $this->num_pages )
		{
			$this->next_num_page = ( $this->current_page_number + 1 );
		}
	}

	/**
	 * Calculate the start and end of the page range ( windows ) and his possible deviations.
	 */
	protected function setPageRange()
	{
		$this->page_range = $this->availables_page_range['default'];
		if ( $this->current_page_number > 9992 )
		{
			$this->page_range = $this->availables_page_range['very_short'];
		}
		elseif ( $this->current_page_number > 986 )
		{
			$this->page_range = $this->availables_page_range['short'];
		}

		$this->page_range = ( $this->page_range - 1 );
		$this->start_page_range = $this->current_page_number;
		$this->end_page_range = ( $this->current_page_number + $this->page_range );
		if ( $this->end_page_range > $this->num_pages )
		{
			 $this->end_page_range = $this->num_pages;
		}

		// Calculate deviations at the start or end of the page range ( window ).
		$extra = ( $this->page_range - ( $this->end_page_range - $this->start_page_range ) );
		if ( $extra > 0 )
		{
			if ( $this->end_page_range == $this->num_pages )
			{
				$this->start_page_range = ( $this->start_page_range - $extra );
				if ( $this->start_page_range < 1 )
				{
					$this->start_page_range = 1;
				}
			}
			else
			{
				$this->end_page_range = ( $this->end_page_range + $extra );
				if ( $this->end_page_range > $this->num_pages )
				{
					$this->end_page_range = $this->num_pages;
				}
			}
		}
	}

	/**
	 * Sets the current page number.
	 *
	 * @param integer $page_number Page number.
	 */
	public function setCurrentPage( $page_number )
	{
		if ( !is_int( $page_number ) )
		{
			trigger_error( 'Pagination setCurrentPage: Page number must be an integer.' );
			return false;
		}

		if ( $page_number <= 0 )
		{
			trigger_error( 'Pagination setCurrentPage: Page number must be a positive integer.' );
			return false;
		}

		$this->current_page_number = $page_number;
	}

	/**
	 * Set number of items per page.
	 *
	 * @param integer $items_per_page Number of items per page.
	 */
	public function setItemsPerPage( $items_per_page )
	{
		if ( !is_int( $items_per_page ) )
		{
			trigger_error( 'Pagination setItemsPerPage: Items per page must be an integer.' );
			return false;
		}

		if ( $items_per_page <= 0 )
		{
			trigger_error( 'Pagination setItemsPerPage: Items per page must be a positive integer.' );
			return false;
		}

		$this->items_per_page = $items_per_page;
	}

	/**
	 * Set maxim number of pages to show.
	 *
	 * @param integer $max_num_pages Maxim number of pages.
	 */
	public function setMaxPages( $max_num_pages )
	{
		if ( !is_int( $max_num_pages ) )
		{
			trigger_error( 'Pagination setMaxPages:  Maxim number of pages must be an integer.' );
			return false;
		}

		if ( $max_num_pages <= 0 )
		{
			trigger_error( 'Pagination setMaxPages:  Maxim number of pages must be a positive integer.' );
			return false;
		}

		$this->max_num_pages = $max_num_pages;
	}

	/**
	 * Set the total number of items.
	 *
	 * @param integer $total The total number of items.
	 */
	public function setNumTotalResults( $total )
	{
		$total = (int)$total;
		if ( $total < 0 )
		{
			trigger_error( 'Pagination setNumTotalResults: Total must be positive.' );
			return false;
		}

		$this->items_total = $total;
	}

	/**
	 * Set the url used in the links.
	 *
	 * @param string $url_base The pagination url.
	 */
	public function setUrlBase( $url_base )
	{
		if ( empty( $url_base ) )
		{
			trigger_error( 'Pagination setUrl: Url must have a value.' );
			return false;
		}

		$this->url_base = $url_base;
	}

	/**
	 * Set a parameter assigned to the pagination template.
	 *
	 * @param string $param Parameter name assigned to template.
	 * @param mixed $value Value of the parameter.
	 */
	public function setTemplateParam( $param, $value )
	{
		if ( !isset( $param ) || empty( $param ) || !isset( $value ) )
		{
			trigger_error( "The parameter \$param or \$value can't be empty." );
			return false;
		}
		$this->params_template[$param] = $value;
	}

	/**
	 * Set the default separator for the page link.
	 *
	 * @param string $separator Separator string.
	 */
	public function setSeparatorLink( $separator )
	{
		if ( empty( $separator ) )
		{
			trigger_error( 'Pagination setSeparatorLink: The separator must have a value.' );
			return false;
		}

		$this->separator_link_page = $separator;
	}

	/**
	 * Set the pagination template.
	 *
	 * @param string $layout_template Name pagination template (this is the .tpl file).
	 * @return boolean
	 */
	public function setTemplate( $layout_template )
	{
		$tpl_config = $this->config->getConfig( 'templates' );
	
		if ( !isset( $tpl_config[$layout_template] ) )
		{
			trigger_error( "Template file '$layout_template' not found." );
			return false;
		}

		$this->layout_template =  ROOT_PATH . '/'. $tpl_config[$layout_template];
	}

	/**
	 * Set display items per page.
	 *
	 * @param array $values_to_show Values to show.
	 */
	public function setDisplayItemsPerPage( $values_to_show )
	{
		$this->display_items_per_page['display'] = false;
		if ( !empty( $values_to_show ) && is_array( $values_to_show ) )
		{
			$result = array_filter( $values_to_show, "is_integer" );
			$this->display_items_per_page['display']	= true;
			$this->display_items_per_page['values'] 	= $result;
		}
	}

	/**
	 * Set page range ( window ).
	 *
	 * @param array $type_page_range Name of the type of page range to set.
	 * @param integer $value Value of the page range.
	 */
	public function setWindowPage( $type_page_range, $value )
	{
		if ( !isset( $this->availables_page_range[$type_page_range] ) )
		{
			trigger_error( "Pagination setWindowPage: The type of page range ( $type_page_range ), not is valid. ( Availables: default, short and very_short )" );
			return false;
		}

		if ( !is_int( $value ) )
		{
			trigger_error( 'Pagination setWindowPage: The value to set page range must be an integer.' );
			return false;
		}

		$this->availables_page_range[$type_page_range] = $value;
	}
}
?>