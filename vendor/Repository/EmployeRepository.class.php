<?php
namespace Repository;
use Manager\EntityRepository;

class EmployeRepository extends EntityRepository {
	
	public function getAllEmploye(){
		return $this->findAll();
	}
	public function getFindEmploye($id){
		return $this->find($id);
	}
	
}