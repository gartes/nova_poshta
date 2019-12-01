<?php
/**
 * @package     Joomla.Platform
 * @subpackage  Form
 *
 * @copyright   Copyright (C) 2005 - 2019 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

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
	 * @var    string
	 * @since  1.7.0
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
	protected function getInput()
	{
		
		
		
		$html = array();
		$attr = '';

		// Initialize some field attributes.
		$attr .= !empty($this->class) ? ' class="' . $this->class . '"' : '';
		$attr .= !empty($this->size) ? ' size="' . $this->size . '"' : '';
		$attr .= $this->multiple ? ' multiple' : '';
		$attr .= $this->required ? ' required aria-required="true"' : '';
		$attr .= $this->autofocus ? ' autofocus' : '';

		// To avoid user's confusion, readonly="true" should imply disabled="true".
		if ((string) $this->readonly == '1' || (string) $this->readonly == 'true' || (string) $this->disabled == '1'|| (string) $this->disabled == 'true')
		{
			$attr .= ' disabled="disabled"';
		}

		// Initialize JavaScript field attributes.
		$attr .= $this->onchange ? ' onchange="' . $this->onchange . '"' : '';

		// Get the field options.
		$options = (array) $this->getOptions();

		// Create a read-only list (no name) with hidden input(s) to store the value(s).
		if ((string) $this->readonly == '1' || (string) $this->readonly == 'true')
		{
			$html[] = JHtml::_('select.genericlist', $options, '', trim($attr), 'value', 'text', $this->value, $this->id);

			// E.g. form field type tag sends $this->value as array
			if ($this->multiple && is_array($this->value))
			{
				if (!count($this->value))
				{
					$this->value[] = '';
				}

				foreach ($this->value as $value)
				{
					$html[] = '<input type="hidden" name="' . $this->name . '" value="' . htmlspecialchars($value, ENT_COMPAT, 'UTF-8') . '"/>';
				}
			}
			else
			{
				$html[] = '<input type="hidden" name="' . $this->name . '" value="' . htmlspecialchars($this->value, ENT_COMPAT, 'UTF-8') . '"/>';
			}
		}
		else
		// Create a regular list passing the arguments in an array.
		{
			$listoptions = array();
			$listoptions['option.key'] = 'value';
			$listoptions['option.text'] = 'text';
			$listoptions['list.select'] = $this->value;
			$listoptions['id'] = $this->id;
			$listoptions['list.translate'] = false;
			$listoptions['option.attr'] = 'optionattr';
			$listoptions['list.attr'] = trim($attr);

			$html[] = JHtml::_('select.genericlist', $options, $this->name, $listoptions);
		}

		return implode($html);
	}
	
	/**
	 * Method to get the field options.
	 *
	 * @return  array  The field option objects.
	 *
	 * @throws Exception
	 * @since   3.7.0
	 */
	protected function getOptions()
	{
		$app                          = \JFactory::getApplication();
		
		$virtuemart_shipmentmethod_id = $app->input->get( 'virtuemart_shipmentmethod_id' , null , 'INT' );
		if(  !$virtuemart_shipmentmethod_id )
		{
			throw new Exception('Err: Dont virtuemart shipmentmethod id In JFormFieldCitylist.' , 500 ) ;
		}#END IF
		
		$opt = $app->input->get( 'opt' , [] , 'ARRAY' ) ;
		$format = $app->input->get( 'format' , 'html' , 'STRING' ) ;
		
		
		$tagLanguage = JFactory::getLanguage()->getTag();
		$lp = null ;
		$options = [''=>'Виберіть відділення...'] ;
		if( $tagLanguage == 'ru-RU' )
		{
			$lp = 'Ru';
			$options = [''=>'Выберите отделение...'] ;
		}#END IF
		
		if( $opt['task']!= 'loadWarehouses' &&  $format    == 'raw'  )
		{
			return $options ;
		}#END IF
		
		if( $app->isClient('administrator') )
		{
			$opt['cityRef'] = $app->input->get('ref_city_delivery' , null ) ;
			
		
		}#END IF
		
		
 	
		
		if (!class_exists( 'VmConfig' )) require(JPATH_ROOT .'/administrator/components/com_virtuemart/helpers/config.php');
		VmConfig::loadConfig();
		$shipmentModel = VmModel::getModel( 'Shipmentmethod' );
		$shipmentModel->setId( $virtuemart_shipmentmethod_id );
		$shipmentMethod = $shipmentModel->getShipment();
		
		
//		echo'<pre>';print_r( $opt );echo'</pre>'.__FILE__.' '.__LINE__;
//		die(__FILE__ .' '. __LINE__ );
		
		
		$Address = \Plg\Np\Api::getAddress( $shipmentMethod->apikey );
		$WarehousesList = $Address::getWarehouses($opt['cityRef']);
		
		if( !count($WarehousesList->data) &&  $format    == 'raw' )
		{
			echo new JResponseJson(null , JText::_('NOVA_POCHTA_WAREHOUSES_LIST_Z_COUNT'));
			$app->close();
		}#END IF
		
		
		
		
		foreach( $WarehousesList->data as $datum )
		{
			$options[$datum->Ref] = $datum->{'Description'.$lp} .' '. $datum->{'SettlementTypeDescription'.$lp} ;
		}#END FOREACH
		
		
		
		if(  $format  == 'raw' )
		{
			$ret = [
				'WarehousesList'=>$options ,
				'info'=> json_encode( $WarehousesList->info )  ,
			];
			echo new JResponseJson( $ret  );
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
	public function addOption($text, $attributes = array())
	{
		if ($text && $this->element instanceof SimpleXMLElement)
		{
			$child = $this->element->addChild('option', $text);

			foreach ($attributes as $name => $value)
			{
				$child->addAttribute($name, $value);
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
	public function __get($name)
	{
		if ($name == 'options')
		{
			return $this->getOptions();
		}

		return parent::__get($name);
	}
}
