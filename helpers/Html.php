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
		 * @param $shipinfo
		 *
		 * @return string
		 *
		 * @throws \Exception
		 * @since version
		 */
		public static function OrderShipmentHtmlBE($shipinfo){
			
			$app = \JFactory::getApplication() ;
			$app->input->set('virtuemart_shipmentmethod_id' , $shipinfo->virtuemart_shipmentmethod_id );
			
			// $shipinfo->novaposhta = json_decode( $shipinfo->novaposhta ) ;
			$app->input->set('ref_city' , $shipinfo->novaposhta->Ref  );
			$app->input->set('ref_city_delivery' , $shipinfo->ref_city  );
			
			$shipinfo->cityRef = $shipinfo->ref_city ;
			
			# Загрузить форму
			$form = \JForm::getInstance( 'ModalBody', JPATH_PLUGINS.'/vmshipment/nova_pochta/forms/admin_cartModalBody.xml');
			$form->bind($shipinfo);
			$viewData['formField'] =  $form->renderFieldset('courierAdress');
			
			$html = '<form id="admin-order-edit">';
			
			# Подключение слоя
			$layout    = new \JLayoutFile( 'cartModalBody' ,JPATH_PLUGINS.'/vmshipment/nova_pochta/tmpl' );
			$html .=  $layout->render($viewData);
			
			$layout    = new \JLayoutFile( 'adminBtn' ,JPATH_PLUGINS.'/vmshipment/nova_pochta/tmpl' );
			$html .=  $layout->render($viewData);
			
			$html .= '</form>';
			
			
			
			return $html ;
			
			
		}
		
		
		/**
		 * Подгрузка складов в городе
		 * @return string
		 *
		 * @throws \Exception
		 * @since version
		 */
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
			$data = [''];
			# Загрузить форму
			$form = \JForm::getInstance( 'ModalBody', JPATH_PLUGINS.'/vmshipment/nova_pochta/forms/cartModalBody.xml');
			$form->bind($data);
			$viewData['formField'] =  $form->renderFieldset('courierAdress');
			
			# Подключение слоя
			$layout    = new \JLayoutFile( 'cartModalBody' ,JPATH_PLUGINS.'/vmshipment/nova_pochta/tmpl' );
			return $layout->render($viewData);
		}
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
	}