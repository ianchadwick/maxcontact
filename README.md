# MaxContact PHP API

... well csv file upload!

## Requirements 

PHP 5.4+

## Installation using Composer

Add ianchadwick/maxcontact to the require part of your composer.json file

```js
"require": {
  "ianchadwick/maxcontact": "dev-master"
}
```

Then update your project with composer

```
composer update
```

CSV file upload to mapping

```php
use MaxContact\Client;
use MaxContact\Commands\ImportCsvFiles;

# Create a new client, the url is unique to each account so you'll want to speak to MaxContact support to get yours.
# They will also be able to provide you with the username and password for use with the API.
$client = new Client('username', 'password', 'https://myusernameapi.maxcontact.com/myusernameapi');

# Create some kind of file upload
# List ID and mapping name will be provided by support again.
# The mapping that is requested will determine the key names below.
$command = new ImportJsonFiles(1, 'LiveFeedMap', [
    [
        'firstName' => 'Ian',
        'phone' => '012345678765',
        # ... any additional feeds that are mapped
    ]
]);

# upload the file and return a true on success
$client->execute($command);

# To get the response from the last request
var_dump($client->getLastResponse()->getBody()->getContents());

# Or get the XML
var_dump($client->getLastResponse()->xml());
```