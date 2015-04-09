<?php
namespace Repository;
use Manager\EntityRepository;
use Entity;

class SalleRepository extends EntityRepository {
	
	public function findAllSalle(){
		return $this->findAll();
	}
	public function FindSalle($id){
		return $this->find($id);
	}	
	public function getNoteAverage($id){
		$id = (int) $id ;
		$req = "SELECT AVG(note) FROM avis WHERE salle_id=$id";
		$q = $this->getDb()->query($req);
		if(!$q) return false;
		else return $q->fetch();
	}
	public function getNotes($id){
		$id = (int) $id ;
		$req = "SELECT note FROM avis WHERE salle_id=$id";
		$q = $this->getDb()->query($req);
		if(!$q) return false;
		else return $q->fetchAll();
	}
	public function getAvis($id){
		$id = (int) $id ;
		$req = "SELECT * FROM avis WHERE salle_id=$id";
		$q = $this->getDb()->query($req);
		if(!$q) return false;
		$q->setFetchMode(\PDO::FETCH_CLASS, 'Entity\\Avis');
		$r = $q->fetchAll();
		return $r;
	}
	public function getTopNotes($limit = 5){
		$limit = "LIMIT 5" ;//. (int) $limit ;
		$req = "SELECT s.*, AVG(a.note) AS note FROM avis a, salle s WHERE s.id = a.salle_id ORDER BY a.note DESC $limit";
		$q = $this->getDb()->query($req);
		if(!$q) return false;
		$q->setFetchMode(\PDO::FETCH_CLASS, 'Entity\\Salle');
		$r = $q->fetchAll(); 
		return $r;
	}
	public function getTopSold($limit = 5){
		$limit = "LIMIT 5" ;
		$req = "
		 SELECT s.*
		 FROM salle s 
		 INNER JOIN produit p
		 	ON s.id = p.salle_id
		 INNER JOIN detailscommande d
		 	ON p.id = d.produit_id
		 GROUP BY s.id
		 ORDER BY count(s.id) 
		 DESC $limit
		 ";

		$q = $this->getDb()->query($req);
		if(!$q) return false;
		$q->setFetchMode(\PDO::FETCH_CLASS, 'Entity\\Salle');
		$r = $q->fetchAll(); 
		return $r;
	}
	public function isDeletable($room){
		if(is_numeric($room)){
			$id = $room;
		}
		elseif ($room instanceof Entity\Salle) {
			$id = $room->getId();
		}
		else{
			return false;
		}
		
		$req = "
			 SELECT s.*
			 FROM salle s 
			 INNER JOIN produit p
			 	ON s.id = p.salle_id
			 INNER JOIN detailscommande d
			 	ON p.id = d.produit_id
			 WHERE p.salle_id = $id
		 ";
		$q = $this->getDb()->query($req);
		if(!$q) return false;
		$q->setFetchMode(\PDO::FETCH_CLASS, 'Entity\\Salle');
		$r = $q->fetchAll(); 

		$return = (count($r) == 0)? true : false;
		return $return;
	}
}