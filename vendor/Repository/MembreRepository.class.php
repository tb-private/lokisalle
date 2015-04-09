<?php
namespace Repository;
use Manager\EntityRepository;

class MembreRepository extends EntityRepository {
	
	public function findAllMembre(){
		return $this->findAll();
	}
	public function FindMembre($id){
		return $this->find($id);
	}
	public function findByMail($mail){
		$mail = (string) $mail ;
		$req = "SELECT * FROM membre WHERE email='$mail'";
		$q = $this->getDb()->query($req);
		if(!$q) return false;
		$q->setFetchMode(\PDO::FETCH_CLASS, 'Entity\\Membre');
		$r = $q->fetch();
		return $r;
	}	
	
	public function tryConnexion($pseudo, $pass){
		$pseudo = htmlspecialchars($pseudo);
		$pass = htmlspecialchars($pass);
		if(!empty($pseudo) && !empty($pass)) {
			$req = "SELECT * FROM membre WHERE pseudo='$pseudo' AND mdp='$pass'";
			$q = $this->getDb()->query($req);
			if(!$q) {	
				return false;
			}
			$q->setFetchMode(\PDO::FETCH_CLASS, 'Entity\\Membre' );
			$r = $q->fetch();
			return $r;
		}
	}

	public function getTopBookings($limit = 5){
		$limit = "LIMIT 5" ;//. (int) $limit ;
		$req = "
			SELECT m.*, COUNT(c.membre_id) AS bookings 
			FROM membre m, commande c 
			WHERE m.id = c.membre_id 
			ORDER BY COUNT(c.membre_id)
			DESC $limit
		";
		$q = $this->getDb()->query($req);
		if(!$q) return false;
		$q->setFetchMode(\PDO::FETCH_CLASS, 'Entity\\Membre');
		$r = $q->fetchAll(); 
		return $r;
	}
	public function getTopBuyers($limit = 5){
		$limit = "LIMIT 5" ;
		$req = "
			 SELECT m.*, SUM(c.montant) AS totalMontant
			 FROM membre m 
			 INNER JOIN commande c
			 	ON m.id = c.membre_id	
			 GROUP BY m.id
			 ORDER BY SUM(c.montant) 
			 DESC $limit
		 ";
		$q = $this->getDb()->query($req);
		if(!$q) return false;
		$q->setFetchMode(\PDO::FETCH_CLASS, 'Entity\\Membre');
		$r = $q->fetchAll(); 
		return $r;
	}
}