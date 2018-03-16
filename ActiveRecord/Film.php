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
	
	function update(){
		$conn = DbConnect::getConnection();
		$query="UPDATE films SET title=:title, year=:year, duration=:duration WHERE id=:id";
		$stmt=$conn->prepare($query);
		$stmt->bindValue(':id', $this->id);
		$stmt->bindValue(':title', $this->title);
		$stmt->bindValue(':year', $this->year);
		$stmt->bindValue(':duration', $this->duration);
		$stmt->execute();
	}

	function delete(){
		$conn = DbConnect::getConnection();
		$stmt = $conn->prepare("DELETE FROM films WHERE films.id = :id");
		$stmt->bindValue(':id',$this->id);
		$stmt->execute();
	}


	static function find($id)
	{
		$conn = DbConnect::getConnection();
		$stmt = $conn->prepare("SELECT * FROM films WHERE films.id = :id");
		$stmt->bindValue(':id',$id);
		$stmt->execute();
		$row = $stmt->fetch();
		// $filmObject = new Film($row["title"], $row["year"], $row["duration"]);
		// $filmObject->id=$row["id"];
		$filmObject = Film::makeFilmObject($row);
		return $filmObject;
	}

	static function getAllFilms()
	{
		$conn = DbConnect::getConnection();
		$query = "SELECT * FROM films";
		$resultset = $conn->query($query);
		$films=[];
		while($row = $resultset->fetch()){
			// $filmObject = new Film($row["title"], $row["year"], $row["duration"]);
			// $filmObject->id=$row["id"];
			$filmObject = Film::makeFilmObject($row);
			$films[] = $filmObject;
		}
		return $films;
	}

	private static function makeFilmObject($row)
	{
		$filmObject = new Film($row["title"], $row["year"], $row["duration"]);
		$filmObject->id=$row["id"];
		return $filmObject;
	}

}

