<?php
require_once("../dbconnect.php");
require_once("Film.php");
require_once("FilmMapper.php");

//get one film
$filmMapper = new FilmMapper();
$film = $filmMapper->find(5);
echo "<p>".$film->title." is ".$film->getAge()." years old</p>";

