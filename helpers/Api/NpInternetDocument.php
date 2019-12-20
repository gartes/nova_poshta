<?php
	/**
	 * @package     ${NAMESPACE}
	 * @subpackage
	 *
	 * @copyright   A copyright
	 * @license     A "Slug" license name e.g. GPL2
	 */
	
	namespace Plg\Np\Api;
	
	use NovaPoshta\ApiModels\InternetDocument;
	use NovaPoshta\Models\CounterpartyContact;
	use NovaPoshta\MethodParameters\InternetDocument_getDocumentList;
	
	/**
	 * API Экспресс-накладная
	 * Работа с экспресс-накладными
	 * @since       version
	 * @package     Plg\Np\Api
	 *
	 */
	class NpInternetDocument
	{
	 
		
		
		static $aptamsDef = [];
		
		
		public static function getDocumentPrice( $opt )
		{
			$data = new \NovaPoshta\MethodParameters\InternetDocument_getDocumentPrice();
			$data->setCitySender($opt['CitySender']);
			$data->setCityRecipient( $opt['cityRef']);
			$data->setWeight($opt['weight']);
			$data->setCost($opt['salesPrice']);
			$data->setServiceType( $opt['serviceType']? 'WarehouseWarehouse':'WarehouseDoors');
			$data->setDateTime($opt['DateTime']);
			return InternetDocument::getDocumentPrice($data);
		}
		
		
	
		
		
		
		
	}