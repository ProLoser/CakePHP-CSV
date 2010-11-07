# CSV Plugin

Allows the importing and exporting of a standard $this->data formatted array to and from csv files.
Doesn't currently support HABTM.

## Instructions

1. Add Component to controller

<pre>var $components = array('Csv.Csv');</pre>

### Importing

2. Upload a csv file to the server

3. Import the csv file into your data variable

Approach 1: Use a CSV file with the first row being Model.field headers

<pre>Posts.csv
Post.title, Post.created, Post.modified, body, user_id, Section.name, Category.0.name, Category.0.description, Category.1.name, Category.1.description
..., ..., ...
</pre>

<pre>$this->data = $this->Csv->import($filepath);</pre>

Approach 2: Pass an array of fields (in order) to the method

<pre>$this->data = $this->Csv->import($filepath, array('Post.title', 'Post.created', 'Post.modified', 'body', 'user_id', 'Category.0.name', 'Category.0.description', 'Category.1.name', 'Category.1.description'));</pre>

4. Process/save/whatever with the data

<pre>$this->Post->saveAll($this->data);</pre>

### Exporting

2. Populate an $this->data type array

<pre>$data = $this->Post->find('all', array('recursive' => 0));</pre>

3. Export to a file in a writeable directory

<pre>$this->Csv->export($filepath, $data, array(
	// Default values
	'delimiter' => ',',
	'enclosure' => '"',
	// Generates a Model.field headings row
	'headers' => true, 
));</pre>