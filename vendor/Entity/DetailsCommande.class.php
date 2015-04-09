<?php

namespace Entity;

class DetailsCommande extends Entity
{
    private $id;
    private $produit_id;
    private $commande_id;

     public function __construct($properties = NULL){
        if(isset($properties) && is_array($properties)){
            $this->hydrate($properties);
        }
        $this->recordableAttributes =  array( 
            'produit_id',
            'commande_id',
        );       
        $this->requiredAttributes =  array( 
            'produit_id',
            'commande_id',
        );
        $this->uniqueAttributes =  array( 
 
        );
    }
   
    public function getId()
    {
        return $this->id;
    }

    public function setProduit_id($produit)
    {
        $this->produit_id = $produit;
        return $this;
    }

    public function getProduit_id()
    {
        return $this->produit_id;
    }

    public function setCommande_id($commande)
    {
        $this->commande_id = $commande;
        return $this;
    }

    public function getCommande_id()
    {
        return $this->commande_id;
    }



}
