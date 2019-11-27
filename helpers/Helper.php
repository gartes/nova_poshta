<?php
	/**
	 * @package     PlgNp
	 * @subpackage
	 *
	 * @copyright   A copyright
	 * @license     A "Slug" license name e.g. GPL2
	 */
	
	namespace Plg\Np;
	
	
	
	
	
	class Helper
	{
		private $app;
		public static $instance;
		
		/**
		 * helper constructor.
		 * @throws \Exception
		 * @since 3.9
		 */
		private function __construct ( $options = [] )
		{
			
			
			
			$this->app = \JFactory::getApplication();
			
			return $this;
		}#END FN
		
		/**
		 * @param   array  $options
		 *
		 * @return helper
		 * @throws \Exception
		 * @since 3.9
		 */
		public static function instance ( $options = [] )
		{
			if( self::$instance === null )
			{
				self::$instance = new self( $options );
			}
			
			return self::$instance;
		}#END FN
		
		public static function plgVmSetOnTablePluginShipment( &$data , &$table ){
			
			
			
			
			echo'<pre>';print_r( $counterparty );echo'</pre>'.__FILE__.' '.__LINE__;
			die(__FILE__ .' '. __LINE__ );
			
			
			
		}
		
	}