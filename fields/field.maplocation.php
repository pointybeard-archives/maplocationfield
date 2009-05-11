<?php
	
	Class fieldMapLocation extends Field{
		
		function __construct(&$parent){
			parent::__construct($parent);
			$this->_name = 'Map Location';
			$this->_required = true;
			
			$this->set('required', 'yes');
			
		}

		public function mustBeUnique(){
			return true;
		}

		function displaySettingsPanel(&$wrapper, $errors=NULL){
			
			parent::displaySettingsPanel($wrapper, $errors);	
			
			$label = Widget::Label('Default Marker Location');
			$label->appendChild(Widget::Input('fields['.$this->get('sortorder').'][default_location]', $this->get('default_location')));
			$wrapper->appendChild($label);			
			
			$this->appendRequiredCheckbox($wrapper);
			$this->appendShowColumnCheckbox($wrapper);
						
		}

		function commit(){

			if(!parent::commit()) return false;
			
			$id = $this->get('id');

			if($id === false) return false;
			
			$fields = array();
			
			$fields['field_id'] = $id;
			$fields['default_location'] = $this->get('default_location');
			
			if(!$fields['default_location']) $fields['default_location'] = 'Brisbane, Australia';

			include_once(TOOLKIT . '/class.gateway.php');
            $ch = new Gateway;
            
            $ch->init();
            $ch->setopt('URL', 'http://maps.google.com/maps/geo?q='.urlencode($fields['default_location']).'&output=xml&key='.$this->_engine->Configuration->get('google-api-key', 'map-location-field'));
            
			$response = $ch->exec();

			if(!preg_match('/<Placemark/i', $response)){
				
				$fields['default_location'] = 'Brisbane, Australia';
				$fields['default_location_coords'] = '-27.46, 153.025';
			}
			
			else{	
				
				$xml = new SimpleXMLElement($response);
			
				$coords = preg_split('/,/', $xml->Response->Placemark[0]->Point->coordinates, -1, PREG_SPLIT_NO_EMPTY);

				$fields['default_location_coords'] = $coords[1] . ',' . $coords[0];
			}
			
			$this->_engine->Database->query("DELETE FROM `tbl_fields_".$this->handle()."` WHERE `field_id` = '$id' LIMIT 1");
				
			return $this->_engine->Database->insert($fields, 'tbl_fields_' . $this->handle());
					
		}

		function displayPublishPanel(&$wrapper, $data=NULL, $flagWithError=NULL, $fieldnamePrefix=NULL, $fieldnamePostfix=NULL){

			$this->_engine->Page->addScriptToHead('http://maps.google.com/maps?file=api&amp;v=2&amp;key='.$this->_engine->Configuration->get('google-api-key', 'map-location-field'), 75);
			$this->_engine->Page->addScriptToHead(URL . '/extensions/maplocationfield/assets/gmap.js', 80);
			
			$value = $data['value'];		
			$label = Widget::Label($this->get('label'));
			$label->setAttribute('class', 'GMap');
			if($this->get('required') != 'yes') $label->appendChild(new XMLElement('i', 'Optional'));
			$label->appendChild(Widget::Input('fields'.$fieldnamePrefix.'['.$this->get('element_name').']'.$fieldnamePostfix, ($value ? $value : $this->get('default_location_coords'))));

			if($flagWithError != NULL) $wrapper->appendChild(Widget::wrapFormElementWithError($label, $flagWithError));
			else $wrapper->appendChild($label);
		}
		
	}

?>