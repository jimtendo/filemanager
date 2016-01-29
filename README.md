# JazzyCRUD (MySQL CRUD for Laravel 5)

## Installation

Add this library to your composer.json file: `"jimtendo/jazzycrud": "dev-master"`

Add Service Provider to `config/app.php`: `'Jimtendo\JazzyCRUD\ServiceProvider'`

Make sure the following libraries are included in your main layout file:

```javascript
<script type="text/javascript" language="javascript" src="//code.jquery.com/jquery-1.10.2.min.js"></script>
<link rel="stylesheet" type="text/css" href="//netdna.bootstrapcdn.com/bootstrap/3.0.3/css/bootstrap.min.css">
<link rel="stylesheet" type="text/css" href="//cdn.datatables.net/plug-ins/9dcbecd42ad/integration/bootstrap/3/dataTables.bootstrap.css">
<script type="text/javascript" language="javascript" src="//cdn.datatables.net/1.10.4/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" language="javascript" src="//cdn.datatables.net/plug-ins/9dcbecd42ad/integration/bootstrap/3/dataTables.bootstrap.js"></script>
```

## Usage

Use like follows:

```php
$crud = new \Jimtendo\JazzyCRUD\Basic;
$crud = $crud->from('feeds')
              ->lists(['id'=>'id', 'url'=>'url'])
              ->creates(['id'=>'id', 'url'=>'url'])
              ->shows(['id'=>'id', 'url'=>'url'])
              ->edits(['id'=>'id', 'url'=>'url'])
              ->titles(['id'=>'Id', 'url'=>'URL'])
              ->customize('url', function($value){ return $value . 'hello'; })
              ->render();

echo $crud;
```

### Future Design

This doesn't support `joins`. In future, should all columns be formatted thusly: `tableName['field']`?

Would we be able to add custom handlers if the `tableName` cannot be found by doing it this way? i.e. If we're querying an API?

Also, another thing that might be worth doing is making the 'actions' themselves hooks.

e.g. In constructor: `$this->handleAction('list', $this->performList)`

This would allow a user to over-ride any particular action.

Should field-types also be hooks? I.e. `$this->handleType('imageUpload', $this->handleImageUpload);`