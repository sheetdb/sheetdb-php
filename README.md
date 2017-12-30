# PHP SheetDB Class

### Installation

The SheetDB PHP Library is available through [Composer](https://getcomposer.org/)

```
composer require sheetdb/sheetdb-php
```

### Test spreadsheet

At any time you can play with our test API here: https://sheetdb.io/api/v1/58f61be4dda40

You can also go to Google Sheets and play with it: https://docs.google.com/spreadsheets/u/2/d/1mrsgBk4IAdSs8Ask5H1z3bWYDlPTKplDIU_FzyktrGk/edit **The spreadsheet resets every hour.**

### Basic usage

In order to instantiate SheetDB connection you need to give an api id. You can find it in [SheetDB Dashboard](https://sheetdb.io).

```php
require('vendor/autoload.php');
use SheetDB\SheetDB;

$sheetdb = new SheetDB('58f61be4dda40');
$response = $sheetdb->get(); // returns all spreadsheets data
$response = $sheetdb->keys(); // returns all spreadsheets key names
$response = $sheetdb->name(); // returns name of a spreadsheet document
```

### Searching

You can use search method to find only specific rows. You have 2 options. Search rows that meets all of the conditions or search rows that meets at least one of the conditions.

You can use second parameter if you want the search to be case sensitive (it is boolean). By default it is not case sensitive.

```php
$response = $sheetdb->search(['name'=>'Steve','age'=>'22']); // returns when name="Steve" AND age=22
$response = $sheetdb->searchOr(['name'=>'Steve','age'=>'19']); // returns when name="Steve" OR age=19

$response = $sheetdb->search(['name'=>'Steve'], true); // returns when name="Steve", this query is case sensitive
```

### Creating a row(s)

If you want to create a new row you'll need to pass an array with key_name => value. If you want to create multiple rows that should be an array of arrays.

```php
$sheetdb->create(['name'=>'Mark','age'=>'35']);
$sheetdb->create([
	['name'=>'Chris','age'=>'34'],
	['name'=>'Amanda','age'=>'29'],
]);
```

### Updating a row(s)

First two parameters are key and value that need to match, third argument is array similar to the create one.

Fourth parameter is optional. If it's true not specified values will be emptied.

```php
$sheetdb->update('name','Chris',['age'=>'33']); // it will change only age
$sheetdb->update('name','Chris',['age'=>'33'],true); // it will update age to 33 but all other values will be emptied, in this case a name
```

### Delete a row(s)

Just like in update first two parameters are key and value. Every row that will match query will be deleted.

```php
$sheetdb->delete('name','Chris'); // will delete all rows with name = "Chris"
```
