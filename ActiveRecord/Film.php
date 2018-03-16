<?php


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
	
	private static function makeFilmObject($row)
	{
		$filmObject = new Film($row["title"], $row["year"], $row["duration"]);
		$filmObject->id=$row["id"];
		return $filmObject;
	}

}

