<?php


namespace Entity;

class Newsletter extends Entity
{
    private $id;
    private $membre_id;
 
    public function __construct($properties = null){
        if(isset($properties) && is_array($properties)){
            $this->hydrate($properties);
        }
        $this->recordableAttributes =  array( 
        'membre_id'
        );       
        $this->requiredAttributes =  array( 
        'membre_id',
        );
        $this->uniqueAttributes =  array( 
        'membre_id',  
        );
    }

    public function getId(){
        return $this->id;
    }
    public function getMembre_id(){
        return $this->membre_id;
    }

    public function setMembre($user)
    {
        $this->membre_id = $user->getId();
        return $this;
    }

    public function setMembreId($id)
    {
        $this->membre_id = $id;
        return $this;
    }

    public function getMembreId()
    {
        return $this->membre_id;
    }
}
