<?php

namespace Entity;

class Promotion extends Entity
{
  
    private $code_promo;
    private $reduction;
    private $id;

    public function __construct($properties = null){
        if(isset($properties) && is_array($properties)){
            $this->hydrate($properties);
        }
        $this->recordableAttributes =  array( 
            'code_promo',
            'reduction',
        );       
        $this->requiredAttributes =  array( 
            'code_promo',
            'reduction',
        );
        $this->uniqueAttributes =  array( 
            'code_promo',
        );
    }

    public function setCodePromo($codePromo)
    {
        $this->code_promo = $codePromo;
        return $this;
    }

    public function getCodePromo()
    {
        return $this->code_promo;
    }

    public function getCode_promo()
    {
        return $this->code_promo;
    }

    public function setReduction($reduction)
    {
        $this->reduction = $reduction;
        return $this;
    }

    public function getReduction()
    {
        return $this->reduction;
    }

    public function getId()
    {
        return $this->id;
    }

    public function validations(){
        $return  = array();
        if(strlen($this->code_promo) < 3) 
            $return['Code promo'] = 'Le codoit contenir au moins 3 caractères.';
        if((!ctype_digit($this->reduction)) )
            $return['note'] = 'La réduction doit être un nombre entier.';
        return $return;
    }  
    
}
