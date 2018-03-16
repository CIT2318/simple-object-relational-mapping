<?php
require_once("../dbconnect.php");
require_once("Film.php");


//get one film
$film=Film::find(10);
echo "<p>".$film->title." is ".$film->getAge()." years old</p>";
