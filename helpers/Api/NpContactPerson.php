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
	
	class NpContactPerson
	{
	 
		
		
		static $aptamsDef = [ ];
		
		/**
		 * Получим контактных персон для контрагентов
		 * @param   string  $counterpartySenderRef - Ref Контрагента отправителя
		 *
		 * @return mixed
		 *
		 * @since version
		 */
		public static function getContactPerson( $counterpartySenderRef )
		{
			$data = new \NovaPoshta\MethodParameters\Counterparty_getCounterpartyContactPersons();
			$data->setRef($counterpartySenderRef);
			return   Counterparty::getCounterpartyContactPersons($data);
		}
		
		
	}