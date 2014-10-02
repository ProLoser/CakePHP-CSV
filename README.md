# CSV Plugin

Allows the importing and exporting of a standard $this->data formatted array to and from csv files.
Doesn't currently support HABTM. I may remove the component now that I created the behavior instead.

## Options

Importing, exporting and setup come with the same options and default values

```php
$options = array(
	// Refer to php.net fgetcsv for more information
	'length' => 0,
	'delimiter' => ',',
	'enclosure' => '"',
	'escape' => '\\',
	// Generates a Model.field headings row from the csv file
	'headers' => true, 
	// If true, String $content is the data, not a path to the file
	'text' => false,
)
```

## Component Instructions

* Add Component to controller

```php
public $components = array('Csv.Csv' => $options);
```

### Importing

* Upload a csv file to the server

* Import the csv file into your data variable:

**Approach 1:** Use a CSV file with the first row being Model.field headers

```php
Posts.csv
Post.title, Post.created, Post.modified, body, user_id, Section.name, Category.0.name, Category.0.description, Category.1.name, Category.1.description
..., ..., ...
```

```php
$this->data = $this->Csv->import($content, $options);
```

**Approach 2:** Pass an array of fields (in order) to the method

```php
$this->data = $this->Csv->import($content, array('Post.title', 'Post.created', 'Post.modified', 'body', 'user_id', 'Category.0.name', 'Category.0.description', 'Category.1.name', 'Category.1.description'));
```

* Process/save/whatever with the data

```php
$this->Post->saveAll($this->data);
```

### Exporting

* Populate an $this->data type array

```php
$data = $this->Post->find('all', array('recursive' => 0));
```

* Export to a file in a writeable directory

```php
$this->Csv->exportCsv($filepath, $data, $options);
```

## Behavior Instructions

The instructions are identical to the component, except for a few method name changes and additional callbacks

* Add Behavior to the model

```php
public $actsAs = array('Csv.Csv' => $options);
```

* Upload a csv file to the server

* Follow instruction for the component, Import using `$this->importCsv()` and export with `$this->exportCsv()`

### Additional optional callbacks:

* `beforeImportCsv($filename, $fields, $options)` returns boolean $continue
* `afterImportCsv($data)`
* `beforeExportCsv($filename, $data, $options)` returns boolean $continue
* `afterExportCsv()`
