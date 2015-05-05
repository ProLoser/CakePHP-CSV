# CSV Plugin

Allows the importing and exporting of a standard $this->data formatted array to and from csv files.
Doesn't currently support HABTM.

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

## Instructions

* Add Behavior to the table

```php
<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\Table;

/**
 * Posts Model
 */
class PostsTable extends Table
{

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        //$options = ...
        $this->addBehavior('CakePHPCSV.Csv', $options);
    }
}
?>
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
$this->data = $this->Posts->import($content, $options);
```

**Approach 2:** Pass an array of fields (in order) to the method

```php
$data = $this->Posts->import($content, array('Post.title', 'Post.created', 'Post.modified', 'body', 'user_id', 'Category.0.name', 'Category.0.description', 'Category.1.name', 'Category.1.description'));
```

* Process/save/whatever with the data

```php
$entities = $this->Posts->newEntities($data);
$Table = $this->Posts;
$Table->connection()->transactional(function () use ($Table, $entities) {
    foreach ($entities as $entity) {
        $Table->save($entity, ['atomic' => false]);
    }
});
```

### Exporting

* Populate an $this->data type array

```php
$data = $this->Post->find()->all();
```

* Export to a file in a writeable directory

```php
$this->Posts->exportCsv($filepath, $data, $options);
```

### Additional optional callbacks:

* `beforeImportCsv($filename, $fields, $options)` returns boolean $continue
* `afterImportCsv($data)`
* `beforeExportCsv($filename, $data, $options)` returns boolean $continue
* `afterExportCsv()`
