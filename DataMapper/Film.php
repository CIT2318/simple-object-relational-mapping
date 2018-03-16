<?php
class Film {
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
}


?>