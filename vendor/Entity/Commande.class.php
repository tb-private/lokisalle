<?php

namespace Entity;
use Repository;

class Commande extends Entity
{

    private $montant;
    private $date;
    private $id;
    private $membre_id;
    private $membre;

    public function __construct($properties = NULL){
        if(isset($properties) && is_array($properties)){
            $this->hydrate($properties);
        }
        $this->recordableAttributes =  array( 
            'montant',
            'date',
            'membre_id',
        );       
        $this->requiredAttributes =  array( 
            'montant',
            'date',
            'membre_id',
        );
        $this->uniqueAttributes =  array( 
 
        );
    }
   
    public function setMontant($montant){
        $this->montant = $montant;
        return $this;
    }

    public function getMontant(){
        return $this->montant;
    }

    public function setDate($date){
        $this->date = $date;
        return $this;
    }

    public function getDate(){
        return $this->date;
    }


    public function getDateFr(){
        setlocale (LC_ALL, "fr_FR"); 
        $phpdate = strtotime( $this->date);
        return strftime("%d %B %Y",$phpdate ); 
    }

    public function getId(){
        return $this->id;
    }

    public function setMembre_id($id){
        $this->membre_id = $id;
        return $this;
    }

    public function getMembre_id(){
        return $this->membre_id;
    }

    public function getProductsEntity(){
        $Drepository= new Repository\DetailsCommandeRepository;
        $products       = $Drepository->findProducts($this->id);
        if(is_array(($products))) {
            return $products;
        }
        return false;
    }

  public function validations(){
    $return  = array();
    if(!is_numeric($this->membre_id) OR $this->membre_id < 1)
        $return['membre'] = 'Aucun membre associé à la commande';
    if(!is_numeric($this->montant))
        $return['montant'] = 'Le montant a une valeur incorrecte';
    return $return;
    }  
}
