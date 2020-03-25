# Object Relational Mapping (ORM)

Previously when working with a database using
 PDO, each row from the resultset is returned as an associative array e.g.

```php
$query = "SELECT * FROM films WHERE films.id=2";
$resultset = $conn->query($query);
$film=$resultset->fetch(); 
print_r($film);//['id'=> 2 'title' => 'Winter's Bone' 'year' => 2010 'duration' => 100] 
echo $film['title']; //Winter's Bone

```

Really we want use objects in our web applications i.e. we want each row from the resultset to be made into an object. Converting between rows in a database and objects in our PHP code is known as Object Relational Mapping (ORM).

Here's a simple Film class
```php
class Film {
    public $title;
    public $year;
    public $duration;
    
    function __construct($title, $year, $duration){
        $this->title=$title;
        $this->year=$year;
        $this->duration=$duration;
    }

    function getAge(){
        $todaysDate   = new DateTime('today');
        $currentYear = $todaysDate->format("Y");
        return $currentYear-$this->year;
    }
}
```
Here's some code that retrieves a row from a database table and uses this data to instantiate a new Film object. Each field from the database becomes a property of the object


```php
$stmt = $conn->prepare("SELECT * FROM films WHERE films.id = 2");
$stmt->execute();
$row = $stmt->fetch();
$filmObject = new Film($row["title"], $row["year"], $row["duration"]);
echo "<p>{$filmObject->title} is {$filmObject->getAge()} years old</p>"; //Winter's Bone is 9 years old

```

## Design Patterns for ORM
There are a number of commonly used design patterns for Object Relational Mapping

