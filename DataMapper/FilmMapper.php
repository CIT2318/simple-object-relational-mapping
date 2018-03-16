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

	private function makeFilmObject($row)
	{
		$filmObject = new Film($row["title"], $row["year"], $row["duration"]);
		$filmObject->id=$row["id"];
		return $filmObject;
	}
}
