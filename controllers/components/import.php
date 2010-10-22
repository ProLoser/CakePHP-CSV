<?php
/**
 * CSV Import Component
 *
 * @author Dean Sofer (proloser@hotmail.com)
 * @version 1.0
 * @package CSV Plugin
 **/
class ImportComponent extends Object {
	
	
	function initialize(&$controller, $settings = array()) {
		// saving the controller reference for later use
		$this->controller =& $controller;
	}
	
	/**
	* Import function
	*
	* @param string $filename path to the file under webroot
	* @return array of all data from the csv file in [Model][field] format
	* @author Dean Sofer
	*/
	function import($filename) {

		$data = array();

		// open the file
		if ($file = @fopen(WWW_ROOT . $filename, "r")) {
			// read the 1st row as headings
			$fields = fgetcsv($file);
			// Row counter
			$r = 0; 
			// read each data row in the file
			while ($row = fgetcsv($file)) {
				$r++;
				// for each header field 
				foreach ($fields as $f => $field) {
					// get the data field from Model.field
					if (strpos($field,'.')) {
						$keys = explode('.',$field);
						$data[$keys[0]][$r][$keys[1]] = $row[$f];
					} else {
						$data[$this->controller->modelClass][$r][$field] = $row[$f];
					}
				}
			}

			// close the file
			fclose($file);

			// return the messages
			return $data;
		} else {
			return false;
		}
	}
}
?>