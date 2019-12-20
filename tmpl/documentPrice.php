<?php
	/**
	 * @package     ${NAMESPACE}
	 * @subpackage
	 *
	 * @copyright   A copyright
	 * @license     A "Slug" license name e.g. GPL2
	 */
	extract ($displayData);
	$currency = CurrencyDisplay::getInstance();
	?>

<div id="documentPrice"><?=JText::_('NOVA_POCHTA_DOCUMENT_PRICE')?><span><?=$currency->priceDisplay(  $Cost ) ?><span></div>
