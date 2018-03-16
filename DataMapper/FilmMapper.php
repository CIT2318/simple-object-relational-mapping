<?php
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
	function update($film){
		$conn = DbConnect::getConnection();
		$query="UPDATE films SET title=:title, year=:year, duration=:duration WHERE id=:id";
		$stmt=$conn->prepare($query);
		$stmt->bindValue(':id', $film->id);
		$stmt->bindValue(':title', $film->title);
		$stmt->bindValue(':year', $film->year);
		$stmt->bindValue(':duration', $film->duration);
		$stmt->execute();
	}

	function delete($film){
		$conn = DbConnect::getConnection();
		$stmt = $conn->prepare("DELETE FROM films WHERE films.id = :id");
		$stmt->bindValue(':id',$film->id);
		$stmt->execute();
	}

	function getAllFilms()
	{
		$conn = DbConnect::getConnection();
		$query = "SELECT * FROM films";
		$resultset = $conn->query($query);
		$films=[];
		while($row = $resultset->fetch()){
			$filmObject = $this->makeFilmObject($row);
			$films[] = $filmObject;
		}
		return $films;
	}

	private function makeFilmObject($row)
	{
		$filmObject = new Film($row["title"], $row["year"], $row["duration"]);
		$filmObject->id=$row["id"];
		return $filmObject;
	}
}
