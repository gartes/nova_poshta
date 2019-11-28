<?php
	/**
	 * @package     ${NAMESPACE}
	 * @subpackage
	 *
	 * @copyright   A copyright
	 * @license     A "Slug" license name e.g. GPL2
	 */
	
	
	extract ($displayData); ?>

<div id="cartModalBody">
	<form id="form-cartModalBody" >
		
		<?= $formField ; ?>
		
		<?php echo JHtml::_( 'form.token' ); ?>
		
	</form>
</div>


