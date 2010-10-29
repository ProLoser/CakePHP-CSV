<?php
/**
 * CSV Component
 *
 * @author Dean Sofer (proloser@hotmail.com)
 * @version 1.0
 * @package CSV Plugin
 **/
class CsvComponent extends Object {
	
	
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
	function import($filename, $fields = array()) {

		$data = array();

		// open the file
		if ($file = @fopen(WWW_ROOT . $filename, 'r')) {
			if (empty($fields)) {
				// read the 1st row as headings
				$fields = fgetcsv($file);
			}
			// Row counter
			$r = 0; 
			// read each data row in the file
			while ($row = fgetcsv($file)) {
				// for each header field 
				foreach ($fields as $f => $field) {
					// get the data field from Model.field
					if (strpos($field,'.')) {
						$keys = explode('.',$field);
						if (isset($keys[2])) {
							$data[$r][$keys[0]][$keys[1]][$keys[2]] = $row[$f];
						} else {
							$data[$r][$keys[0]][$keys[1]] = $row[$f];
						}
					} else {
						$data[$r][$this->controller->modelClass][$field] = $row[$f];
					}
				}
				$r++;
			}

			// close the file
			fclose($file);

			// return the messages
			return $data;
		} else {
			return false;
		}
	}
	
	/**
	 * Converts a data array into 
	 *
	 * @param string $filename 
	 * @param string $data 
	 * @return void
	 * @author Dean
	 */
	public function export($filename, $data, $options = array()) {
		$options = array_merge(array(
			'delimiter' => ',',
			'enclosure' => '"',
			'headers' => true,
		), $options);
		
		// open the file
		if ($file = @fopen(WWW_ROOT . $filename, 'w')) {
			
			// Iterate through and format data
			foreach ($data as $record) {
				$firstRecord = true;
				$row = array();
				foreach ($record as $model => $fields) {
					// TODO add parsing for HABTM
					foreach ($fields as $field => $value) {
						if (!is_array($value)) {
							if ($firstRecord) {
								$headers[] = $model . '.' . $field;
							}
							$row[] = $value;
						} // TODO due to HABTM potentially being huge, creating an else might not be plausible
					}
				}
				$rows[] = $row;
				$firstRecord = false;
			}
			
			if ($options['headers']) {
				// write the 1st row as headings
				fputcsv($file, $headers, $options['delimiter'], $options['enclosure']);
			}
			// Row counter
			$r = 0; 
			foreach ($rows as $row) {
				fputcsv($file, $row, $options['delimiter'], $options['enclosure']);
				$r++;
			}

			// close the file
			fclose($file);

			return $r;
		} else {
			return false;
		}
	}
}
?>