# Object Relational Mapping

This practical looks at simple versions of commonly used patterns for moving between objects and database tables

* Download the files in the repository. 
* Change the connection settings in *dbconnect.php* so they work with your database.

## Active Record
* From the ActiveRecord folder open *index.php*. Make sure it works. It should display the details for the film with an id of 10. 
* Have a good look through the code, try and understand what is happening. 
* On your own
    - In *index.php* add some code to test that the *save()* method works. If you do this successfully, you should get a new row in your films database table.
    - Add additional methods to the Film class (*update*, *delete* and *getAllFilms*)
    - Add code to *index.php* to test these work.

## Data Mapper
* From the DataMapper folder open *index.php*. Make sure it works. It should display the details for the film with an id of 10. 
* Have a good look through the code, try and understand what is happening. 
* On your own
    - In *index.php* add some code to test that the *save()* method works. If you do this successfully, you should get a new row in your films database table.
    - Add additional methods to the FilmMapper class (*update*, *delete* and *getAllFilms*)
    - Add code to *index.php* to test these work.