### Active Record
The active record pattern (http://www.martinfowler.com/eaaCatalog/activeRecord.html) makes domain classes responsible for working with the database. Here's what a Film class might look like:

```php
class Film{
    public $id;
    public $title;
    public $year;
    public $duration;

    function __construct($title, $year, $duration){
        $this->title=$title;
        $this->year=$year;
        $this->duration=$duration;
    }
    function getAge(){
        $todaysDate   = new DateTime('today');
        $currentYear = $todaysDate->format("Y");
        return $currentYear-$this->year;
    }
    function save(){
        $conn = DbConnect::getConnection();
        $query="INSERT INTO films (id, title, year, duration) VALUES (NULL, :title, :year, :duration)";
        $stmt=$conn->prepare($query);
        $stmt->bindValue(':title', $this->title);
        $stmt->bindValue(':year', $this->year);
        $stmt->bindValue(':duration', $this->duration);
        $stmt->execute();
        $this->id = $conn->lastInsertId();
    }
    static function find($id)
    {
        $conn = DbConnect::getConnection();
        $stmt = $conn->prepare("SELECT * FROM films WHERE films.id = :id");
        $stmt->bindValue(':id',$id);
        $stmt->execute();
        $row = $stmt->fetch();
        $filmObject = Film::makeFilmObject($row);
        return $filmObject;
    }
    private static function makeFilmObject($row)
    {
        $filmObject = new Film($row["title"], $row["year"], $row["duration"]);
        $filmObject->id=$row["id"];
        return $filmObject;
    }
}
```
Essentially we have added methods to handle CRUD operations for the object. Note that this isn't complete, the code is simplified and we would typically have other operations (updating, deleting etc). Now when we need to perform database related tasks we can do this via the Film class. e.g.
```php
$film = new Film("Arrival", "2016", 116);
$film->save(); //inserts a row into a database

$film=Film::find(3); //gets the film with ID of 3
echo "<p>{$film->title} is {$film->getAge()} years old</p>";

```

Note that the *find()* method is static. 

What is nice about the Active Record pattern is that all the complexity for working with the database is encapsulated in the domain class (in this example Film). It is quite intuitive to call methods such as *save()* and *update()* on the actual object we want to save or update, without having to involve any other objects or functions.

The Active Record pattern is commonly used in MVC frameworks e.g. Laravel, Ruby on Rails to provide ORM e.g. see https://laravel.com/docs/5.7/eloquent for examples that look similar to the above one. 

There are also disadvantages to the Active record pattern. It tends to make our domain classes large and overly complex. One of the key principles of OOP is the 'single responsibility principle'. Each class should be responsible for a single part of the application’s functionality
(https://en.wikipedia.org/wiki/Single_responsibility_principle). The Active Record pattern gives domain classes too much responsibility.

## The Data Mapper Pattern

An alternative design pattern for database interaction is the data mapper pattern:

*[A mapper ] moves data between objects and a database while keeping them independent of each other* (http://martinfowler.com/eaaCatalog/dataMapper.html) 

The mapper class is responsible for working with the database - insert, delete etc. Domain objects don’t need any knowledge of the database. Again here's a simple example:

```php
class FilmMapper{

    public function find($id){
        $conn = DbConnect::getConnection();
        $stmt = $conn->prepare("SELECT * FROM films WHERE films.id = :id");
        $stmt->bindValue(':id',$id);
        $stmt->execute();
        $row = $stmt->fetch();
        $filmObject=$this->makeFilmObject($row);
        return $filmObject;
    }

    public function save($film)
    {
        $conn = DbConnect::getConnection();
        $query="INSERT INTO films (id, title, year, duration) VALUES (NULL, :title, :year, :duration)";
        $stmt=$conn->prepare($query);
        $stmt->bindValue(':title', $film->title);
        $stmt->bindValue(':year', $film->year);
        $stmt->bindValue(':duration', $film->duration);
        $stmt->execute();
        $film->id = $conn->lastInsertId();
    }
    private function makeFilmObject($row)
    {
        $filmObject = new Film($row["title"], $row["year"], $row["duration"]);
        $filmObject->id=$row["id"];
        return $filmObject;
    }
}
```
Again, this is a simplified/incomplete example. We can use the FilmMapper e.g.

```php
$filmMapper = new FilmMapper();
//save a film
$film = new Film("Arrival", "2016", 116);
$filmMapper->save($film); //inserts a row into a database

$film = $filmMapper->find(8); //gets the film with id of 8
echo "<p>{$film->title} is {$film->getAge()} years old</p>";
```

The DataMapper pattern isn't quite as neat to use as the Active Record pattern, but it has the advantages that we keep the database related code separate. 

## Associations between classes
We know how to implement relationships between classes in a database i.e foreign keys, junction tables etc. How do we do this in PHP code? 

### One-to-many relationships
In our example, there is a one-to-many relationship between Certificate and Film. To implement this in PHP: 

First we need a Certificate class
```php
class Certificate {
    public $name;
    public $description;
    public $image;
    
    function __construct($name, $description, $image){
        $this->name=$name;
        $this->description=$description;
        $this->image=$image;
    }
}
```
We would then make certificate a property of Film e.g.

```php
class Film {
    public $title;
    public $year;
    public $duration;
    public $certificate; //new certificate property


    function __construct($title, $year, $duration, $certificate){
        $this->title=$title;
        $this->year=$year;
        $this->duration=$duration;
        $this->certificate=$certificate; 
    }

    function getAge(){
        $todaysDate   = new DateTime('today');
        $currentYear = $todaysDate->format("Y");
        return $currentYear-$this->year;
    }
    
}
```

When we create Film objects, first we need to create a Certificate object and then we need to specify the certificate as a property of the film e.g. 

```php
$cert = new Certificate("12a", "12a films contain...", "12a.png");
$film = new Film("Arrival", "2016", 116, $cert);
```

We can then use the certificate property like any other property of the Film object e.g.

```php
//Arrival has a certificate of 12a
echo "{$film->title} has a certificate of {$film->certificate->name}";

```

### Many-to-many relationships
Sticking with the film example there is a many-to-many relationship between Film and Genre i.e. a film belongs to many genres and a genre is associated with many different films. 

We need to associate a Film object with multiple Genre objects. We do this using an array. 

Here's a simple Genre class:
```php
class Genre {
    public $name;
    public $description;

    function __construct($name, $description){
        $this->name=$name;
        $this->description=$description;
    }
}
```

The Film class would now look like:

```php
class Film {
    public $title;
    public $year;
    public $duration;
    public $certificate; 
    public $genres;//new genres property


    function __construct($title, $year, $duration,$certificate){
        $this->title=$title;
        $this->year=$year;
        $this->duration=$duration;
        $this->certificate=$certificate; 
        $this->genres=[]; //declare $genres as an empty array
    }

    function getAge(){
        $todaysDate   = new DateTime('today');
        $currentYear = $todaysDate->format("Y");
        return $currentYear-$this->year;
    }
    function addGenre($genre){
        $this->genres[]=$genre; //push the new Genre into the array
    }
    function removeGenre($genre){
        $index = array_search($genre, $this->genres);
        if($index > -1){
            array_splice($this->genres, $index, 1); //remove the genre from the array
        }
    }
    
}
```
We can now associate genres with films 

```php


$cert = new Certificate("12a", "12a films contain...", "12a.png");
$film = new Film("Arrival", "2016", 116,$cert);

$genre1 = new Genre("Thriller","A Thriller is a story ..."); //create a genre
$genre2 = new Genre("Science Fiction","Science fiction..."); //create a genre
$film->addGenre($genre1); //assign the genre to the film
$film->addGenre($genre2); //assign the genre to the film

foreach($film->genres as $genre)
{
    echo "<p>{$genre->name}</p>";
}

```

### ORM with associations
The object relational mapping now becomes more complex. Here's an example of a FilmMapper class that associates certificates with films

```php
class FilmMapper{
    public function find($id){
        $conn = DbConnect::getConnection();
        $stmt = $conn->prepare("SELECT * FROM films WHERE films.id = :id");
        $stmt->bindValue(':id',$id);
        $stmt->execute();
        $row = $stmt->fetch();
        $filmObject=$this->makeObject($row);
        return $filmObject;
    }
    public function save($film)
    {
        $conn = DbConnect::getConnection();
        $query="INSERT INTO films (id, title, year, duration) VALUES (NULL, :title, :year, :duration, :certId)";
        $stmt=$conn->prepare($query);
        $stmt->bindValue(':title', $film->title);
        $stmt->bindValue(':year', $film->year);
        $stmt->bindValue(':duration', $film->duration);
        $stmt->bindValue(':certId', $film->certificate->id);
        return $stmt->execute();
    }
    public function makeObject($row)
    {
        //get the certificate
        $certMapper = new CertificateMapper();
        $certificateObject = $certMapper->find($row["certificate_id"]);
        
        //make a film object
        $filmObject = new Film($row["title"], $row["year"], $row["duration"], $certificate);
        return $filmObject;
    }
}
```
When retrieving data from the database we need to use a CertificateMapper to get hold of a certificate object. When saving the Film we use certificate id as a foreign key. 

### ORM is complex
As soon as we start thinking about associations ORM becomes complex. We need separate mappers for each domain class GenreMapper, CertificateMapper etc. The above only considers unidirectional relationships e.g. we haven't thought how Genre objects can store their associated film. 

It is usually easier to use an 'off the shelf' solution. There are PHP libraries that will handle Object Relational Mapping for us 
E.g. Doctrine http://www.doctrine-project.org/ . Most MVC frameworks provide ORM
E.g. in Laravel – Eloquent ORM https://laravel.com/docs/5.7/eloquent .

How having some hands-on experience of ORM mapping is useful as you can then grasp what libraries and frameowkrs are actually doing. 
