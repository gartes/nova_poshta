<?php
	/**
	 * @package     PlgNp
	 * @subpackage
	 *
	 * @copyright   A copyright
	 * @license     A "Slug" license name e.g. GPL2
	 */
	
	namespace Plg\Np;
	
	
	
	
	
	use Exception;
	use GNZ11\Core\Js;
	use JLoader;
	use JFactory;
	use JUri;
	
	class Helper
	{
		private $app;
		public static $instance;
		
		/**
		 * helper constructor.
		 * @throws Exception
		 * @since 3.9
		 */
		private function __construct ( $options = [] )
		{
			$this->app = JFactory::getApplication();
			
			return $this;
		}#END FN
		
		/**
		 * @param   array  $options
		 *
		 * @return helper
		 * @throws Exception
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
		
		
		public function loadWarehouses(){
			$ret =  \Plg\Np\Html::loadWarehouses();
			return $ret ;
		}
		
		/**
		 * Получение содержимого окна выбора метода доставки на фронте в корзине
		 * @return string - Html форма
		 *
		 * @since version
		 */
		public function getModalBody(){
			$ret =  \Plg\Np\Html::getModalBody();
			return $ret ;
		}
		
		/**
		 * # Получить контрагента отправителя
		 * @param $data
		 * @param $table
		 *
		 *
		 * @throws Exception
		 * @since version
		 */
		public static function plgVmSetOnTablePluginShipment( &$data , &$table ){
			$data = self::getPersonsBySender( $data );
		}
		
		/**
		 * Добавление HTML к выбору доставки
		 * @param $plugin
		 *
		 *
		 * @return null
		 * @throws Exception
		 * @since version
		 */
		public static function renderPluginName ( $plugin )
		{
			$html = null ;
			
			JLoader::registerNamespace( 'GNZ11' , JPATH_LIBRARIES . '/GNZ11' , $reset = false , $prepend = false , $type = 'psr4' );
			
			Js::instance();
			$doc = JFactory::getDocument();
			$doc->addScript( '/plugins/vmshipment/nova_pochta/assets/js/front-cart_shipment.js' );

			$doc->addScriptOptions( 'NovaPoshta-cart' , [ 'virtuemart_shipmentmethod_id' => $plugin->virtuemart_shipmentmethod_id , ] );
			$doc->addScriptOptions( 'siteUrl' , JUri::root() );
			
			$doc->addStyleSheet('/libraries/GNZ11/assets/css/form/radio-btn-group.css');
			$doc->addStyleSheet('/plugins/vmshipment/nova_pochta/assets/css/front-cart_shipment.css');
			return $html ;
		}
		
		/**
		 * # Получить контрагента отправителя
		 * @param $data
		 *
		 * @return mixed
		 *
		 * @throws Exception
		 * @since version
		 */
		private static function getPersonsBySender ( &$data )
		{
			$Counterparty = \Plg\Np\Api::getCounterparty( $data[ 'params' ][ 'apikey' ] );
			
			# Получить контрагента отправителя
			$DataCounterparty = $Counterparty::getCounterparties( [ 'Property' => 'Sender' , ] );
			$Ref              = $DataCounterparty->data[ 0 ]->Ref;
			
			$ContactPerson = \Plg\Np\Api::getContactPerson( $Ref );
			
			$np_params                       = [ 'Sender' => $DataCounterparty->data[ 0 ] , 'ContactPerson' => $ContactPerson->data[ 0 ] , ];
			$data[ 'np_params_sender' ] =   json_encode( $np_params )  ;
			
			return $data;
		}
		
	}