<?php
	/**
	 * @package     Plg\Np
	 * @subpackage
	 *
	 * @copyright   A copyright
	 * @license     A "Slug" license name e.g. GPL2
	 */
	
	namespace Plg\Np;
	
	
	class Html
	{
		/**
		 *
		 *
		 * @since version
		 */
		public static function getModalBody(){
			
			$data = [];
			
			# Загрузить форму
			$form = \JForm::getInstance( 'ModalBody', JPATH_PLUGINS.'/vmshipment/nova_pochta/forms/cartModalBody.xml');
			$form->bind($data);
			$viewData['formField'] =  $form->renderFieldset('courierAdress');
			
			# Подключение слоя
			$layout    = new \JLayoutFile( 'cartModalBody' ,JPATH_PLUGINS.'/vmshipment/nova_pochta/tmpl' );
			return $layout->render($viewData);
			
			
			
			
			
			
			echo'<pre>';print_r( $viewData );echo'</pre>'.__FILE__.' '.__LINE__;
			die(__FILE__ .' '. __LINE__ );
			return 'xxxxxx' ;
		}
	}