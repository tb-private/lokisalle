<?php
namespace Manager;
use Manager\PDOManager;
use Entity;
use Lib;
//use PDO;

class EntityRepository{
	private $db;
	private static $lastId;

	public function getDb(){
		if(!$this->db){
			$obj = PDOManager::getInstance();
			$this->db = $obj->getPdo();
		}
		return $this->db;
	}
	
	public function getTableName(){	
		$return =  
			strtolower(			
				str_replace( 	
					array(
						'Repository\\', 'Repository'), 
						'', 
						get_called_class() // des classes qui demandent getTableName
					)
				);
		return $return;
	}
	public function getEntityClass($entity){	
		$return = str_replace( 	array('Entity\\', 'Entity'), '', get_class($entity)); 
		return $return;
	}
	
	public function find($id){
		$id = (int) $id ;
		$req = 'SELECT * FROM ' . $this->getTableName() . " WHERE id=$id";
		$q = $this->getDb()->query($req);
		if(!$q) return false;
		$q->setFetchMode(\PDO::FETCH_CLASS, 'Entity\\'.  $this->getTableName() );
		$r = $q->fetch();
		return $r;
	}
	
	public function findAll($limit = NULL){
		if ($limit != NULL){
			$limit = ' LIMIT '. (int) $limit;
		}
		$q = $this->getDb()->query('Select * FROM ' . $this->getTableName() . $limit);
		if(!$q) return false;
		$q->setFetchMode(\PDO::FETCH_CLASS, 'Entity\\'.  $this->getTableName() );
		$r = $q->fetchAll();
		return $r;
	}

	public function save($entity){
		if($entity instanceof Entity\Entity){
			$recordable = $entity->getRecordable();
			if(!empty($recordable)){
				$attributeList = array();
				$attributePrepare = array();
				$valueList = array();
				foreach ($entity->getRecordable() as $attribute => $value) {
					$attributeList[] = $attribute;
					$attributePrepare[] = ':'.$attribute;
					$valueList[] =  $value;
				}
				$attributeList = implode(', ', $attributeList);
				$attributePrepareString = implode(', ', $attributePrepare);
			}
			$table = $this->getTableName();
			$q = $this->getDb()->prepare("INSERT INTO $table ($attributeList) VALUES ($attributePrepareString)");
			$i = 0;
			foreach ($attributePrepare as $attribute) {
				$q->bindParam($attribute, $valueList[$i]);
				$i++;
			}
			$q->execute();
			self::$lastId = $this->getDb()->lastInsertId();
		}
		else {
			return false;
		}
		return true;		
	}
	public function update($entity){
		if($entity instanceof Entity\Entity){
			$id = $entity->getId();
			if(!isset($id)){
				return false;
			}
			$recordable = $entity->getRecordable();
			if(!empty($recordable)){
				$attributeList = array();
				$valueList = array();
				foreach ($entity->getRecordable() as $attribute => $value) {
					$attributeList[] = $attribute. ' = :'.$attribute;
					$bindList[] = ':'.$attribute;
					$valueList[] = $value;
				}
				$attributePrepareString = implode(', ', $attributeList);		
				$table = $this->getTableName();	
				$q = $this->getDb()->prepare("UPDATE $table SET $attributePrepareString WHERE id='$id'");
				$i = 0;
				foreach ($bindList as $attribute) {
					$q->bindParam($attribute, $valueList[$i]);
					$i++;
				}
				$q->execute();
			} 
			else {
				return false;}
		}
		else {
			return false;
		}
		return true;		
	}
	public function validate($entity, $update = false){
		if($entity instanceof Entity\Entity){
			$errors = array();
			if(is_callable(array($entity, 'validations'))){
				$errors = $entity->validations();
			}
			if(!$update && isset($entity->uniqueAttributes)){
				$class = $this->getEntityClass($entity);
				$class = strtolower($class);
				foreach ($entity->uniqueAttributes as $key) {
					$value = 'get' . ucfirst($key);
					$value = $entity->$value();
					$req = "SELECT $key FROM  $class WHERE $key='$value'";
					$q = $this->getDb()->query($req);

					if(strlen($q->fetchColumn()) > 0) {
						$errors[] = "$key déjà utilisé par un autre $class.";
					}
				}
			}
			if(count($errors) == 0){
				return true;
			}
			else{
				$session = Lib\App::getSession();
				foreach ($errors as $error) {
					if(is_string($error)){
						$session->addError($error);
					}
				}
			}
		}
		else return false;
	}
	public function delete($entity){
		if($entity instanceof Entity\Entity){
			$id = (int) $entity->getId();
			$req = "DELETE FROM " . $this->getTableName() ." WHERE id=$id";
			$q = $this->getDb()->query($req);	
		}
		if ($q) return true;
		else return false;
	}

	public static function getLastId(){
		return self::$lastId;
	}

}