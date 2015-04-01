<?php
/**
 * CSV Component
 *
 * @author Dean Sofer (proloser@hotmail.com)
 * @version 1.0
 * @package CSV Plugin
 **/
App::uses('Component', 'Controller');
class CsvComponent extends Component {

	/**
	 * Allows the mapping of preg-compatible regular expressions to public or
	 * private methods in this class, where the array key is a /-delimited regular
	 * expression, and the value is a class method.  Similar to the public functionality of
	 * the findBy* / findAllBy* magic methods.
	 *
	 * @var array
	 * @access public
	 */
	public $defaults = array(
		'length' => 0,
		'delimiter' => ',',
		'enclosure' => '"',
		'escape' => '\\',
		'headers' => true
	);

	public function initialize(Controller $controller, $settings = array()) {
		// saving the controller reference for later use
		$this->controller = $controller;
		$this->defaults = array_merge($this->defaults, $settings);
	}

	/**
	 * Encoding for foreign characters
	 *
	 * @var array
	 * @access protected
	 */
	protected function _encode($str = '') {
		return iconv("UTF-8","UTF-8//TRANSLIT", html_entity_decode($str, ENT_COMPAT, 'utf-8'));
	}

	/**
	 * Import public function
	 *
	 * @param string $filename path to the file under webroot
	 * @return array of all data from the csv file in [Model][field] format
	 * @author Dean Sofer
	 */
	public function import($filename, $fields = array(), $options = array()) {
		$options = array_merge($this->defaults, $options);
		$data = array();

		// open the file
		if ($file = @fopen($filename, 'r')) {
			if (empty($fields)) {
				// read the 1st row as headings
				$fields = fgetcsv($file, $options['length'], $options['delimiter'], $options['enclosure']);
			}
			// Row counter
			$r = 0;
			// read each data row in the file
			while ($row = fgetcsv($file, $options['length'], $options['delimiter'], $options['enclosure'])) {
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
		$options = array_merge($this->defaults, $options);

		// open the file
		if ($file = @fopen($filename, 'w')) {
			// Iterate through and format data
			$firstRecord = true;
			foreach ($data as $record) {
				$row = array();
				foreach ($record as $model => $fields) {
					// TODO add parsing for HABTM
					foreach ($fields as $field => $value) {
						if (!is_array($value)) {
							if ($firstRecord) {
								$headers[] = $this->_encode($model . '.' . $field);
							}
							$row[] = $this->_encode($value);
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
