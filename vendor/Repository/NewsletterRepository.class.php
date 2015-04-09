<?php
namespace Repository;
use Manager\EntityRepository;
use Entity;

class NewsletterRepository extends EntityRepository {
	
	public function getPopulation(){
		$req = "SELECT COUNT(id) FROM newsletter";
		$q = $this->getDb()->query($req);
		if(!$q) return false;
		else return $q->fetch();
	}
	public function getMails(){
		$req = "
				SELECT email FROM membre m 
				INNER JOIN newsletter n 
	    			ON n.membre_id = m.id 
				";
		$q = $this->getDb()->query($req);
		if(!$q) return false;
		else return $q->fetchAll();
	}
	public function getIds(){
		$req = " SELECT membre_id FROM newsletter";
		$q = $this->getDb()->query($req);
		if(!$q) return false;
		else return $q->fetchAll();
	}
	public function findMembre($membre){
		if(is_int($membre)){
			$id = $membre;
		}
		elseif($membre instanceof Entity\Membre){
			$id = $membre->getId();
		}
		else return false;
		$req = "SELECT * FROM newsletter WHERE membre_id=$id";
		$q = $this->getDb()->query($req);
		if(!$q) return false;
		else return $q->fetch();
	}

	public function unsubscribe($membre){
		if($membre instanceof Entity\Membre){
			$id = (int) $membre->getId();
			$req = "Delete FROM newsletter WHERE membre_id = $id";
			$q = $this->getDb()->query($req);
		}
		if ($q) return true;
		else return false;
	}
	
}