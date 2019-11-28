<?php
	/**
	 * @package     Plg\Np
	 * @subpackage
	 *
	 * @copyright   A copyright
	 * @license     A "Slug" license name e.g. GPL2
	 */
	
	namespace Plg\Np;
	
	Use NovaPoshta\Config;
	use Exception;
	use JLoader;
	
	class Api
	{
		
		/**
		 * @var string API ключ
		 */
		protected static $apiKey = null;
		
		public static $ApiNpConfig = null;
		public static $ApiNpCounterparty = null;
		
		
		/**
		 * @param   null  $ApiKey
		 *
		 *
		 * @return string|null
		 * @throws Exception
		 * @since version
		 */
		public static function initApiNpConfig ( $ApiKey = null )
		{
			if( !self::$apiKey )
			{
				if( !$ApiKey )
				{
					throw new Exception( 'Failed to start application' , 500 );
				}
				
				JLoader::registerNamespace( 'NovaPoshta' , JPATH_LIBRARIES . '/GNZ11/Api/Shipment/NovaPoshta' , $reset = false , $prepend = false , $type = 'psr4' );
				
				Config::setApiKey( $ApiKey );
				Config::setFormat( Config::FORMAT_JSONRPC2 );
				Config::setLanguage( Config::LANGUAGE_UA );
				
				self::$apiKey = $ApiKey;
				
			}
			
			return self::$apiKey;
			
		}
		
		/**
		 * @param $ApiKey
		 *
		 *
		 * @return Api\NpCounterparty
		 * @throws Exception
		 * @since version
		 */
		public static function  getCounterparty ( $ApiKey )
		{
			if( !self::$apiKey ){
				if( !$ApiKey )
				{
					throw new Exception( 'Failed to start application Dont ApiKey' , 500 );
				}
				self::initApiNpConfig( $ApiKey );
			}
			
			if( !self::$ApiNpCounterparty ){
				 self::$ApiNpCounterparty = new \Plg\Np\Api\NpCounterparty() ;
			}
			return self::$ApiNpCounterparty ;
			
		}
		/**
		 * @param $ApiKey
		 *
		 *
		 * @return Api\NpAddress
		 * @throws Exception
		 * @since version
		 */
		public static function  getAddress ( $ApiKey )
		{
			if( !self::$apiKey ){
				if( !$ApiKey )
				{
					throw new Exception( 'Failed to start application Dont ApiKey' , 500 );
				}
				self::initApiNpConfig( $ApiKey );
			}
			
			if( !self::$ApiNpCounterparty ){
				self::$ApiNpCounterparty = new \Plg\Np\Api\NpAddress() ;
			}
			return self::$ApiNpCounterparty ;
			
		}
	
	
		
		
		
		/**
		 * Получим контактных персон для контрагентов
		 * @param $counterpartySenderRef
		 *
		 * @return mixed
		 *
		 * @throws Exception
		 * @since version
		 */
		public static function getContactPerson( $counterpartySenderRef ){
			if( !self::$apiKey ){
				throw new Exception( 'Failed to start application Dont ApiKey' , 500 );
			}
			return \Plg\Np\Api\NpContactPerson::getContactPerson( $counterpartySenderRef ) ;
			
		}
		
		
	}