<?php
/**
 * Csv Model Behavior
 * 
 * Allows the IO of csv files with a standard saveAll() format data array. Does not save to db.
 *
 * @package default
 * @author Dean Sofer
 * @version $Id$
 * @copyright 
 **/
class CsvBehavior extends ModelBehavior {

	/**
	 * Contains configuration settings for use with individual model objects.
	 * Individual model settings should be stored as an associative array, 
	 * keyed off of the model name.
	 *
	 * @var array
	 * @access public
	 * @see Model::$alias
	 */
	var $settings = array();

	/**
	 * Allows the mapping of preg-compatible regular expressions to public or
	 * private methods in this class, where the array key is a /-delimited regular
	 * expression, and the value is a class method.  Similar to the functionality of
	 * the findBy* / findAllBy* magic methods.
	 *
	 * @var array
	 * @access public
	 */
	var $defaults = array(
		'length' => 0,
		'delimiter' => ',',
		'enclosure' => '"',
		'escape' => '\\',
		'headers' => true,
		'text' => false,
	);


	/**
	 * Initiate Csv Behavior
	 *
	 * @param object $model
	 * @param array $config
	 * @return void
	 * @access public
	 */
	function setup(&$model, $config = array()) {
		$this->settings[$model->alias] = array_merge($this->defaults, $config);
	}

	/**
	 * Import function
	 *
	 * @param string $filename path to the file under webroot
	 * @return array of all data from the csv file in [Model][field] format
	 * @author Dean Sofer
	 */
	function importCsv(&$model, $content, $fields = array(), $options = array()) {
		$options = array_merge($this->defaults, $options);
		
		if (!$this->_trigger($model, 'beforeImportCsv', array($content, $fields, $options))) {
			return false;
		}
		
		if ($options['text']) {
			// store the content to a file and reset
			$file = fopen("php://memory", "rw");
			fwrite($file, $content);
			fseek($file, 0);
		} else {
			$file = fopen($content, 'r');
		}
		
		// open the file
		if ($file) {
			
			$data = array();
			
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
						$data[$r][$model->alias][$field] = $row[$f];
					}
				}
				$r++;
			}
			return $data;

			// close the file
			fclose($file);
			
			$this->_trigger($model, 'afterImportCsv', array($data));

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
	public function exportCsv(&$model, $filename, $data, $options = array()) {
		$options = array_merge($this->defaults, $options);
		
		if (!$this->_trigger($model, 'beforeExportCsv', array($filename, $data, $options))) {
			return false;
		}
		
		// open the file
		if ($file = fopen($filename, 'w')) {
			
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
			
			$this->_trigger($model, 'afterExportCsv', array());

			return $r;
		} else {
			return false;
		}
	}
	
	protected function _trigger(&$model, $callback, $parameters) {
		if (method_exists($model, $callback)) {
			return call_user_func_array(array($model, $callback), $parameters);
		} else {
			return true;
		}
	}

}