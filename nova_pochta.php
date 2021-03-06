<?php
	
	defined( '_JEXEC' ) or die( 'Restricted access' );
	
	use GNZ11\Core\Js;
	use Joomla\CMS\Factory;
	use Joomla\CMS\Date\Date;
	use Joomla\CMS\Language\Text;
	if (!class_exists( 'VmConfig' )) require(JPATH_ROOT .'/administrator/components/com_virtuemart/helpers/config.php');
	VmConfig::loadConfig();
	
	
	/**
	 * Shipment plugin for weight_countries shipments, like regular postal services
	 *
	 * @version    $Id: weight_countries.php 10149 2019-09-16 12:20:25Z Milbo $
	 * @package    VirtueMart
	 * @subpackage Plugins - shipment
	 * @copyright  Copyright (C) 2004-2012 VirtueMart Team - All rights reserved.
	 * @license    http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
	 * VirtueMart is free software. This version may have been modified pursuant
	 * to the GNU General Public License, and as distributed it includes or
	 * is derivative of works licensed under the GNU General Public License or
	 * other free or open source software licenses.
	 * See /administrator/components/com_virtuemart/COPYRIGHT.php for copyright notices and details.
	 *
	 * http://virtuemart.org
	 * @author     Valerie Isaksen
	 *
	 */
	class plgVmShipmentNova_pochta extends vmPSPlugin
	{
		
		/**
		 * Affects constructor behavior. If true, language files will be loaded automatically.
		 *
		 * @var    boolean
		 * @since  3.1
		 */
		protected $autoloadLanguage = true;
		
		private $Helper ; 
		
		/**
		 * @param   object  $subject
		 * @param   array   $config
		 *
		 * @throws Exception
		 * @since 3.9
		 */
		function __construct ( &$subject , $config )
		{
			
			
			
			parent::__construct( $subject , $config );
			$this->_loggable   = true;
			$this->_tablepkey  = 'id';
			$this->_tableId    = 'id';
			$this->tableFields = array_keys( $this->getTableSQLFields() );
			$varsToPush        = $this->getVarsToPush();
			$this->addVarsToPushCore( $varsToPush , 0 );
			$this->setConfigParameterable( $this->_configTableFieldName , $varsToPush );
			$this->setConvertable( [ 'min_amount' , 'max_amount' , 'shipment_cost' , 'package_fee' ] );
			
			JLoader::registerNamespace('Plg\Np',JPATH_PLUGINS.'/vmshipment/nova_pochta/helpers',$reset=false,$prepend=false,$type='psr4');
			$this->Helper = \Plg\Np\Helper::instance(  );
			//vmdebug('Muh constructed plgVmShipmentWeight_countries',$varsToPush);
		}
		
		/**
		 * Create the table for this plugin if it does not yet exist.
		 *
		 * @since 3.9
		 */
		public function getVmPluginCreateTableSQL ()
		{
			return $this->createTableSQL( 'Shipment Nova Pochta Table' );
		}
		
		/**
		 * список полей тбл. доставки
		 * @return array
		 * @since 3.9
		 */
		function getTableSQLFields ()
		{
			
			$SQLfields = [
				'id' => 'int(1) UNSIGNED NOT NULL AUTO_INCREMENT' ,
				'virtuemart_order_id' => 'int(11) UNSIGNED' ,
				'order_number' => 'char(32)' ,
				'ref_city' => 'char(36)' ,
				'novaposhta' => 'text ' ,
				'virtuemart_shipmentmethod_id' => 'mediumint(1) UNSIGNED' ,
				'shipment_name' => 'varchar(5000)' ,
				'order_weight' => 'decimal(10,4)' ,
				'shipment_weight_unit' => 'char(3) DEFAULT \'KG\'' ,
				'shipment_cost' => 'decimal(10,2)' ,
				'shipment_package_fee' => 'decimal(10,2)' ,
				'tax_id' => 'smallint(1)'
			];
			
			return $SQLfields;
		}
		
		/**
		 * This method is fired when showing the order details in the frontend.
		 * It displays the shipment-specific data.
		 *
		 * @param   integer  $virtuemart_order_id           The order ID
		 * @param   integer  $virtuemart_shipmentmethod_id  The selected shipment method id
		 * @param   string   $shipment_name                 Shipment Name
		 *
		 * @return mixed Null for shipments that aren't active, text (HTML) otherwise
		 * @since 3.9
		 */
		public function plgVmOnShowOrderFEShipment ( $virtuemart_order_id , $virtuemart_shipmentmethod_id , &$shipment_name )
		{
			
			$this->onShowOrderFE( $virtuemart_order_id , $virtuemart_shipmentmethod_id , $shipment_name );
			return ;
		}
		
		/**
		 * Создание подтвержденного заказа.
		 * This event is fired after the order has been stored; it gets the shipment method-
		 * specific data.
		 *
		 * @param   int     $order_id  The order_id being processed
		 * @param   object  $cart      the cart
		 * @param   array   $order     The actual order saved in the DB
		 *
		 * @return mixed Null when this method was not selected, otherwise true
		 * @throws Exception
		 * @since 3.9
		 */
		function plgVmConfirmedOrder ( VirtueMartCart $cart , $order )
		{
			
			if( !( $method = $this->getVmPluginMethod( $order[ 'details' ][ 'BT' ]->virtuemart_shipmentmethod_id ) ) )
			{
				return null; // Another method was selected, do nothing
			}
			
			
			if( !$this->selectedThisElement( $method->shipment_element ) )
			{
				return false;
			}
			
			
			$app = \JFactory::getApplication() ;
			// 8d5a980d-391c-11dd-90d9-001a92567626 - киев
			
		//	echo'<pre>';print_r( $app->input );echo'</pre>'.__FILE__.' '.__LINE__;
		// 	die(__FILE__ .' '. __LINE__ );
			$values[ 'virtuemart_order_id' ]          = $order[ 'details' ][ 'BT' ]->virtuemart_order_id;
			$values[ 'order_number' ]                 = $order[ 'details' ][ 'BT' ]->order_number;
			$values[ 'virtuemart_shipmentmethod_id' ] = $order[ 'details' ][ 'BT' ]->virtuemart_shipmentmethod_id;
			$values[ 'shipment_name' ]                = $this->renderPluginName( $method );
			$values[ 'order_weight' ]                 = $this->getOrderWeight( $cart , $method->weight_unit );
			$values[ 'shipment_weight_unit' ]         = $method->weight_unit;
			
			$novaposhta = $app->input->get('novaposhta' , [] , 'ARRAY' ) ;
			$values[ 'ref_city' ]         = $app->input->get('cityRef' , false ) ;
			$values[ 'novaposhta' ]       = json_encode($novaposhta) ;
			
			$costs = $this->getCosts( $cart , $method , $cart->cartPrices );
			if( !empty( $costs ) )
			{
				$values[ 'shipment_cost' ]        = $method->shipment_cost;
				$values[ 'shipment_package_fee' ] = $method->package_fee;
			}
			if( empty( $values[ 'shipment_cost' ] ) ) $values[ 'shipment_cost' ] = 0.0;
			if( empty( $values[ 'shipment_package_fee' ] ) ) $values[ 'shipment_package_fee' ] = 0.0;
			
			$values[ 'tax_id' ] = $method->tax_id;
			$this->storePSPluginInternalData( $values );
			
			return true;
		}
		
		/**
		 * Этот метод запускается при отображении деталей заказа в бэкэнде.
		 * Отображает данные, относящиеся к конкретной поставке.
		 * ПРИМЕЧАНИЕ: этот плагин НЕ должен использоваться для отображения полей формы, так как он вызывается снаружи
		 * форма! Используйте взамен plgVmOnUpdateOrderBE ()!
		 *
		 * @param   integer  $virtuemart_order_id           The order ID
		 * @param   integer  $virtuemart_shipmentmethod_id  The order shipment method ID
		 *
		 * @return mixed Null for shipments that aren't active, text (HTML) otherwise
		 * @throws Exception
		 * @since  3.9
		 * @author Valerie Isaksen
		 */
		public function plgVmOnShowOrderBEShipment ( $virtuemart_order_id , $virtuemart_shipmentmethod_id )
		{
			if( !( $this->selectedThisByMethodId( $virtuemart_shipmentmethod_id ) ) )
			{
				return null;
			}
			
			$Method = $this->getPluginMethod( $virtuemart_shipmentmethod_id ) ;
			
			
			
			$html = $this->getOrderShipmentHtml( $virtuemart_order_id );
			
			
			
			
			
			
			$this->Helper::setPluginSetting($Method) ;
			$this->Helper::setSetting($Method) ;
			
			$doc = JFactory::getDocument();
			$NpSettingPlg = $doc->getScriptOptions('NpSettingPlg');
			$NpSettingPlg['virtuemart_order_id'] = $virtuemart_order_id ;
			$doc->addScriptOptions('NpSettingPlg' , $NpSettingPlg );
			
			
			
			return $html;
		}
		
		/**
		 * Информация о доставке в бланке заказа Админ панель
		 * @param $virtuemart_order_id
		 *
		 * @return string
		 * @throws Exception
		 * @since 3.9
		 */
		function getOrderShipmentHtml ( $virtuemart_order_id )
		{
			
			
			
			
			
			$db = JFactory::getDBO();
			$q  = 'SELECT * FROM `' . $this->_tablename . '` ' . 'WHERE `virtuemart_order_id` = ' . $virtuemart_order_id;
			$db->setQuery( $q );
			if( !( $shipinfo = $db->loadObject() ) )
			{
				$msg = vmText::sprintf( 'VMSHIPMENT_WEIGHT_COUNTRIES_NO_ENTRY_FOUND' , $virtuemart_order_id );
				vmWarn( $msg );
				vmDebug( $msg , $q . " " . $db->getErrorMsg() );
				
				return '';
			}
			
			$currency   = CurrencyDisplay::getInstance();
			$tax        = ShopFunctions::getTaxByID( $shipinfo->tax_id );
			$taxDisplay = is_array( $tax ) ? $tax[ 'calc_value' ] . ' ' . $tax[ 'calc_value_mathop' ] : $shipinfo->tax_id;
			$taxDisplay = ( $taxDisplay == -1 ) ? vmText::_( 'COM_VIRTUEMART_PRODUCT_TAX_NONE' ) : $taxDisplay;
			
			$modelOrder = VmModel::getModel ('orders');
			$vmorder = $modelOrder->getOrder ($virtuemart_order_id);
			$vmorderST = $vmorder['details']['ST'] ;
			
			$html = '<table class="adminlist table">' . "\n";
			$html .= $this->getHtmlHeaderBE();
			
			$shipinfo->novaposhta = json_decode( $shipinfo->novaposhta ) ;
			$app = \JFactory::getApplication() ;
			$app->input->set('novaposhta' , $shipinfo->novaposhta );
			
			
			$this->Helper::setCityRef( $shipinfo->ref_city );
			$this->Helper::setWarehousesRef( $shipinfo->novaposhta->warehouses );
			$this->Helper::setCitySender( $shipinfo->novaposhta->CitySender );
			$this->Helper::setSenderAddress( $shipinfo->novaposhta->SenderAddress );
			
			$RecipientText = $vmorderST->last_name.(!empty($vmorderST->last_name)?' ':'' ).$vmorderST->first_name ;
			$phone = (!empty($vmorderST->phone_1)?$vmorderST->phone_1:$vmorderST->phone_2) ;
			
			
//			echo'<pre>';print_r( $vmorderST->phone_1 );echo'</pre>'.__FILE__.' '.__LINE__;
//			die(__FILE__ .' '. __LINE__ );
			
			$shipinfo->novaposhta->RecipientText = trim( $RecipientText ) ;
			$shipinfo->novaposhta->RecipientsPhone = trim( $phone ) ;
			//$this->Helper::setRecipientText( trim( $RecipientText ) );
			
			
			
//			echo'<pre>';print_r( $shipinfo );echo'</pre>'.__FILE__.' '.__LINE__;
			
			$html .= $this->Helper::OrderShipmentHtmlBE($shipinfo) ;
			
			
			
			
//			$html .= $this->getHtmlRowBE( 'WEIGHT_COUNTRIES_SHIPPING_NAME' , $shipinfo->shipment_name );
//			$html .= $this->getHtmlRowBE( 'WEIGHT_COUNTRIES_WEIGHT' , $shipinfo->order_weight . ' ' . ShopFunctions::renderWeightUnit( $shipinfo->shipment_weight_unit ) );
//			$html .= $this->getHtmlRowBE( 'WEIGHT_COUNTRIES_COST' , $currency->priceDisplay( $shipinfo->shipment_cost ) );
//			$html .= $this->getHtmlRowBE( 'WEIGHT_COUNTRIES_PACKAGE_FEE' , $currency->priceDisplay( $shipinfo->shipment_package_fee ) );
//			$html .= $this->getHtmlRowBE( 'WEIGHT_COUNTRIES_TAX' , $taxDisplay );
			$html .= '</table>' . "\n";
			
			return $html;
		}
		
		/**
		 * @param   VirtueMartCart  $cart
		 * @param                   $method
		 * @param                   $cart_prices
		 *
		 * @return array
		 * @throws Exception
		 * @since 3.9
		 */
		function getCosts ( VirtueMartCart $cart , $method , $cart_prices )
		{
			$app                          = \JFactory::getApplication();
			$opt = $app->input->get( 'opt' , [] , 'ARRAY' ) ;
			
			$weight = null ;
			
			if( !isset( $opt['cityRef'] ) ) return 0.0; #END IF
			
			foreach( $cart->products as $product )
			{
				$weight += $product->product_weight ;
				
			}#END FOREACH
			
			if( $weight < 0.1 ) $weight = 0.1 ;  #END IF
			
			$date = new Date('now +1 day');
			$DateTime = $date->format('d.m.Y', false, false) ;
			
			$opt['CitySender'] = $method->city_sender_ref ;
			$opt['salesPrice'] = $cart_prices['salesPrice'] ;
			$opt['weight'] = $weight ;
			$opt['DateTime'] = $DateTime  ;
			
			
			# Получить стоимость доставки
			$InternetDocument = \Plg\Np\Api::getInternetDocument( $method->apikey );
			$DocumentPrice = $InternetDocument::getDocumentPrice( $opt );
			
			
			
			return ['nova_pochta'=>$DocumentPrice] ;
			
			
			echo'<pre>';print_r( $DocumentPrice );echo'</pre>'.__FILE__.' '.__LINE__;
//			die(__FILE__ .' '. __LINE__ );
			/*echo'<pre>';print_r(  $DocumentPrice );echo'</pre>'.__FILE__.' '.__LINE__;
			echo'<pre>';print_r(  $method->apikey );echo'</pre>'.__FILE__.' '.__LINE__;
			die(__FILE__ .' '. __LINE__ );*/
			
			if( $method->free_shipment && $cart_prices[ 'salesPrice' ] >= $method->free_shipment )
			{
				return 0.0;
			}
			else
			{
				if( empty( $method->shipment_cost ) ) $method->shipment_cost = 0.0;
				if( empty( $method->package_fee ) ) $method->package_fee = 0.0;
				
				return $method->shipment_cost + $method->package_fee;
			}
		}
		
		/**
		 * @param   VirtueMartCart  $cart
		 * @param   int             $method
		 * @param   array           $cart_prices
		 *
		 * @return bool Если false способ доставки не отображается
		 * @since 3.9
		 */
		protected function checkConditions ( $cart , $method , $cart_prices )
		{
			
			
			
			return true;
			
			
			static $result = [];
			
			if( $cart->STsameAsBT == 0 )
			{
				$type = 'ST';
			}
			else
			{
				$type = 'BT';
			}
			
			$address = $cart->getST();
			
			if( !is_array( $address ) ) $address = [];
			if( isset( $cart_prices[ 'salesPrice' ] ) )
			{
				$hashSalesPrice = $cart_prices[ 'salesPrice' ];
			}
			else
			{
				$hashSalesPrice = '';
			}
			
			
			if( empty( $address[ 'virtuemart_country_id' ] ) ) $address[ 'virtuemart_country_id' ] = 0;
			if( empty( $address[ 'zip' ] ) ) $address[ 'zip' ] = 0;
			
			$hash = $method->virtuemart_shipmentmethod_id . $type . $address[ 'virtuemart_country_id' ] . '_' . $address[ 'zip' ] . '_' . $hashSalesPrice;
			
			if( isset( $result[ $hash ] ) )
			{
				return $result[ $hash ];
			}
			
			$this->convert( $method );
			
			$result[ $hash ] = parent::checkConditions( $cart , $method , $cart_prices );
			
			if( !$result[ $hash ] ) return false;
			
			$orderWeight = $this->getOrderWeight( $cart , $method->weight_unit );
			
			$weight_cond     = $this->testRange( $orderWeight , $method , 'weight_start' , 'weight_stop' , 'weight' );
			$nbproducts_cond = $this->_nbproductsCond( $cart , $method );
			
			$userFieldsModel = VmModel::getModel( 'Userfields' );
			if( $userFieldsModel->fieldPublished( 'zip' , $type ) )
			{
				if( !isset( $address[ 'zip' ] ) )
				{
					$address[ 'zip' ] = '';
				}
				$zip_cond = $this->testRange( $address[ 'zip' ] , $method , 'zip_start' , 'zip_stop' , 'zip' );
			}
			else
			{
				$zip_cond = true;
			}
			
			$allconditions = (int) $weight_cond + (int) $zip_cond + (int) $nbproducts_cond;
			if( $allconditions === 3 )
			{
				$result[ $hash ] = true;
				
				return true;
			}
			else
			{
				$result[ $hash ] = false;
				
				//vmdebug('checkConditions '.$method->shipment_name.' does not fit ',(int)$weight_cond,(int)$zip_cond,(int)$nbproducts_cond,(int)$orderamount_cond,(int)$country_cond);
				return false;
			}
			
			$result[ $hash ] = false;
			
			return false;
		}
		
		/**
		 * @param $method
		 */
		function convert ( &$method )
		{
			
			//$method->weight_start = (float) $method->weight_start;
			//$method->weight_stop = (float) $method->weight_stop;
			/*$method->min_amount =  (float)str_replace(',','.',$method->min_amount);
			$method->max_amount =   (float)str_replace(',','.',$method->max_amount);
			$method->zip_start = (int)$method->zip_start;
			$method->zip_stop = (int)$method->zip_stop;
			$method->nbproducts_start = (int)$method->nbproducts_start;
			$method->nbproducts_stop = (int)$method->nbproducts_stop;
			$method->free_shipment = (float)str_replace(',','.',$method->free_shipment);*/
		}
		
		/**
		 * @param $cart
		 * @param $method
		 *
		 * @return bool
		 * @since 3.9
		 */
		private function _nbproductsCond ( $cart , $method )
		{
			
			/*if (empty($method->nbproducts_start) and empty($method->nbproducts_stop)) {
				//vmdebug('_nbproductsCond',$method);
				return true;
			}
	
			$nbproducts = 0;
			foreach ($cart->products as $product) {
				$nbproducts += $product->quantity;
			}
	
			if ($nbproducts) {
	
				$nbproducts_cond = $this->testRange($nbproducts,$method,'nbproducts_start','nbproducts_stop','products quantity');
	
			} else {
				$nbproducts_cond = false;
			}
	
			return $nbproducts_cond;*/
		}
		
		private function testRange ( $value , $method , $floor , $ceiling , $name )
		{
			
			/*$cond = true;
			if(!empty($method->$floor) and !empty($method->$ceiling)){
				$cond = (($value >= $method->$floor AND $value <= $method->$ceiling));
				if(!$cond){
					$result = 'FALSE';
					$reason = 'is NOT within Range of the condition from '.$method->$floor.' to '.$method->$ceiling;
				} else {
					$result = 'TRUE';
					$reason = 'is within Range of the condition from '.$method->$floor.' to '.$method->$ceiling;
				}
			} else if(!empty($method->$floor)){
				$cond = ($value >= $method->$floor);
				if(!$cond){
					$result = 'FALSE';
					$reason = 'is not at least '.$method->$floor;
				} else {
					$result = 'TRUE';
					$reason = 'is over min limit '.$method->$floor;
				}
			} else if(!empty($method->$ceiling)){
				$cond = ($value <= $method->$ceiling);
				if(!$cond){
					$result = 'FALSE';
					$reason = 'is over '.$method->$ceiling;
				} else {
					$result = 'TRUE';
					$reason = 'is lower than the set '.$method->$ceiling;
				}
			} else {
				$result = 'TRUE';
				$reason = 'no boundary conditions set';
			}
	
			if(!$result) vmdebug('shipmentmethod '.$method->shipment_name.' = '.$result.' for variable '.$name.' = '.$value.' Reason: '.$reason);
			return $cond;*/
		}
		
		/**
		 * Карточка товара
		 *
		 * @param $product
		 * @param $productDisplayShipments
		 *
		 * @return bool
		 *
		 * @since version
		 */
		function plgVmOnProductDisplayShipment ( $product , &$productDisplayShipments )
		{
			
			
			if( $this->getPluginMethods( $product->virtuemart_vendor_id ) === 0 )
			{
				
				return false;
			}
			
			$html = [];
			
			$currency = CurrencyDisplay::getInstance();
			
			foreach( $this->methods as $this->_currentMethod )
			{
				
				if( $this->_currentMethod->show_on_pdetails )
				{
					
					if( !isset( $cart ) )
					{
						$cart                        = VirtueMartCart::getCart();
						$cart->products[ 'virtual' ] = $product;
						$cart->_productAdded         = true;
						$cart->prepareCartData();
					}
					if( $this->checkConditions( $cart , $this->_currentMethod , $cart->cartPrices ) )
					{
						
						$product->prices[ 'shipmentPrice' ] = $this->getCosts( $cart , $this->_currentMethod , $cart->cartPrices );
						
						if( isset( $product->prices[ 'VatTax' ] ) and count( $product->prices[ 'VatTax' ] ) > 0 )
						{
							reset( $product->prices[ 'VatTax' ] );
							$rule = current( $product->prices[ 'VatTax' ] );
							if( isset( $rule[ 1 ] ) )
							{
								$product->prices[ 'shipmentTax' ]   = $product->prices[ 'shipmentPrice' ] * $rule[ 1 ] / 100.0;
								$product->prices[ 'shipmentPrice' ] = $product->prices[ 'shipmentPrice' ] * ( 1 + $rule[ 1 ] / 100.0 );
							}
						}
						
						$html[ $this->_currentMethod->virtuemart_shipmentmethod_id ] = $this->renderByLayout( 'default' , [ "method" => $this->_currentMethod , "cart" => $cart , "product" => $product , "currency" => $currency ] );
					}
				}
			}
			if( isset( $cart ) )
			{
				unset( $cart->products[ 'virtual' ] );
				$cart->_productAdded = true;
				$cart->prepareCartData();
			}
			
			
			$productDisplayShipments[] = $html;
			
		}
		
		/**
		 * Create the table for this plugin if it does not yet exist.
		 * This functions checks if the called plugin is active one.
		 * When yes it is calling the standard method to create the tables
		 *
		 * @param $jplugin_id
		 *
		 * @return bool|mixed
		 *
		 * @since version
		 */
		function plgVmOnStoreInstallShipmentPluginTable ( $jplugin_id )
		{
			
			return $this->onStoreInstallPluginTable( $jplugin_id );
		}
		
		/**
		 * @param   VirtueMartCart  $cart
		 *
		 * @return null
		 * @since 3.9
		 */
		public function plgVmOnSelectCheckShipment ( VirtueMartCart &$cart )
		{
			
			return $this->OnSelectCheck( $cart );
		}
		
		/**
		 * plgVmDisplayListFE
		 * This event is fired to display the pluginmethods in the cart (edit shipment/payment) for example
		 *
		 * @param   object   $cart      Cart object
		 * @param   integer  $selected  ID of the method selected
		 *
		 * @return boolean True on success, false on failures, null when this plugin was not selected.
		 * On errors, JError::raiseWarning (or JError::raiseError) must be used to set a message.
		 * @since 3.9
		 *
		 */
		public function plgVmDisplayListFEShipment ( VirtueMartCart $cart , $selected = 0 , &$htmlIn )
		{
			
			$htmlIn[] = '<h1>xxxxxxxxx</h1>' ;
			
			
			return $this->displayListFE( $cart , $selected , $htmlIn );
		}
		
		/**
		 * Вывод названия
		 *
		 * @param $plugin
		 *
		 * @return mixed
		 *
		 * @throws Exception
		 * @since version
		 */
		protected function renderPluginName ($plugin) {
			
			$c = array();
			$idN = 'virtuemart_'.$this->_psType.'method_id';
			if(isset($c[$this->_psType][$plugin->$idN])){
				return $c[$this->_psType][$plugin->$idN];
			}
			
			# Загрузить в Helper Настройки Способа Доставки
			$this->Helper->setPluginSetting($plugin);
			# Установить JS OPT
			$this->Helper->setSetting();
			
			$addHtml = $this->Helper::renderPluginName( $plugin );
			
			
			$plugin_name = $this->_psType . '_name';
			
			$return = '';
			
			$description = 'XXXXXX';
			$c[$this->_psType][$plugin->$idN] = $return . '<span class="' . $this->_type . '_name">' . $plugin->$plugin_name . '</span>' . $addHtml;
			return $c[$this->_psType][$plugin->$idN];
		}
		
		/**
		 * @param   VirtueMartCart  $cart
		 * @param   array           $cart_prices
		 * @param                   $cart_prices_name
		 *
		 * @return bool|null
		 */
		public function plgVmOnSelectedCalculatePriceShipment ( VirtueMartCart $cart , array &$cart_prices , &$cart_prices_name )
		{
			
			return $this->onSelectedCalculatePrice( $cart , $cart_prices , $cart_prices_name );
		}
		
		/**
		 * plgVmOnCheckAutomaticSelected
		 * Checks how many plugins are available. If only one, the user will not have the choice. Enter edit_xxx page
		 * The plugin must check first if it is the correct type
		 *
		 * @param   VirtueMartCart cart: the cart object
		 *
		 * @return null if no plugin was found, 0 if more then one plugin was found,  virtuemart_xxx_id if only one
		 *              plugin is found
		 *
		 * @author Valerie Isaksen
		 */
		function plgVmOnCheckAutomaticSelectedShipment ( VirtueMartCart $cart , array $cart_prices , &$shipCounter )
		{
			
			return $this->onCheckAutomaticSelected( $cart , $cart_prices , $shipCounter );
		}
		
		/**
		 * Вывод в корзине при оформлении заказа
		 *
		 * @param   VirtueMartCart  $cart
		 *
		 * @return bool|null
		 *
		 * @since version
		 */
		function plgVmOnCheckoutCheckDataShipment ( VirtueMartCart $cart )
		{
			
			if( empty( $cart->virtuemart_shipmentmethod_id ) ) return false;
			
			$virtuemart_vendor_id = 1; //At the moment one, could make sense to use the cart vendor id
			if( $this->getPluginMethods( $virtuemart_vendor_id ) === 0 )
			{
				return null;
			}
			
			foreach( $this->methods as $this->_currentMethod )
			{
				if( $cart->virtuemart_shipmentmethod_id == $this->_currentMethod->virtuemart_shipmentmethod_id )
				{
					if( !$this->checkConditions( $cart , $this->_currentMethod , $cart->cartPrices ) )
					{
						return false;
					}
					break;
				}
			}
		}
		
		/**
		 * This method is fired when showing when priting an Order
		 * It displays the the payment method-specific data.
		 *
		 * @param   integer  $_virtuemart_order_id  The order ID
		 * @param   integer  $method_id             method used for this order
		 *
		 * @return mixed Null when for payment methods that were not selected, text (HTML) otherwise
		 * @author Valerie Isaksen
		 */
		function plgVmonShowOrderPrint ( $order_number , $method_id )
		{
			return $this->onShowOrderPrint( $order_number , $method_id );
		}
		
		function plgVmDeclarePluginParamsShipment ( $name , $id , &$dataOld )
		{
			return $this->declarePluginParams( 'shipment' , $name , $id , $dataOld );
		}
		
		/**
		 * Настройка способа доаставки
		 * @param $data
		 *
		 * @return bool|null
		 *
		 * @throws Exception
		 * @since 3.9
		 */
		function plgVmDeclarePluginParamsShipmentVM3 ( &$data )
		{
			$doc = JFactory::getDocument();
			$doc->addScript('/plugins/vmshipment/nova_pochta/assets/js/admin-method_edit.js') ;
			$doc->addScript('/plugins/vmshipment/nova_pochta/assets/js/form-np_element_city.js') ;
			$doc->addStyleSheet('/plugins/vmshipment/nova_pochta/assets/css/admin-method_edit.css');
			
			JLoader::registerNamespace( 'GNZ11' , JPATH_LIBRARIES . '/GNZ11' , $reset = false , $prepend = false , $type = 'psr4' );
			Js::instance();
			
			
//			echo'<pre>';print_r( $this );echo'</pre>'.__FILE__.' '.__LINE__;
//			echo'<pre>';print_r( $data );echo'</pre>'.__FILE__.' '.__LINE__;
			
//			die(__FILE__ .' '. __LINE__ );
			
			return $this->declarePluginParams( 'shipment' , $data );
		}
		
		
		/**
		 * События во время сохранения параметров способов доставки
		 *
		 * @param $data   array  Form Data
		 * @param $table  object TableShipmentmethods
		 *
		 * @return bool
		 * @throws Exception
		 * @since 3.9
		 */
		function plgVmSetOnTablePluginShipment ( &$data , &$table )
		{
			
			$name = $data[ 'shipment_element' ];
			$id   = $data[ 'shipment_jplugin_id' ];
			
			if( !empty( $this->_psType ) and !$this->selectedThis( $this->_psType , $name , $id ) )
			{
				return false;
			}
			
			
			# Если ключ передается
			if( !empty( $data['apikey'] ) )
			{
				# Получить контрагента отправителя
				$this->Helper::plgVmSetOnTablePluginShipment( $data , $table);
			}else{
				$data['np_params_sender'] = null ;
			}#END IF
			
			
			
			
			
			
			
			
			
			
			$tCon = [ 'weight_start' , 'weight_stop' , 'min_amount' , 'max_amount' , 'shipment_cost' , 'package_fee' ];
			foreach( $tCon as $f )
			{
				if( !empty( $data[ $f ] ) )
				{
					$data[ $f ] = str_replace( [ ',' , ' ' ] , [ '.' , '' ] , $data[ $f ] );
				}
			}
			
			
			
			$data[ 'nbproducts_start' ] = (int) $data[ 'nbproducts_start' ];
			$data[ 'nbproducts_stop' ]  = (int) $data[ 'nbproducts_stop' ];
			
			$data['params'][ 'nbproducts_start' ] = $data['params']['np_params'] ;
			
			
			
//			echo'<pre>';print_r(  $data );echo'</pre>'.__FILE__.' '.__LINE__;
//			die(__FILE__ .' '. __LINE__ );
		
		
		
		
			//Reasonable tests:
			if( !empty( $data[ 'zip_start' ] ) and !empty( $data[ 'zip_stop' ] ) and (int) $data[ 'zip_start' ] >= (int) $data[ 'zip_stop' ] )
			{
				vmWarn( 'VMSHIPMENT_WEIGHT_COUNTRIES_ZIP_CONDITION_WRONG' );
			}
			if( !empty( $data[ 'weight_start' ] ) and !empty( $data[ 'weight_stop' ] ) and (float) $data[ 'weight_start' ] >= (float) $data[ 'weight_stop' ] )
			{
				vmWarn( 'VMSHIPMENT_WEIGHT_COUNTRIES_WEIGHT_CONDITION_WRONG' );
			}
			
			if( !empty( $data[ 'min_order' ] ) and !empty( $data[ 'max_order' ] ) and (float) $data[ 'min_order' ] >= (float) $data[ 'max_order' ] )
			{
				vmWarn( 'VMSHIPMENT_WEIGHT_COUNTRIES_AMOUNT_CONDITION_WRONG' );
			}
			
			if( !empty( $data[ 'nbproducts_start' ] ) and !empty( $data[ 'nbproducts_stop' ] ) and (float) $data[ 'nbproducts_start' ] >= (float) $data[ 'nbproducts_stop' ] )
			{
				vmWarn( 'VMSHIPMENT_WEIGHT_COUNTRIES_NBPRODUCTS_CONDITION_WRONG' );
			}
			
			//$data['orderamount_start'] = $data['min_amount'];
			//$data['orderamount_stop'] = $data['max_amount'];
			
			//$data['show_on_pdetails'] = (int) $data['show_on_pdetails'];
			return $this->setOnTablePluginParams( $name , $id , $table );
			
		}
		
		/**
		 * Точка входа AJAX
		 *
		 * @throws Exception
		 * @since version
		 */
		public function onAjaxNova_pochta()
		{
			
			
			if( !JSession::checkToken( 'get' ) ) exit( 'ERR: check Token!!!' );
			$app = JFactory::getApplication();
			$opt = $app->input->get( 'opt' , [] , 'ARRAY' );
			$virtuemart_shipmentmethod_id = $app->input->get( 'virtuemart_shipmentmethod_id' , null , 'INT' );
			
			if( $virtuemart_shipmentmethod_id )
			{
				$Method = $this->getPluginMethod( $virtuemart_shipmentmethod_id ) ;
				$this->Helper::setPluginSetting($Method);
			}#END IF
			
			
			
			
			if( !isset( $opt[ 'task' ] ) ) return; #END IF
			if( !method_exists( $this->Helper , $opt[ 'task' ] ) )
			{
				echo new JResponseJson( null , JText::_( 'NOVA_POCHTA_MY_TASK_ERROR' ) , true );
				$app->close();
			} #END IF
			try
			{
				$res = $this->Helper->{$opt[ 'task' ]}();
				echo new JResponseJson( $res );
				$app->close();
			}
			catch( Exception $e )
			{
				// Executed only in PHP 5, will not be reached in PHP 7
				echo new JResponseJson( null , $e->getMessage() , true );
				$app->close();
				
				
			}
			catch( Throwable $e )
			{
				// Executed only in PHP 7, will not match in PHP 5
				echo new JResponseJson( null , $e->getMessage() , true );
				$app->close();
			}
		}
		
		
		
	}
	
	// No closing tag
/**
 *
   @media (max-width: 768px)
   .yjnewspopItem.col-sm-4 {
      width: 33.333333333% !important;
   }
  }
 *
 *
 */
