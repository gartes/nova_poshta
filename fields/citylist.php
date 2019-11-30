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
class JFormFieldCitylist extends JFormFieldList
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since  1.7.0
	 */
	protected $type = 'Citylist';

	protected static $settingPLG ;
	
	/**
	 * Настройки
	 *
	 * @throws Exception
	 * @since version
	 */
	protected static function getSetting(){
		$app                          = \JFactory::getApplication();
		if (!class_exists( 'VmConfig' )) require(JPATH_ROOT .'/administrator/components/com_virtuemart/helpers/config.php');
		VmConfig::loadConfig();
		
		$virtuemart_shipmentmethod_id = $app->input->get( 'virtuemart_shipmentmethod_id' , null , 'INT' );
		if(  !$virtuemart_shipmentmethod_id )
		{
			throw new Exception('Err: Dont virtuemart shipmentmethod id In JFormFieldCitylist.' , 500 ) ;
		}#END IF
		
		$shipmentModel = VmModel::getModel( 'Shipmentmethod' );
		$shipmentModel->setId( $virtuemart_shipmentmethod_id );
		self::$settingPLG = $shipmentModel->getShipment();
		
		
	}
	
	/**
	 * Method to get the field input markup for a generic list.
	 * Use the multiple attribute to enable multiselect.
	 *
	 * @return  string  The field input markup.
	 *
	 * @throws Exception
	 * @since   3.7.0
	 */
	protected function getInput()
	{
		
		self::getSetting();
		
		$setting = self::$settingPLG ;
		if( !$setting->city_celect_style )
		{
			return '<input type="text"
						name="cityText"
						id="cityText"
						value=""
						class="ac_Settlements cityText"
						autocomplete="off">
						
					<i class="auto_control clean icon-cancel" onclick="jQuery(this).prev(\'input\').val(\'\')"></i>
						
						' ;
		}#END IF
		
		
		
		
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
		$format = $app->input->get( 'format' , 'hrml' , 'STRING' ) ;
		$opt = $app->input->get( 'opt' , [] , 'ARRAY' ) ;
		
		
		
		if (!class_exists( 'VmConfig' )) require(JPATH_ROOT .'/administrator/components/com_virtuemart/helpers/config.php');
		VmConfig::loadConfig();
		
		
		
		$virtuemart_shipmentmethod_id = $app->input->get( 'virtuemart_shipmentmethod_id' , null , 'INT' );
		if(  !$virtuemart_shipmentmethod_id )
		{
			throw new Exception('Err: Dont virtuemart shipmentmethod id In JFormFieldCitylist.' , 500 ) ;
		}#END IF
		
		
		
		
		$shipmentModel = VmModel::getModel( 'Shipmentmethod' );
		$shipmentModel->setId( $virtuemart_shipmentmethod_id );
		$shipmentMethod = $shipmentModel->getShipment();
		
		
		
		$tagLanguage = JFactory::getLanguage()->getTag();
		$lp = null ;
		$options = [''=>'Виберіть місто...'] ;
		if( $tagLanguage == 'ru-RU' )
		{
			$lp = 'Ru';
			$options = [''=>'Выберите город...'] ;
		}#END IF
		
		
		if( $format == 'raw' && $opt[ 'task' ] == 'loadWarehouses'  )
		{
			return null;
		}#END IF
		
		if( !$shipmentMethod->city_celect_style )
		{
			return null;
		}#END IF
		
		
		$Address = \Plg\Np\Api::getAddress( $shipmentMethod->apikey );
		$CityList = $Address::getCityList();
		
		
		
		foreach( $CityList->data as $datum )
		{
			$options[$datum->Ref] = $datum->{'Description'.$lp} .' '. $datum->{'SettlementTypeDescription'.$lp} ;
		}#END FOREACH
		
		
		
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
