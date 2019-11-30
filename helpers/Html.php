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
		
		public static function loadWarehouses(){
			$data = [];
			$app = \JFactory::getApplication() ;
			$virtuemart_shipmentmethod_id = $app->input->get('virtuemart_shipmentmethod_id') ;
			
			# Загрузить форму
			$form = \JForm::getInstance( 'ModalBody', JPATH_PLUGINS.'/vmshipment/nova_pochta/forms/cartModalBody.xml');
			$form->bind($data);
			
			
			
			$viewData['formField'] =  $form->renderFieldset('courierAdress');
			# Подключение слоя
			$layout    = new \JLayoutFile( 'cartModalBody' ,JPATH_PLUGINS.'/vmshipment/nova_pochta/tmpl' );
			return $layout->render($viewData);
		}
		
		
		/**
		 *
		 *
		 * @since version
		 */
		public static function getModalBody(){
			
			$app = \JFactory::getApplication() ;
			
			
			
			$data = [
				''
			];
			
			
			
			
			# Загрузить форму
			$form = \JForm::getInstance( 'ModalBody', JPATH_PLUGINS.'/vmshipment/nova_pochta/forms/cartModalBody.xml');
			$form->bind($data);
			$viewData['formField'] =  $form->renderFieldset('courierAdress');
			
			# Подключение слоя
			$layout    = new \JLayoutFile( 'cartModalBody' ,JPATH_PLUGINS.'/vmshipment/nova_pochta/tmpl' );
			return $layout->render($viewData);
		}
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
	}