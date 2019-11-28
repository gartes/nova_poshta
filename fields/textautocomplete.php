<?php defined('JPATH_PLATFORM') or die;

class JFormFieldTextAutocomplete extends JFormFieldText
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since  11.1
	 */
	protected $type = 'TextAutocomplete';
	protected $autocomplete = false;
	/**
	 * The allowable maxlength of the field.
	 *
	 * @var    integer
	 * @since  3.2
	 */
	protected $maxLength;

	/**
	 * The mode of input associated with the field.
	 *
	 * @var    mixed
	 * @since  3.2
	 */
	protected $inputmode;

	/**
	 * The name of the form field direction (ltr or rtl).
	 *
	 * @var    string
	 * @since  3.2
	 */
	protected $dirname;

	/**
	 * Name of the layout being used to render the field
	 *
	 * @var    string
	 * @since  3.7
	 */
	protected $layout = 'joomla.form.field.text';

	/**
	 * Method to get certain otherwise inaccessible properties from the form field object.
	 *
	 * @param   string  $name  The property name for which to the the value.
	 *
	 * @return  mixed  The property value or null.
	 *
	 * @since   3.2
	 */
	/*public function __get($name){
		switch ($name)
		{
			case 'maxLength':
			case 'dirname':
			case 'inputmode':
				return $this->$name;
		}

		return parent::__get($name);
	}*/

	/**
	 * Method to set certain otherwise inaccessible properties of the form field object.
	 *
	 * @param   string  $name   The property name for which to the the value.
	 * @param   mixed   $value  The value of the property.
	 *
	 * @return  void
	 *
	 * @since   3.2
	 */
	/*public function __set($name, $value){
		switch ($name)
		{
			case 'maxLength':
				$this->maxLength = (int) $value;
				break;

			case 'dirname':
				$value = (string) $value;
				$this->dirname = ($value == $name || $value == 'true' || $value == '1');
				break;

			case 'inputmode':
				$this->inputmode = (string) $value;
				break;

			default:
				parent::__set($name, $value);
		}
	}*/

	/**
	 * Method to attach a JForm object to the field.
	 *
	 * @param   SimpleXMLElement  $element  The SimpleXMLElement object representing the `<field>` tag for the form field object.
	 * @param   mixed             $value    The form field value to validate.
	 * @param   string            $group    The field name group control value. This acts as as an array container for the field.
	 *                                      For example if the field has name="foo" and the group value is set to "bar" then the
	 *                                      full field name would end up being "bar[foo]".
	 *
	 * @return  boolean  True on success.
	 *
	 * @see     JFormField::setup()
	 * @since   3.2
	 */
	/*public function setup(SimpleXMLElement $element, $value, $group = null){
		$result = parent::setup($element, $value, $group);

		if ($result == true)
		{
			$inputmode = (string) $this->element['inputmode'];
			$dirname = (string) $this->element['dirname'];

			$this->inputmode = '';
			$inputmode = preg_replace('/\s+/', ' ', trim($inputmode));
			$inputmode = explode(' ', $inputmode);

			if (!empty($inputmode))
			{
				$defaultInputmode = in_array('default', $inputmode) ? JText::_('JLIB_FORM_INPUTMODE') . ' ' : '';

				foreach (array_keys($inputmode, 'default') as $key)
				{
					unset($inputmode[$key]);
				}

				$this->inputmode = $defaultInputmode . implode(' ', $inputmode);
			}

			// Set the dirname.
			$dirname = ((string) $dirname == 'dirname' || $dirname == 'true' || $dirname == '1');
			$this->dirname = $dirname ? $this->getName($this->fieldname . '_dir') : false;

			$this->maxLength = (int) $this->element['maxlength'];
		}

		return $result;
	}*/
	
	
	 
	
	/**
	 * Method to get the field input markup.
	 *
	 * @return  string  The field input markup.
	 *
	 * @since   11.1
	 */
	protected function getInput(){
		$html = parent::getInput() ;
		$this->autocomplete = false ; 
		
		$doc = JFactory::getDocument();
		
		$JS = "\n";
		
		$JS .= 'jQuery( document ).on("autocompletecreate", function(event, ui){
					jQuery( \'[name="'.$this->name.'"]\' ).trigger("UkCpuAutocompletecreate")
				});'. "\n";
		
		
		$JS .= 'jQuery( \'[name="'.$this->name.'"]\' ).on("autocompletecreate", function(event, ui){
					//event.target.id
					var $ = jQuery,
						e = $(event.target)	,	
						name = e.attr("name");
						
					if(name=="'.$this->name.'"){
						
						var anchor = $("<div />", {
							id:"'.(string)$this->id.'_autocompleteConteyner" ,
							calss:"ukCpuAcConteyner conteynerUiAc" ,
						});	
						jQuery( \'[name="'.$this->name.'"]\' ).after(anchor)
						
						
						jQuery( \'[name="'.$this->name.'"]\' ).after(
							jQuery( "<i />",{
								class:"auto_control clean icon-cancel",
								click:function  (){
									jQuery(this).prev("input").val("");
									VMUPS.setDefaultValue(this);
								}
							}
						)
						);
					}
				});'. "\n";
		
		$JS .='jQuery( \'[name="'.$this->name.'"]\' ).autocomplete();' . "\n";
		
		
		
		if( !empty($this->element['appendTo']) ){
			
			$JS .= 'jQuery( \'[name="'.$this->name.'"]\' )
						.autocomplete( "option", "appendTo", "'.$this->element['appendTo'].'" );' . "\n";
		}else{
			/*"#textAutocomplite_'.$this->name.'" );*/
			$JS .= 'jQuery( \'[name="'.$this->name.'"]\' )
						.autocomplete("option","appendTo","#'.(string)$this->id.'_autocompleteConteyner");' . "\n";
		} // end if
		
		
		
		if( $this->element['minLength'] ){
			$JS .='jQuery( \'[name="'.$this->name.'"]\' ).autocomplete( "option", "minLength", '.$this->element['minLength'].' );' . "\n";
		} // end if
		
		
		
		 
		if( !empty($this->element['html']) ){
			$JS .= 'jQuery(\'[name="'.$this->name.'"]\' ).autocomplete( "option", "html", '.$this->element['html'].');' . "\n";	
		}
	 	
		// source="function:sourceCityGlavpunkt"
		if( !empty($this->element['source']) ){
			$source = explode ( ":" ,$this->element['source'] );
			if( $source[0]=='function' ){
				$JS .= 'jQuery( \'[name="'.$this->name.'"]\' ).autocomplete( "option", "source", '.$source[1].');' . "\n";		
			} // end if
		}
		
		
		if( !empty($this->element['select']) ){
			$JS .= 'jQuery( \'[name="'.$this->name.'"]\' ).off("autocompleteselect").on( "autocompleteselect",'.$this->element['select'].');' . "\n";	
		}
		
		if( !empty($this->element['change']) ){
			$JS .= 'jQuery( \'[name="'.$this->name.'"]\' ).off("autocompletechange").on( "autocompletechange",'.$this->element['change'].');' . "\n";	
		}
		
		
		if( !empty($this->element['search']) ){
			$JS .= 'jQuery( \'[name="'.$this->name.'"]\' ).on( "autocompletesearch",'.$this->element['search'].');' . "\n";	
		}
		
		if( !empty($this->element['open']) ){
			$JS .= 'jQuery( \'[name="'.$this->name.'"]\' ).on( "autocompleteopen",'.$this->element['open'].');' . "\n";	
		}
		
		 
		
		
		// $doc->addScript('//github.com/jquery/jquery-ui/blob/master/ui/widgets/autocomplete.js?v=1.8.23','text/javascript'); 
		
		
		
		
		
		
		$doc->addScriptDeclaration( 'jQuery(document).on("afterVmupsInit", function  (){'.$JS.' }) ' );	
				
		return $html;
		
		 
	}

	/**
	 * Method to get the field options.
	 *
	 * @return  array  The field option objects.
	 *
	 * @since   3.4
	 */
	protected function getOptions() {
		$options = array();
		
		foreach ($this->element->children() as $option)
		{
			// Only add <option /> elements.
			if ($option->getName() != 'option')
			{
				continue;
			}

			// Create a new option object based on the <option /> element.
			$options[] = JHtml::_(
				'select.option', (string) $option['value'],
				JText::alt(trim((string) $option), preg_replace('/[^a-zA-Z0-9_\-]/', '_', $this->fieldname)), 'value', 'text'
			);
		}

		return $options;
	}

	 

	/**
	 * Method to get the data to be passed to the layout for rendering.
	 *
	 * @return  array
	 *
	 * @since 3.7
	 */
	protected function getLayoutData() {
		$data = parent::getLayoutData();
			
			
			
		// Initialize some field attributes.
		$maxLength    = !empty($this->maxLength) ? ' maxlength="' . $this->maxLength . '"' : '';
		$inputmode    = !empty($this->inputmode) ? ' inputmode="' . $this->inputmode . '"' : '';
		$dirname      = !empty($this->dirname) ? ' dirname="' . $this->dirname . '"' : '';
		
		/* Get the field options for the datalist.
			Note: getSuggestions() is deprecated and will be changed to getOptions() with 4.0. */
		$options  = (array) $this->getOptions();
		
		 
		
		$extraData = array(
			'maxLength' => $maxLength,
			'pattern'   => $this->pattern,
			'inputmode' => $inputmode,
			'dirname'   => $dirname,
			'options'   => $options,
			'autocomplete' => "off" ,
			'class' => $this->class
		);
		
		 
		
		return array_merge($data, $extraData);
	}
}
