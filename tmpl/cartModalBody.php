<?php
	/**
	 * @package     ${NAMESPACE}
	 * @subpackage
	 *
	 * @copyright   A copyright
	 * @license     A "Slug" license name e.g. GPL2
	 */
	
extract ($displayData);
	$app = \JFactory::getApplication() ;
	$admin = $app->isClient('administrator') ;

	
?>

<div id="cartModalBody">
	<?php
        
		
		if( \Plg\Np\Helper::$options->form_style   )
		{
	?>
            <form id="form-cartModalBody" >
		<?php
			}#END IF
		?>
	
		<div class="f-cont">
			<?= $formField ; ?>
			<?= JHtml::_( 'form.token' ); ?>
		</div>
	    <?php
		    if( \Plg\Np\Helper::$options->form_style   )
		    {
	    ?>
            <div class="btn-wrapper" id="toolbar-apply">
            <button id="NpSave" onclick="" class="btn btn-small button-apply btn-success">
                <span class="icon-apply icon-white" aria-hidden="true"></span>
                Сохранить</button>
        </div>
            </form>
		    <?php
		    }#END IF
		    ?>
    
</div>
