<?php

namespace Entity;
use Repository\SalleRepository;
use Repository\MembreRepository;

class Avis extends entity
{
    private $commentaire;
    private $note;
    private $date;
    private $id;
    private $salle;
    private $salle_id;
    private $membre;
    private $membre_id;

    public function __construct($properties = null){
        if(isset($properties) && is_array($properties)){
            $this->hydrate($properties);
        }
        $this->recordableAttributes =  array( 
            'EntitySelect' => 'membre_id',  
            'EntitySelect' => 'salle_id',     
            'textarea' => 'commentaire',     
            'note',  
            'date',   
        );       
        $this->requiredAttributes =  array( 
            'membre_id',  
            'salle_id',     
            'commentaire',     
            'note',  
            'date',     
        );
        $this->uniqueAttributes =  array( 
 
        );
    }

    public function setCommentaire($commentaire)
    {
        $this->commentaire = $commentaire;
        return $this;
    }

    public function getCommentaire()
    {
        return $this->commentaire;
    }

    public function setNote($note)
    {
        $this->note = (int) $note;
        return $this;
    }

    public function getNote()
    {
        return $this->note;
    }

    public function setDate($date)
    {
        $this->date = $date;
        return $this;
    }

    public function getDate()
    {
        return $this->date;
    }
    public function getDateFr()
    {
        setlocale (LC_ALL, "fr_FR"); 
        $phpdate = strtotime( $this->date);
        return strftime("%d %B %Y",$phpdate );     
    }

    public function getId()
    {
        return $this->id;
    }

    public function setSalle()
    {
        $this->salle = $salle;
        return $this;
    }

    public function getSalle()
    {
        if(empty($this->salle)){
            $salleRepository = new SalleRepository;
            $this->salle = $salleRepository->find($this->salle_id);
        }
        return $this->salle;
    }

    public function setMembre($membre)
    {
        $this->membre = $membre;
        return $this;
    }

    public function getMembre()
    {
        if(empty($this->membre)){
            $membreRepository = new membreRepository;
            $this->membre = $membreRepository->find($this->membre_id);
        }
        return $this->membre;
    }

    public function setMembreId($id)
    {
        $this->membre_id = (int) $id;
        return $this;
    }

    public function getMembreId()
    {
        return $this->membre_id;
    }

    public function setMembre_id($id)
    {
        $this->membre_id = (int) $id;
        return $this;
    }

    public function getMembre_id()
    {
        return $this->membre_id;
    }

    public function setSalle_id($id)
    {
        $this->salle_id = (int) $id;
        return $this;
    }

    public function getSalle_id()
    {
        return $this->salle_id;
    }

    public function setSalleId($id)
    {
        $this->salle_id = (int) $id;
        return $this;
    }

    public function getSalleId()
    {
        return $this->salle_id;
    }

  public function validations(){
    $return  = array();
    if(strlen($this->commentaire) < 10) 
        $return['commentaire'] = 'Le commentaire doit dépasser 10 caractères.';
    if((!is_int($this->note)) OR ($this->note > 10) OR ($this->note < 0))
        $return['note'] = 'La note doit être un chiffre entier entre 0 et 10.';
    return $return;
    }  
}
