<?php

namespace Entity;
use Repository;

class Produit extends Entity
{
    private $date_arrivee;
    private $date_depart;
    private $prix;
    private $etat;
    private $id;
    private $sesDetail;
    private $salle_id;
    private $promotion_id;
    private $promotion;
    private $salle;

       public function __construct($properties = null){
        if(isset($properties) && is_array($properties)){
            $this->hydrate($properties);
        }
        $this->recordableAttributes =  array( 
             'EntitySelect' => 'salle_id',
             'date_arrivee',
             'date_depart',
             'prix',
             'etat',
             'promotion_id',
        );       
        $this->requiredAttributes =  array( 
             'salle_id',
             'date_arrivee',
             'date_depart',
             'prix',
             'etat',
             'promotion_id',
        );
        $this->uniqueAttributes =  array( 
  
        );
    }


    public function setDateArrivee($dateArrivee){
        $this->date_arrivee = $dateArrivee;
        return $this;
    }

    public function getDateArrivee(){
        return $this->date_arrivee;
    }

    public function setDate_arrivee($dateArrivee){
        $this->date_arrivee = $dateArrivee;
        return $this;
    }

    public function getDate_arrivee(){
        return $this->date_arrivee;
    }

    public function getDateArriveeFr(){
        setlocale (LC_ALL, "fr_FR"); 
        $phpdate = strtotime( $this->date_arrivee );
        return strftime("%d %B %Y",$phpdate ); 
    }

    public function setDateDepart($dateDepart){
        $this->date_depart = $dateDepart;
        return $this;
    }
    public function setDate_depart($dateDepart){
        $this->date_depart = $dateDepart;
        return $this;
    }
    




    public function getDateDepart(){
        return $this->date_depart;
    }       
    public function getDate_depart(){
        return $this->date_depart;
    }    
    public function getDateDepartFr(){
        setlocale (LC_ALL, "fr_FR"); 
        $phpdate = strtotime( $this->date_depart );
        return strftime("%d %B %Y",$phpdate ); 
    }
    public function getDateDepartFormat(){ 
        $phpdate = strtotime( $this->date_depart );
        return date("d/m/Y",$phpdate ); 
    }

    public function getDateArriveeFormat(){ 
        $phpdate = strtotime( $this->date_arrivee );
        return date("d/m/Y",$phpdate ); 
    }




    public function setPrix($prix){
        $this->prix = $prix;
        return $this;
    }
    
    public function getPrix(){
        return $this->prix;
    }

    public function setEtat($etat){
        if(empty($etat)) $etat = '0';
        $this->etat = $etat;
        return $this;
    }
    
    public function getEtat(){
        if(empty($this->etat)) $this->etat = '00';
        return $this->etat;
    }
    
    public function getId(){
        return $this->id;
    }

    public function addSesDetail(){
        $this->sesDetail[] = $sesDetail;
        return $this;
    }

    public function removeSesDetail(){
        $this->sesDetail->removeElement($sesDetail);
    }

    public function getSesDetail(){
        return $this->sesDetail;
    }

    public function setSalle($salle){
        $this->salle_id = $salle;
        return $this;
    }

    public function getSalle(){
        return $this->salle_id;
    }

    public function setSalle_id($salle){
        $this->salle_id = $salle;
        return $this;
    }

    public function getSalle_id(){
        return $this->salle_id;
    }

    public function setPromotionId($promotion){
        $this->promotion_id = $promotion;
        return $this;
    }

    public function getPromotionId(){
        return $this->promotion_id;
    }
    public function setPromotion_id($promotion){
        $this->promotion_id = $promotion;
        return $this;
    }

    public function getPromotion_id(){
        return $this->promotion_id;
    }

    public function getSalleEntity(){
        $roomId     = $this->getSalle();
        $Srepository= new Repository\SalleRepository;
        $room       = $Srepository->find($roomId);
        if(is_object($room)){
            return $room;
        }
        return false;
    }
    public function getPromotionEntity(){
        $id     = $this->getPromotionId();
        $Prepository= new Repository\PromotionRepository;
        $promo       = $Prepository->find($id);
        if(is_object($promo)){
            return $promo;
        }
        return false;
    }

    public function isDeletable(){
        $return = false;    
        $Srepository = new Repository\ProduitRepository;
        if ($Srepository->isDeletable($this)) { 
            $return = $Srepository->delete($this);
        }
        return $return;
    }

    public function validations(){
        $Repository = new Repository\ProduitRepository;
        $return  = array();
        if($this->date_arrivee  < date("Y-m-d H:i:s"))
             $return['Date arrivée'] = 'la date de d\'arrivée doit être supérieure à aujourd\'hui.';            
        if($this->date_arrivee  > $this->date_depart) 
            $return['Date arrivée'] = 'la date de d\'arrivée doit être inférieure à la date de départ.';
        if(!$Repository->isdateAvailable($this)) 
            $return['Dates'] = 'ces dates sont incompatibles avec un produit basé sur la même salle.';
        if(!ctype_digit($this->prix)) 
            $return['Prix'] = 'le prix doit être un nombre entier.';
        if( !ctype_digit($this->etat) && $this->etat > 1 )
            $return['Etat'] = 'L\'etat doit être à zero ou 1';
        if( !ctype_digit($this->salle_id)  )
            $return['Salle'] = 'Erreur sur le choix de salle';
        if( !ctype_digit($this->promotion_id)  )
            $return['Promotion'] = 'Erreur sur le choix de promotion';
        return $return;
    }  

}
