<?php
namespace Repository;
use Manager\EntityRepository;
use Lib;
use Entity;

class ProduitRepository extends EntityRepository {
	
	public function findAllAvailable($limit = NULL){
		if ($limit != NULL){
			$limit = ' LIMIT '. (int) $limit;
		}
		$req = "
			SELECT * 
			FROM produit 
			WHERE date_arrivee > NOW() 
			AND date_arrivee < date_depart
			AND etat = 0
			$limit
			";
		$q = $this->getDb()->query($req);
		if(!$q) {	
			return false;
		}
		$q->setFetchMode(\PDO::FETCH_CLASS, 'Entity\\Produit' );
		$r = $q->fetchAll();
		return $r;		
	}

	public function findAvailable($id){
		$id = (int) $id ;
		$req = 'SELECT * FROM ' . $this->getTableName() . " WHERE id=$id AND etat=0";
		$q = $this->getDb()->query($req);
		if(!$q) return false;
		$q->setFetchMode(\PDO::FETCH_CLASS, 'Entity\\'.  $this->getTableName() );
		$r = $q->fetch();
		return $r;
	}

	public function findSearch($date = null, $keywords = null){
		$session = Lib\App::getSession();
		if(!is_null($date)) {
			if(is_int($date)) {
				$date = date('Y-m-d G:i:s', $date);
			}
			if(is_string($date)) {
				$date = date('Y-m-d G:i:s', strtotime($date));	
			}
			if(is_object($date)) {
				$date = date('Y-m-d G:i:s', $date->getTimestamp());
			}
		}
		else $date = 'NOW()';
		if ($keywords === null){
			$req = "
			SELECT * FROM produit 
			WHERE date_arrivee > NOW() 
			AND date_arrivee < date_depart
			AND date_arrivee > '$date'
			AND etat = 0
			";
		}
		else{
			if(is_string($keywords)){ 
				$keywords = preg_replace('/[^A-Za-z0-9\-]/', '', $keywords);
				$keywords = explode(' ', $keywords);
			}
			else $keywords = '*';
			if(is_array($keywords)){
				foreach ($keywords as $key => $value) {
					$value = preg_replace('/[^A-Za-z0-9\-]/', '', $value);
					$keywords[$key] = '.*' . $value . '.*';
				}
				$keywords = implode('|', $keywords);
			}
			$req = "
				SELECT * FROM produit p 
				INNER JOIN salle s 
	    			ON s.id = p.salle_id 
				WHERE p.date_arrivee > NOW() 
				AND p.date_arrivee < p.date_depart
				AND p.date_arrivee > '$date'
				AND etat = 0
				AND CONCAT(s.description, s.ville, s.pays, s.titre) REGEXP '($keywords)' 
				";
		}
		$q = $this->getDb()->query($req);
		if(!$q) {	
			return false;
		}
		$q->setFetchMode(\PDO::FETCH_CLASS, 'Entity\\Produit' );
		$r = $q->fetchAll();
		return $r;	
	}


	public function findRelated(Entity\Produit $product, $limit = null){
		$id = $product->getId();
		$city = $product
			->getSalleEntity()
			->getVille();
		if(empty($id)) $city = 'null';
		if(empty($city)) $city = '*';
		if ($limit != NULL){
			$limit = ' LIMIT '. (int) $limit;
		}
		$req = "
			SELECT * FROM produit 
			WHERE date_arrivee > NOW() 
			AND date_arrivee < date_depart
			AND etat = 0
			AND id != $id
			$limit
			";
		$q = $this->getDb()->query($req);
		if(!$q) {	
			return false;
		}
		$q->setFetchMode(\PDO::FETCH_CLASS, 'Entity\\Produit' );
		$r = $q->fetchAll();
		return $r;		
	}

	public function deleteAsocciatedProducts($roomId){
		if(is_numeric($roomId)){
			$req = "DELETE FROM produit WHERE salle_id=$roomId";
			$q = $this->getDb()->query($req);	
		}
		if ($q) return true;
		else return false;
	}
	public function isDeletable($product){
		if(is_numeric($product)){
			$id = $product;
		}
		elseif ($product instanceof Entity\Produit) {
			$id = $product->getId();
		}
		else{
			return false;
		}
		
		$req = "
			 SELECT *
			 FROM detailscommande
			 WHERE produit_id = $id
		 ";
		$q = $this->getDb()->query($req);
		if(!$q) return false;
		$q->setFetchMode(\PDO::FETCH_NUM);
		$r = $q->fetchAll(); 

		$return = (count($r) == 0)? true : false;
		return $return;
	}
	public function isdateAvailable($product){
		$arrivee = $product->getDateArrivee();
		$depart = $product->getDateDepart();
		$salle = $product->getSalle();
		$req = "
			 SELECT *
			 FROM produit
			 WHERE (
			 	'$arrivee' < date_depart 
			 	AND '$arrivee' > date_arrivee 
			 	AND salle_id = $salle )
			 OR (
			 	'$depart' < date_depart 
			 	AND '$depart' > date_arrivee 
			 	AND salle_id = $salle )
		 ";
		$q = $this->getDb()->query($req);
		if(!$q) return false;
		$q->setFetchMode(\PDO::FETCH_NUM);
		$r = $q->fetchAll(); 

		$return = (count($r) == 0)? true : false;
		return $return;
	}
	
	
}