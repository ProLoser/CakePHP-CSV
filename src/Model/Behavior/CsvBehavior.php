<?php
namespace CakePHPCSV\Model\Behavior;

use Cake\ORM\Behavior;
use Cake\ORM\Table;
use Cake\Utility\Hash;

/**
 * Csv behavior
 */
class CsvBehavior extends Behavior
{
    /**
     * Default configuration.
     *
     * @var array
     * @access protected
     */
    protected $_defaultConfig = [
        'length' => 0,
        'delimiter' => ',',
        'enclosure' => '"',
        'escape' => '\\',
        'headers' => true,
        'text' => false,
        'excel_bom' => false,
    ];

    /**
     * Import public function
     *
     * @param string $filename path to the file under webroot
     * @return array of all data from the csv file in [Model][field] format
     * @author Dean Sofer
     */
    public function importCsv($content, $fields = array(), $options = array())
    {
        $config = $this->config();
        $options = array_merge($config, $options);

        if (!$this->_trigger('beforeImportCsv', array($content, $fields, $options))) {
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
            $data = [];

            if (empty($fields)) {
                // read the 1st row as headings
                $fields = fgetcsv($file, $options['length'], $options['delimiter'], $options['enclosure']);
                foreach ( $fields as $key => $field ) {
                    $field = trim($field);
                    if ( empty($field) ) {
                        continue;
                    }
                    $fields[$key] = strtolower($field);
                }
            } elseif ( $options['headers'] ) {
                fgetcsv($file, $options['length'], $options['delimiter'], $options['enclosure']);
            }
            // Row counter
            $r = 0;
            // read each data row in the file
            $alias = $this->_table->alias();
            while ($row = fgetcsv($file, $options['length'], $options['delimiter'], $options['enclosure'])) {
                // for each header field
                foreach ($fields as $f => $field) {
                    if (!isset($row[$f])) {
                        $row[$f] = null;
                    }
                    // get the data field from Model.field
                    if (strpos($field,'.')) {
                        $keys = explode('.',$field);
                        if ( $keys[0] == $alias ) {
                            $field = $keys[1];
                        }
                        if ( !isset($data[$r]) ) {
                            $data[$r] = [];
                        }
                        $data[$r] = Hash::insert($data[$r], $field, $row[$f]);

                    } else {
                        $data[$r][$field] = $row[$f];
                    }

                }
                $r++;
            }

            // close the file
            fclose($file);

            $this->_trigger('afterImportCsv', array($data));

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
    public function exportCsv($filename, $data, $options = array())
    {
        $config = $this->config();
        $options = array_merge($config, $options);

        if (!$this->_trigger('beforeExportCsv', array($filename, $data, $options))) {
            return false;
        }

        // open the file
        if ($file = fopen($filename, 'w')) {
            // Add BOM for proper display UTF-8 in EXCEL
            if($options['excel_bom']) {
                fputs($file, chr(239) . chr(187) . chr(191));
            }
            // Iterate through and format data
            $firstRecord = true;
            foreach ($data as $record) {
                $record = $record->toArray();
                $row = array();
                foreach ($record as $field => $value) {
                    var_dump($value);
                    if ( !is_array($value) ) {
                        $row[] = $value;
                        if ($firstRecord) {
                            $headers[] = $field;
                        }
                        continue;
                    }
                    $table = $field;
                    $fields = $value;
                    // TODO add parsing for HABTM
                    foreach ($fields as $field => $value) {
                        if (!is_array($value)) {
                            if ($firstRecord) {
                                $headers[] = $table . '.' . $field;
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

            $this->_trigger('afterExportCsv', array());

            return $r;
        } else {
            return false;
        }
    }

    protected function _trigger($callback, $parameters) {
        if (method_exists($this->_table, $callback)) {
            return call_user_func_array(array($this->_table, $callback), $parameters);
        } else {
            return true;
        }
    }
}
