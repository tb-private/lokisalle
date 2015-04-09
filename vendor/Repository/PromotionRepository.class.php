<?php
namespace Repository;
use Manager\EntityRepository;
use Lib;
use Entity;

class PromotionRepository extends EntityRepository {
	
	public function findAllPromotion(){
		return $this->findAll();
	}
	public function findPromotion($id){
		return $this->find($id);
	}

	public function findByCode($code){
		$code = htmlspecialchars((string)$code);
		$req = "SELECT * FROM promotion WHERE code_promo='$code'";
		$q = $this->getDb()->query($req);
		if(!$q) {	
			return false;
		}
		$q->setFetchMode(\PDO::FETCH_CLASS, 'Entity\\Promotion' );
		$r = $q->fetch();
		return $r;		
	}


	
	
}