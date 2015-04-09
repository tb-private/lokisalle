<?php

namespace Entity;
use Lib;
use Repository;

class Entity 
{
	public function hydrate($donnees)
	{
	    foreach ($donnees as $key => $value){
	    	$method = 'set'.str_replace(' ', '', ucwords(str_replace('_', ' ', $key)));

		    if (is_callable(array($this, $method))){
		        $this->$method($value);
		    }
	    }
	}

    public function save(){
    	$return = false;
    	if($this->isSavable()){
    		$entity = 'Repository\\' . $this->getEntityName() . 'Repository';
	        $repository = new $entity;
	        if ($repository->validate($this)) {
	        	$return = $repository->save($this);
	        }
	        else $return = false;	        
    	}
        return $return;
    }
    public function delete(){
    	$return = false;
		$entity = 'Repository\\' . $this->getEntityName() . 'Repository';
        $repository = new $entity;	        
        $return = $repository->delete($this); 
        return $return;
    }

	public function update(){
	    	$return = false;
	    	if($this->isSavable()){
	    		$entity = 'Repository\\' . $this->getEntityName() . 'Repository';
		        $repository = new $entity;
		        if ($repository->validate($this, true)) {
		        	$return = $repository->update($this);
		        }
		        else $return = false;	        
	    	}
	        return $return;
	    }



	private function isSavable(){
		$return = true;		
		$required = $this->getRequired();
		if(!empty($required)){
			foreach ($this->getRequired() as $attribute => $value) {
				$method = 'get'. ucfirst($attribute);
				$result = $this->$method();
				if(empty($result) && $result!== 0){
					$return = false;
					Lib\App::getSession()->addError("le champs '$attribute' est vide");
				}
			}
		}
		else $return = false;
		return $return;
	}	
	public function getRecordable(){      
        foreach ($this->recordableAttributes as $key) {
            $method = 'get' . ucfirst($key);
            $return[$key] = $this->$method();
        }
        return $return;
    }
    public function getRequired(){
        $return = array();
        foreach ($this->requiredAttributes as $key) {
            $method = 'get' . ucfirst($key);
            $return[$key] = $this->$method();
        }
        return $return;
    }
	public function getEntityName(){	
		$return = str_replace(array('Entity\\', 'Entity'), '', get_called_class());
		return $return;
	}
    
	

}