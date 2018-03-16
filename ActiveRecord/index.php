<?php
require_once("dbconnect.php");
require_once("Film.php");


//get one film
// $film=Film::find(10);
// echo "<p>".$film->title." is ".$film->getAge()." years old</p>";

//save
// $film = new Film("Arrival", "2016", 116);
// $film->save(); 

// //get all the films
// $films = Film::getAllFilms();
// foreach($films as $film){
// 	echo "<p>".$film->title." is ".$film->getAge()." years old</p>";
// }


// //update
// $film=Film::find(1); 
// $film->year="2010";
// $film->update();
// echo "<p>".$film->title." is ".$film->getAge()." years old</p>";

// //delete
// $film=Film::find(12); 
// $film->delete();