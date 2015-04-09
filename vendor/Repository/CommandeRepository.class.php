<?php
namespace Repository;
use Manager\EntityRepository;
use Lib;
use Entity;

class CommandeRepository extends EntityRepository {

	public function	userBookings($user){
		$userId = $user->getId();
		$req = "SELECT * FROM commande WHERE membre_id='$userId'";
		$q = $this->getDb()->query($req);
		if(!$q) {	
			return false;
		}
		$q->setFetchMode(\PDO::FETCH_CLASS, 'Entity\\Commande' );
		$r = $q->fetchAll();
		return $r;		
	

	}

}