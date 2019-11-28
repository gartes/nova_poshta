<?php
	/**
	 * @package     ${NAMESPACE}
	 * @subpackage
	 *
	 * @copyright   A copyright
	 * @license     A "Slug" license name e.g. GPL2
	 */
	
extract ($displayData);

?>

<div id="cartModalBody">
	<form id="form-cartModalBody" >
		<div class="f-cont">
			<?= $formField ; ?>
			
			<?php echo JHtml::_( 'form.token' ); ?>


        
            
        </div>
        <div class="btn-wrapper" id="toolbar-apply">
            <button onclick="" class="btn btn-small button-apply btn-success">
                <span class="icon-apply icon-white" aria-hidden="true"></span>
                Сохранить</button>
        </div>
		
	</form>
</div>
