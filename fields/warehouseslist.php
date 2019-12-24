<?php
	/**
	 * @package     Joomla.Platform
	 * @subpackage  Form
	 *
	 * @copyright   Copyright (C) 2005 - 2019 Open Source Matters, Inc. All rights reserved.
	 * @license     GNU General Public License version 2 or later; see LICENSE
	 */
	
	defined( 'JPATH_PLATFORM' ) or die;
	
	/**
	 * Form Field class for the Joomla Platform.
	 * Supports a generic list of options.
	 *
	 * @since  1.7.0
	 */
	class JFormFieldWarehouseslist extends JFormFieldList
	{
		/**
		 * The form field type.
		 *
		 * @since  1.7.0
		 * @var    string
		 */
		protected $type = 'Warehouseslist';
		
		/**
		 * Method to get the field input markup for a generic list.
		 * Use the multiple attribute to enable multiselect.
		 *
		 * @return  string  The field input markup.
		 *
		 * @since   3.7.0
		 */
		protected function getInput ()
		{
			
			
			$html = [];
			$attr = '';
			
			// Initialize some field attributes.
			$attr .= !empty( $this->class ) ? ' class="' . $this->class . '"' : '';
			$attr .= !empty( $this->size ) ? ' size="' . $this->size . '"' : '';
			$attr .= $this->multiple ? ' multiple' : '';
			$attr .= $this->required ? ' required aria-required="true"' : '';
			$attr .= $this->autofocus ? ' autofocus' : '';
			
			// To avoid user's confusion, readonly="true" should imply disabled="true".
			if( (string) $this->readonly == '1' || (string) $this->readonly == 'true' || (string) $this->disabled == '1' || (string) $this->disabled == 'true' )
			{
				$attr .= ' disabled="disabled"';
			}
			
			// Initialize JavaScript field attributes.
			$attr .= $this->onchange ? ' onchange="' . $this->onchange . '"' : '';
			
			// Get the field options.
			$options = (array) $this->getOptions();
			
			/*if( $this->id == 'novaposhta_SenderAddress' )
			{
				$attr .= ' readonly ';
				$this->readonly = 1 ;
			}#END IF*/
			
			// Create a read-only list (no name) with hidden input(s) to store the value(s).
			if( (string) $this->readonly == '1' || (string) $this->readonly == 'true' )
			{
				
				$html[] = JHtml::_( 'select.genericlist' , $options , '' , trim( $attr ) , 'value' , 'text' ,  \Plg\Np\Helper::$SenderAddress , $this->id );
				
				// E.g. form field type tag sends $this->value as array
				if( $this->multiple && is_array( $this->value ) )
				{
					if( !count( $this->value ) )
					{
						$this->value[] = '';
					}
					
					foreach( $this->value as $value )
					{
						$html[] = '<input type="hidden" name="' . $this->name . '" value="' . htmlspecialchars( $value , ENT_COMPAT , 'UTF-8' ) . '"/>';
					}
				}
				else
				{
					$html[] = '<input type="hidden" name="' . $this->name . '" value="' . htmlspecialchars( $this->value , ENT_COMPAT , 'UTF-8' ) . '"/>';
				}
			}
			else
				// Create a regular list passing the arguments in an array.
			{
				
				
				
				
				
				$WarehousesRef = \Plg\Np\Helper::$WarehousesRef;
				
				
				
				# Получение REF - города в настройках способа доставки
				if( $this->id == 'params_warehouses' )
				{
					$WarehousesRef = \Plg\Np\Helper::$SenderAddress;
					
					
				}#END IF
				
				if( $this->id == 'novaposhta_SenderAddress' )
				{
					// die(__FILE__ .' '. __LINE__ );
					$WarehousesRef = \Plg\Np\Helper::$SenderAddress;
					$attr .= ' disabled="disabled"';
					$html[] = '<input type="hidden" name="' . $this->name . '" value="' . $WarehousesRef . '"/>';
					
				}#END IF
				
				
				$listoptions                     = [];
				$listoptions[ 'option.key' ]     = 'value';
				$listoptions[ 'option.text' ]    = 'text';
				$listoptions[ 'list.select' ]    = $this->value;
				$listoptions[ 'id' ]             = $this->id;
				$listoptions[ 'list.translate' ] = false;
				$listoptions[ 'option.attr' ]    = '';
				$listoptions[ 'list.attr' ]      = trim( $attr );
				
				
				
				//			echo'<pre>';print_r( $this->id );echo'</pre>'.__FILE__.' '.__LINE__;
				//			die(__FILE__ .' '. __LINE__ );
				$html[] = JHtml::_( 'select.genericlist' , $options , $this->name , $listoptions , 'value' , 'text' , $WarehousesRef );
			}
			
			return implode( $html );
		}
		
		/**
		 * Method to get the field options.
		 *
		 * @return  array  The field option objects.
		 *
		 * @throws Exception
		 * @since   3.7.0
		 *          28e9f4b72a218df4b7e6dcb051afab6b
		 */
		protected function getOptions ()
		{
			$app = \JFactory::getApplication();
			
			$virtuemart_shipmentmethod_id = $app->input->get( 'virtuemart_shipmentmethod_id' , null , 'INT' );
			
			
			if( !$virtuemart_shipmentmethod_id )
			{
				$view = $app->input->get( 'view' , false , 'STRING' );
				$task = $app->input->get( 'task' , false , 'STRING' );
				if( $view == 'shipmentmethod' && $task == 'edit' )
				{
					$idsArr                       = $app->input->get( 'cid' , [] , 'ARRAY' );
					$virtuemart_shipmentmethod_id = $idsArr[ 0 ];
				}
				else
				{
					throw new Exception( 'Err: Dont virtuemart shipmentmethod id In JFormFieldCitylist.' , 500 );
				}#END IF
			}#END IF
			
			$opt    = $app->input->get( 'opt' , [] , 'ARRAY' );
			$format = $app->input->get( 'format' , 'html' , 'STRING' );
			
			
			$tagLanguage = JFactory::getLanguage()->getTag();
			$lp          = null;
			$options     = [ '' => 'Виберіть відділення...' ];
			if( $tagLanguage == 'ru-RU' )
			{
				$lp      = 'Ru';
				$options = [ '' => 'Выберите отделение...' ];
			}#END IF
			
			if( $opt[ 'task' ] != 'loadWarehouses' && $format == 'raw' )
			{
				return $options;
			}#END IF
			
			if( $app->isClient( 'administrator' ) )
			{
				
				$opt[ 'cityRef' ] = $app->input->get( 'ref_city_delivery' , null );
				$opt              = $app->input->get( 'opt' , [] , 'ARRAY' );
				
				if( !$opt[ 'cityRef' ] )
				{
					$opt[ 'cityRef' ] = \Plg\Np\Helper::$CityRef;
				}#END IF
				if( $this->id == 'novaposhta_SenderAddress' )
				{
					$opt[ 'cityRef' ] = \Plg\Np\Helper::$CitySender;
					
				}#END IF
			}#END IF
			$cityRef = $opt[ 'cityRef' ];
			
			if( !class_exists( 'VmConfig' ) ) require( JPATH_ROOT . '/administrator/components/com_virtuemart/helpers/config.php' );
			VmConfig::loadConfig();
			$shipmentModel = VmModel::getModel( 'Shipmentmethod' );
			$shipmentModel->setId( $virtuemart_shipmentmethod_id );
			$shipmentMethod = $shipmentModel->getShipment();
			
			# Получение REF - города в настройках способа доставки
			if( $this->id == 'params_warehouses' || $this->id == 'novaposhta_SenderAddress'   )
			{
				$cityRef = $shipmentMethod->city_sender_ref;
				\Plg\Np\Helper::setSenderAddress( $shipmentMethod->warehouses );
			}#END IF
			
			
			try
			{
				$Address        = \Plg\Np\Api::getAddress( $shipmentMethod->apikey );
				$WarehousesList = $Address::getWarehouses( $cityRef );
				
			}
			catch( Throwable $e )
			{
				$app->enqueueMessage( $e->getMessage() );
				
				return null;
			}
			
			
			
			if( !count( $WarehousesList->data ) && $format == 'json' )
			{
				echo new JResponseJson( null , JText::_( 'NOVA_POCHTA_WAREHOUSES_LIST_Z_COUNT' ) );
				$app->close();
			}#END IF
			
			
			foreach( $WarehousesList->data as $datum )
			{
				$options[ $datum->Ref ] = $datum->{'Description' . $lp} . ' ' . $datum->{'SettlementTypeDescription' . $lp};
			}#END FOREACH
			
			
			if( $format == 'json' )
			{
				$ret = [ 'WarehousesList' => $options , 'info' => json_encode( $WarehousesList->info ) , ];
				
				
				echo new JResponseJson( $ret );
				$app->close();
				
			}#END IF
			
			
			return $options;
		}
		
		/**
		 * Method to add an option to the list field.
		 *
		 * @param   string  $text        Text/Language variable of the option.
		 * @param   array   $attributes  Array of attributes ('name' => 'value' format)
		 *
		 * @return  JFormFieldList  For chaining.
		 *
		 * @since   3.7.0
		 */
		public function addOption ( $text , $attributes = [] )
		{
			if( $text && $this->element instanceof SimpleXMLElement )
			{
				$child = $this->element->addChild( 'option' , $text );
				
				foreach( $attributes as $name => $value )
				{
					$child->addAttribute( $name , $value );
				}
			}
			
			return $this;
		}
		
		/**
		 * Method to get certain otherwise inaccessible properties from the form field object.
		 *
		 * @param   string  $name  The property name for which to get the value.
		 *
		 * @return  mixed  The property value or null.
		 *
		 * @since   3.7.0
		 */
		public function __get ( $name )
		{
			if( $name == 'options' )
			{
				return $this->getOptions();
			}
			
			return parent::__get( $name );
		}
	}
