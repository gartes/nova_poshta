<?php
	/**
	 * @package     ${NAMESPACE}
	 * @subpackage
	 *
	 * @copyright   A copyright
	 * @license     A "Slug" license name e.g. GPL2
	 */
	
	namespace Plg\Np\Api;
	
	use NovaPoshta\ApiModels\ContactPerson;
	use NovaPoshta\MethodParameters\Counterparty_getCounterparties;
	use NovaPoshta\MethodParameters\MethodParameters;
	use NovaPoshta\ApiModels\Counterparty;
	
	class NpAddress
	{
	 
		
		
		static $aptamsDef = [ ];
		
		/**
		 * Списки складов
		 * @param $cityRef
		 *
		 * @return \NovaPoshta\Models\DataContainerResponse
		 *
		 * @since version
		 */
		public static function getWarehouses( $cityRef ){
			$data = new \NovaPoshta\MethodParameters\Address_getWarehouses();
			$data->setCityRef($cityRef);
			$result = \NovaPoshta\ApiModels\Address::getWarehouses($data);
			return $result ;
			// $addressSender = $result->data[5]->Ref;
			
		}
		
		/**
		 * список городов
		 * @return \NovaPoshta\Models\DataContainerResponse
		 *
		 * @since version
		 */
		public static function getCityList(){
			
			$result = \NovaPoshta\ApiModels\Address::getCities();
			return $result  ;
		}
		
		
	}