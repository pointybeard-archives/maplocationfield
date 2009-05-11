<?php

	Class extension_maplocationfield extends Extension{
	
		public function about(){
			return array('name' => 'Field: Map Location',
						 'version' => '1.0',
						 'release-date' => '2008-02-01',
						 'author' => array('name' => 'Symphony Team',
										   'website' => 'http://www.symphony21.com',
										   'email' => 'team@symphony21.com')
				 		);
		}
		
		public function uninstall(){
			$this->_Parent->Database->query("DROP TABLE `tbl_fields_maplocation`");

			$this->_Parent->Configuration->remove('google-api-key', 'map-location-field');			
			$this->_Parent->saveConfig();
		}
		
		public function getSubscribedDelegates(){
			return array(
						array(

							'page' => '/system/preferences/',
							'delegate' => 'AddCustomPreferenceFieldsets',
							'callback' => 'appendPreferences'

						),
					);
		}		

		public function appendPreferences($context){
			$group = new XMLElement('fieldset');
			$group->setAttribute('class', 'settings');
			$group->appendChild(new XMLElement('legend', 'Map Location Field'));

			$label = Widget::Label('Google Maps API Key');
			$label->appendChild(Widget::Input('settings[map-location-field][google-api-key]', General::Sanitize($context['parent']->Configuration->get('google-api-key', 'map-location-field'))));		
			$group->appendChild($label);
			
			$group->appendChild(new XMLElement('p', 'Get a Google Maps API key from the <a href="http://code.google.com/apis/maps/index.html">Google Maps site</a>.', array('class' => 'help')));
			
			$context['wrapper']->appendChild($group);
						
		}

		public function install(){

			return $this->_Parent->Database->query("CREATE TABLE `tbl_fields_maplocation` (
			  `id` int(11) unsigned NOT NULL auto_increment,
			  `field_id` int(11) unsigned NOT NULL,
			  `default_location` varchar(60) NOT NULL,
			  `default_location_coords` varchar(60) NOT NULL,
			  PRIMARY KEY  (`id`),
			  UNIQUE KEY `field_id` (`field_id`)
			) TYPE=MyISAM");

		}
			
	}

?>