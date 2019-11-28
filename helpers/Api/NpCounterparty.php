<?php
	/**
	 * @package     ${NAMESPACE}
	 * @subpackage
	 *
	 * @copyright   A copyright
	 * @license     A "Slug" license name e.g. GPL2
	 */
	
	namespace Plg\Np\Api;
	
	use NovaPoshta\ApiModels\Counterparty;
	use NovaPoshta\MethodParameters\Counterparty_getCounterparties;
	use NovaPoshta\MethodParameters\Counterparty_getCounterpartyAddresses;
	use NovaPoshta\MethodParameters\Counterparty_getCounterpartyContactPersons;
	use NovaPoshta\MethodParameters\Counterparty_getCounterpartyOptions;
	use NovaPoshta\MethodParameters\Counterparty_getCounterpartyByEDRPOU;
	use NovaPoshta\MethodParameters\Counterparty_cloneLoyaltyCounterpartySender;
	use NovaPoshta\MethodParameters\MethodParameters;
	
	
	class NpCounterparty
	{
	 
		
		/**
		 * NpCounterparty constructor.
		 */
		public function __construct ()
		{
			return $this ;
		}
		
		static $aptamsDef = [
			'Property'=> 'Recipient' ,
			'Page'=> 1 ,
			
		];
		
		public static function getCounterparties( $params = [] )
		{
			
			$setParams = array_merge( self::$aptamsDef , $params ) ;
			
			$data = new Counterparty_getCounterparties();
			$data->setCounterpartyProperty( $setParams['Property'] );
			return Counterparty::getCounterparties($data);
			
			
			
			
//			$data->setPage(1);
//			$data->setCityRef($setParams['CityRef'] );
//			$data->setFindByString('Петр');
			// или
			//        $data->setRef('adcad698-011c-11e5-ad08-005056801333');
			
			
		}
		
		
	}