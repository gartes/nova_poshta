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
	
	
	class ApiNpCounterparty
	{
		public static function getCounterparties()
		{
			$data = new Counterparty_getCounterparties();
			$data->setCounterpartyProperty('Recipient');
			$data->setPage(1);
			$data->setCityRef('8d5a980d-391c-11dd-90d9-001a92567626');
			$data->setFindByString('Петр');
			// или
			//        $data->setRef('adcad698-011c-11e5-ad08-005056801333');
			
			return Counterparty::getCounterparties($data);
		}
		
		
	}