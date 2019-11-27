<?php
	/**
	 * @package     Plg\Np
	 * @subpackage
	 *
	 * @copyright   A copyright
	 * @license     A "Slug" license name e.g. GPL2
	 */
	
	namespace Plg\Np;
	
	
	class Api
	{
	
	
	\JLoader::registerNamespace('NovaPoshta',JPATH_LIBRARIES.'/GNZ11/Api/Shipment/NovaPoshta',$reset=false,$prepend=false,$type='psr4');
	$counterparty = new Counterparty();
	
	
	}