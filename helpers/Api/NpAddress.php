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
		}
		
		/**
		 * список городов
		 * @return \NovaPoshta\Models\DataContainerResponse
		 *
		 * @since version
		 */
		public static function getCityList( $param ){
			$data = new \NovaPoshta\MethodParameters\Address_getCities();
			if( isset($param['Ref']) )
			{
				$data->setRef($param['Ref']);
			}#END IF
			$result = \NovaPoshta\ApiModels\Address::getCities($data);
			return $result  ;
		}
		
		/**
		 * Вызвать метод getSettlements() - загрузить справочник населенных пунктов Украины
		 *
		 *
		 */
		public static function getSettlements($param){
			$data = new \NovaPoshta\MethodParameters\Address_getCities();
			if( isset($param['Ref']) )
			{
				$data->setRef($param['Ref']);
			}#END IF
			
			
			$result = \NovaPoshta\ApiModels\Address::getSettlements($data);
			return $result  ;
		}
		
		
		
	
		
		
		
		
	}