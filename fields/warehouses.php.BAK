<?php defined('JPATH_PLATFORM') or die;

			
 
use NovaPoshta\ApiModels\Address;
use NovaPoshta\MethodParameters\Address_getStreet;
use NovaPoshta\MethodParameters\Address_getWarehouses;
use NovaPoshta\MethodParameters\Address_getCities;
use NovaPoshta\MethodParameters\Address_getAreas;






class JFormFieldWarehouses extends JFormFieldList {

	/** 
	 * The form field type.
	 *
	 * @var    string
	 * @since  11.1
	 */
	protected $type = 'Warehouses';
 
	public 		$paramsPLG ; 
	protected 	$cache ;
	protected 	$VMUPS ;
	
	public function __construct($form = null)
	{
		
		parent::__construct($form);
		$plg = JPluginHelper::getPlugin('vmshipment','vmups');
		$this->paramsPLG = class_exists('JParameter') ? @new JParameter($plg->params) : @new JRegistry($plg->params);
		
		jimport('joomla.cache.cache');
		// $conf =  JFactory::getConfig();
		$options = array(
	 		'cachebase'    => JPATH_ROOT.DS.'cache' , // кеш папка в корне сайта
			'lifetime'     => 1440 , //  (int) $conf->get('cachetime'), // Врямя жизни кэша
			'caching'      => true , //   принудительное включение кэша
		);
		// libraries/joomla/cache/controller.php
		$this->cache = new JCache($options);
		
		
		
		$session = JFactory::getSession();
		$DefaultArr = array(
			'novaposhta' => array(
				'cityRef' => '8e1718f5-1972-11e5-add9-005056887b8d'	
			)
		);
		
		$VMUPS = $session->get('vmUps', $DefaultArr);
		if( !isset ( $VMUPS['novaposhta'] ) ){
			$VMUPS['novaposhta'] = $DefaultArr['novaposhta'];	
		} // end if
		$this->VMUPS = $VMUPS;
		
		
		
	} // end function

	 protected function getLabel() {
		 
		$key = $this->paramsPLG->get('apikey' , '');
		if( !$key  ){return '';} // end if
		return parent::getLabel();
	}
	 

	/*
	* Получение списка отделений в выбраном городе новой почты 
	*
	*
	*/
	public function getWarehouses($CityRef = false){
		
		 
		
		$app =  JFactory::getApplication();
		if ($app->isSite()) {
			if(  !$CityRef ){
				$key =  $this->VMUPS['novaposhta']['cityRef'];	
			}else{
				$key =	$CityRef;
			} // end if
			if( !$key ){
				$key = '';	
			} // end if
		}
 		if ($app->isAdmin()) {
			
			$view = vRequest::getCmd('view', 'orders') ;
			$layout = vRequest::getCmd('layout', false) ;
			if( $view == 'plugin' && $layout == 'edit' ){
				$key = $this->paramsPLG->get('citySender' , '');	
			}else{
				$key =  $this->VMUPS['novaposhta']['cityRef'];	
			} // end if
			if( !$key  ){return '';} // end if	
		}
		 
	 
	 
		if ( !( $Warehouse = $this->cache->get( $key, 'plg_vmups' ) ) ) {
			
			
			$data = new \NovaPoshta\MethodParameters\MethodParameters();
			$data->CityRef = $key;
			$res = \NovaPoshta\ApiModels\Address::getWarehouses($data);
			$Warehouse =  json_encode ( $res->data );
			$this->cache->store( $Warehouse  , $key, 'plg_vmups' );
		}
		
		$ret = json_decode ($Warehouse ) ;
		
	 
		return $ret ;
	} // end function
	
	
	/**
	 * Method to get the field input markup.
	 * @return  string  The field input markup.
	 * @since   11.1
	 */
	protected function getInput(){
		 
		
		 $key = $this->paramsPLG->get('apikey' , '');
		if( !$key  ){return '';} // end if
		$key = $this->paramsPLG->get('citySender' , '');
		if( !$key  ){return '';} // end if
		$html = array();
		$attr = '';
		// Initialize some field attributes.
		$attr .= !empty($this->class) ? ' class="' . $this->class . '"' : '';
		$attr .= !empty($this->size) ? ' size="' . $this->size . '"' : '';
		$attr .= $this->multiple ? ' multiple' : '';
		$attr .= $this->required ? ' required aria-required="true"' : '';
		$attr .= $this->autofocus ? ' autofocus' : '';
		// $attr .= (string)$this->element['data-dynamic-update'] ? 'data-dynamic-update="1"' : '';
		// Initialize JavaScript field attributes.
		$attr .= $this->onchange ? ' onchange="' . $this->onchange . '"' : '';
		// Get the field options.
		$options = (array) $this->getOptions();
		 
		$html[] = JHtml::_('select.genericlist', $options, $this->name, trim($attr), 'value', 'text', $this->value, $this->id);
		return implode($html);
	}

 

	/**
	 * Method to get the field options.
	 *
	 * @return  array  The field option objects.
	 *
	 * @since   3.4
	 */
	protected function getOptions() {
		
		 
		
		$cityList = $this->getWarehouses();
		$options = array();
		
		if( count ($cityList) == 1 ){
			$this->value =  $cityList[0]->Ref ;
		}else{
			//  $options[] = JHTML::_('select.option',  '0', vmText::_('PLG_VMUPS_SELECT'), 'value', 'text' );	
		} // end if
		
		foreach ($cityList as $option) {
			// Create a new option object based on the <option /> element.
			$options[] = JHtml::_( 
				'select.option', 
				(string) $option->Ref ,
				JText::alt(trim((string) $option -> Description), 
				preg_replace('/[^a-zA-Z0-9_\-]/', '_', $this->fieldname)), 
				'value', 
				'text'
			);
		}
		return $options;
	}  // end function

	 
}
