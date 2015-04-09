<?php

namespace Entity;
use Repository;

class Salle extends Entity
{
    private $pays;
    private $ville;
    private $adresse;
    private $cp;
    private $titre;
    private $description;
    private $photo;
    private $capacite;
    private $categorie;
    private $id;
    private $note;

    public function __construct($properties = null){
        if(isset($properties) && is_array($properties)){
            $this->hydrate($properties);
        }
        $this->recordableAttributes =  array( 
        'pays',
        'ville',
        'adresse',
        'cp',
        'titre',
        'Textarea' => 'description',
        'File' => 'photo',
        'capacite',
        'categorie',
        );       
        $this->requiredAttributes =  array( 
        'pays',
        'ville',
        'adresse',
        'cp',
        'titre',
        'description',
        'photo',
        'capacite',
        'categorie',
        );
        $this->uniqueAttributes =  array( 
            'titre',  
        );
    }

    public function setPays($pays)
    {
        $this->pays = $pays;
        return $this;
    }

    public function getPays()
    {
        return $this->pays;
    }

    public function setVille($ville)
    {
        $this->ville = $ville;
        return $this;
    }

    public function getVille()
    {
        return $this->ville;
    }

    public function setAdresse($adresse)
    {
        $this->adresse = $adresse;
        return $this;
    }

    public function getAdresse()
    {
        return $this->adresse;
    }

    public function setCp($cp)
    {
        $this->cp = $cp;
        return $this;
    }

    public function getCp()
    {
        return $this->cp;
    }

    public function setTitre($titre)
    {
        $this->titre = $titre;
        return $this;
    }

    public function getTitre()
    {
        return $this->titre;
    }

    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setPhoto($photo)
    {
        $this->photo = $photo;
        return $this;
    }

    public function getPhoto()
    {
        return $this->photo;
    }
    public function getPhotoHtml()
    {
        $url   = SITEURL . '/images/rooms/' .$this->photo;
        $title = $this->titre;
        return "<img title='$title' alt='Photographie de $title' src='$url' />";
    }

    public function setCapacite($capacite)
    {
        $this->capacite = $capacite;
        return $this;
    }

    public function getCapacite()
    {
        return $this->capacite;
    }

    public function setCategorie($categorie)
    {
        $this->categorie = $categorie;
        return $this;
    }

    public function getCategorie()
    {
        return $this->categorie;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setNote($note)
    {
        $this->note = $note;
        return $this;
    }

    public function getNote()
    {
        if (isset($this->note)) return $this->note;
        return false;
    }

    public function isDeletable(){
        $return = false;    
        $Srepository = new Repository\SalleRepository;
        if ($Srepository->isDeletable($this)) { 
            $Prepository = new Repository\ProduitRepository;
            $Prepository->deleteAsocciatedProducts($this->id);
            $return = $Srepository->delete($this);
        }
        return $return;
    }

}