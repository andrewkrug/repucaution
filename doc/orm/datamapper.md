##ORM
___
###About:
The ORM based on [WanWizard DataMapper](http://datamapper.wanwizard.eu/). Please read their docs.

This document describes some extensions of DataMapper.
___

####Create:
You can create an object via static method ``create``

Example:

````
$newUser = User::create(); // $newUser contains User Object

$someModel = SomeModel::create(); // $someModel contains SomeModel Object
````


####findAll:
You can get all rows from DB via static method ``findAll``

Example:

````
$users = User::findAll(); // $users contains all users stored in DB
````

####findBy:
You can get all rows from DB by one field via static method ``findBy``

Example:

````
//You need to find all users by company "ikantam"
$users = User::findByCompany('ikantam'); 

//You need to find all users by first_name
$users = User::findByFirstName('Alex'); 
````

####findOneBy:
You can get one row from DB by one field via static method ``findOneBy``

It works same as ``findBy`` method, but it return only one object instead of collection of objects.

Example:

````
//You need to find only one user by email
$users = User::findOneByEmail('test@test.com'); 
````