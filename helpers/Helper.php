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
	use JText;
	
	class Helper
	{
		private $app;
		public static $instance;
		public static $options ;
		
		public static $CityRef ;
		public static $WarehousesRef ;
		/**
		 * Идентификатор города отправителя
		 * @since 3.9
		 * @var Helper
		 */
		public static $CitySender ;
		/**
		 * Идентификатор адреса отправителя
		 * @since 3.9
		 * @var Helper
		 */
		public static $SenderAddress ;
		
		/**
		 * helper constructor.
		 * @throws Exception
		 * @since 3.9
		 */
		private function __construct ( $options = [] )
		{
			$this->app = JFactory::getApplication();
			self::$options = $options ;
			self::setSetting();
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
		}
		
		/**
		 * Идентификатор города отправителя
		 * @param   mixed  $CitySender
		 * @since 3.9
		 */
		public static function setCitySender ( $CitySender )
		{
			self::$CitySender = $CitySender;
		}
		
		/**
		 * Идентификатор адреса отправителя
		 * @param   Helper  $SenderAddress
		 * @since 3.9
		 */
		public static function setSenderAddress (  $SenderAddress )
		{
			self::$SenderAddress = $SenderAddress;
		}
		
		/**
		 * @param   mixed  $CityRef
		 * @since 3.9
		 */
		public static function setCityRef ( $CityRef )
		{
			self::$CityRef = $CityRef;
		}
		
		/**
		 * @param   mixed  $WarehousesRef
		 * @since 3.9
		 */
		public static function setWarehousesRef ( $WarehousesRef )
		{
			self::$WarehousesRef = $WarehousesRef;
		}#END FN
		
		
		
		
		
		
		
		/**
		 * Html Разметка в бланке заказа Админ.
		 * @param $shipinfo
		 *
		 * @return string
		 *
		 * @throws Exception
		 * @since version
		 */
		public static function OrderShipmentHtmlBE($shipinfo){
			$doc = JFactory::getDocument();
			$doc->addScript( '/plugins/vmshipment/nova_pochta/assets/js/front-cart_shipment.js' );
			$doc->addScript('/plugins/vmshipment/nova_pochta/assets/js/admin-order_edit.js');
			$doc->addStyleSheet('/plugins/vmshipment/nova_pochta/assets/css/front-cart_shipment.css');
			$doc->addStyleSheet('/plugins/vmshipment/nova_pochta/assets/css/admin-order_edit.css');
			
			JLoader::registerNamespace( 'GNZ11' , JPATH_LIBRARIES . '/GNZ11' , $reset = false , $prepend = false , $type = 'psr4' );
			Js::instance();
			$doc->addScriptOptions('siteUrl' , JUri::root() );
			$doc->addScriptOptions('csrf.token' , \JSession::getFormToken() );
			
			
			
			$ret =  \Plg\Np\Html::OrderShipmentHtmlBE($shipinfo);
			return $ret ;
		}
		
		public static function setPluginSetting($plugin){
			self::$options = $plugin ;
		}
		
		public static function setSetting(){
			$opt = self::$options ;
			$app = JFactory::getApplication() ;
			
			
			$doc = JFactory::getDocument();
			$doc->addScriptOptions('NpSettingPlg' , [
				'city_celect_style'=> $opt->city_celect_style ,
				'form_style'=> $opt->form_style ,
				'virtuemart_shipmentmethod_id'=> $opt->virtuemart_shipmentmethod_id ,
				'administrator'=> $app->isClient('administrator') ,
			]);
			
			
			
			
		}
		
		
		public function adminSaveOrder(){
			
			$d_form = $this->app->input->get('form' , false , 'RAW' );
			
			$params = array();
			parse_str($d_form, $params);
			
			$virtuemart_order_id = $this->app->input->get('virtuemart_order_id' , null , 'INT');
			if( !$virtuemart_order_id )
			{
				echo new \JResponseJson( null , JText::_( 'NOVA_POCHTA_SAVE_ERROR_ORDER_ID' ) , true );
				$this->app->close();
			}#END IF
			
			$db = JFactory::getDbo();
			$query = $db->getQuery(true) ;
			
			// Поля для обновления
			$fields = array(
				$db->quoteName('ref_city') . ' = ' . $db->quote($params['cityRef']) ,
				$db->quoteName('novaposhta') . ' = ' . $db->quote( json_encode( $params['novaposhta'] ) ) ,
			);
			// Условия обновления
			$conditions = array(
				$db->quoteName('virtuemart_order_id') . ' = '  . $db->quote($virtuemart_order_id)
			
			);
			$query->update($db->quoteName('#__virtuemart_shipment_plg_nova_pochta'))->set($fields)->where($conditions);
			//  echo $query->dump();
			// Устанавливаем и выполняем запрос
			$db->setQuery($query);
			try
			{
				$db->execute();
			}
			catch( \Exception $e )
			{
				// Executed only in PHP 5, will not be reached in PHP 7
				echo 'Выброшено исключение: ' , $e->getMessage() , "\n";
			}
			catch( \Throwable $e )
			{
				// Executed only in PHP 7, will not match in PHP 5
				echo 'Выброшено исключение: ' , $e->getMessage() , "\n";
				echo '<pre>';
				print_r( $e );
				echo '</pre>' . __FILE__ . ' ' . __LINE__;
			}
			
			$this->app->enqueueMessage( JText::sprintf('NOVA_POCHTA_SAVE_RESULT' , $db->getAffectedRows()  ));
			return true ;
		}
		
		/**
		 *
		 *
		 * @throws Exception
		 * @since version
		 */
		public function getCost(){
			$app                          = \JFactory::getApplication();
			
			$virtuemart_shipmentmethod_id = $app->input->get( 'virtuemart_shipmentmethod_id' , null , 'INT' );
			if(  !$virtuemart_shipmentmethod_id )
			{
				throw new Exception('Err: Dont virtuemart shipmentmethod id In JFormFieldCitylist.' , 500 ) ;
			}#END IF
			
			if (!class_exists( 'VmConfig' )) require(JPATH_ROOT .'/administrator/components/com_virtuemart/helpers/config.php');
			\VmConfig::loadConfig();
			$shipmentModel = \VmModel::getModel( 'Shipmentmethod' );
			$shipmentModel->setId( $virtuemart_shipmentmethod_id );
			$shipmentMethod = $shipmentModel->getShipment();
			
			$cart                        = \VirtueMartCart::getCart();
			$cart->prepareCartData();
			\Joomla\CMS\Plugin\PluginHelper::importPlugin('vmshipment' , 'nova_pochta');
			$dispatcher = \JDispatcher::getInstance();
			$returnValues = $dispatcher->trigger('getCosts', [$cart , $shipmentMethod , $cart->cartPrices  ]);
			
			foreach( $returnValues as $returnValue )
			{
				if( !isset( $returnValue['nova_pochta'] ) ) continue ; #END IF
				$Value = $returnValue['nova_pochta'] ;
			}#END FOREACH
			
			
			$viewData['Cost'] = $Value->data[0]->Cost;
			# Подключение слоя
			$layout    = new \JLayoutFile( 'documentPrice' ,JPATH_PLUGINS.'/vmshipment/nova_pochta/tmpl' );
			return $layout->render($viewData);
		}
		
		/**
		 * Подгрузка складов в городе
		 * @return string
		 *
		 * @throws Exception
		 * @since 3.9
		 * @since version
		 */
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
			$doc->addScript( '/plugins/vmshipment/nova_pochta/assets/js/form-element-city_select.js' );

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