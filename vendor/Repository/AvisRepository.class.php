<?php
namespace Repository;
use Manager\EntityRepository;

class AvisRepository extends EntityRepository {
	
	public function findAllSalle(){
		return $this->findAll();
	}
	public function FindSalle($id){
		return $this->find($id);
	}	
	public function hasComment($roomId, $userId){
		$roomId = (int) $roomId;
		$userId = (int) $userId;
		$req = "SELECT * FROM avis WHERE salle_id=$roomId AND membre_id=$userId";
		$q = $this->getDb()->query($req);
		if(!$q) return false;
		else return !empty($q->fetch());
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
		// pour eviter de creer un objet et d'utiliser les setters 
		// setFetchMode permet d'affecter les propriétés de l'objet créé
		// PDO::FETCH_CLASS renvoie un objet, du type Entity\… 
		// il faut absolument que les propriété de la class correspondante soient égales à indexs de la table choisie.
		$r = $q->fetchAll();
		return $r;
	}
}