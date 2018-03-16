<?php
require_once("../dbconnect.php");
require_once("Film.php");
require_once("FilmMapper.php");

//get one film
// $filmMapper = new FilmMapper();
// $film = $filmMapper->find(5);
// echo "<p>".$film->title." is ".$film->getAge()." years old</p>";

//save a film
// $film = new Film("Arrival", "2016", 116);
// $filmMapper->save($film); //inserts a row into a database


//update a film
// $filmMapper = new FilmMapper();
// $film = $filmMapper->find(10);
// $film->year ="1900";
// $filmMapper->update($film);



//get all the films
// $films = $filmMapper->getAllFilms();
// foreach($films as $film){
// 	echo "<p>".$film->title." is ".$film->getAge()." years old</p>";
// }


//delete a film
// $film = $filmMapper->find(22);
// $filmMapper->delete($film);

