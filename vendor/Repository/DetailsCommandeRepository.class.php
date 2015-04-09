<?php
namespace Repository;
use Manager\EntityRepository;
use Lib;
use Entity;
use Repository;

class DetailsCommandeRepository extends EntityRepository {
			
	public function findProducts($bookingId){

		if(!empty($bookingId)) {
			$req = "SELECT * FROM detailscommande WHERE commande_id='$bookingId'";
			$q = $this->getDb()->query($req);
			if(!$q) {	
				return false;
			}
			$r = $q->fetchAll();
			$return ='';
			$Prepository = new Repository\ProduitRepository;
			if($r){
				$return = array();
				foreach ($r as $key => $value) {
					$product = $Prepository->find($value['produit_id']);
					$return[] = $product;
					//$title = $product->getSalle()->getTitre();
				}
			}

			return $return;
		}
	
	}
	public function findByCommand($bookingId){

		if(!empty($bookingId)) {
			$req = "SELECT * FROM detailscommande WHERE commande_id='$bookingId'";
			$q = $this->getDb()->query($req);
			if(!$q) {	
				return false;
			}
			$q->setFetchMode(\PDO::FETCH_CLASS, 'Entity\\DetailsCommande' );
			$r = $q->fetchAll();
			return $r;		
		}
	
	}

}