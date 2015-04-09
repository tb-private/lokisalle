<?php

namespace Entity;
use Lib;
use Repository;
use Manager;


class Membre extends Entity
{
    private $pseudo;
    private $mdp;
    private $password;
    private $nom;
    private $prenom;
    private $email;
    private $sexe;
    private $ville;
    private $cp;
    private $adresse;
    private $statut;
    private $role;
    private $id;
    private $sesAvis;
    private $sesCommande;
    private $bookings;

    public function __construct($properties = null){
        $this->statut = ($this->statut == 3)? 3 :2;
        $this->setRole();
        if(isset($properties) && is_array($properties)){
            $this->hydrate($properties);
        }
        $this->recordableAttributes =  array( 
            'pseudo',  
            'mdp',     
            'nom',     
            'prenom',  
            'email',   
            'sexe',    
            'ville',   
            'cp',  
            'adresse',  
            'statut',        
            );       
        $this->requiredAttributes =  array( 
            'pseudo',  
            'mdp',     
            'nom',     
            'prenom',  
            'email',   
            'sexe',    
            'ville',   
            'cp',  
            'adresse',          );
        $this->uniqueAttributes =  array( 
            'email',   
            'pseudo',  
        );
    }

    public function setPseudo($pseudo)
    {
        $this->pseudo = $pseudo;
        return $this;
    }

    public function getPseudo()
    {
        return $this->pseudo;
    }

    public function setMdp($mdp)
    {
        $this->mdp = $mdp;
        $this->password = $mdp;
        return $this;
    }

    public function getMdp()
    {
        return $this->mdp;
    }

    public function setNom($nom)
    {
        $this->nom = $nom;
        return $this;
    }

    public function getNom()
    {
        return $this->nom;
    }

    public function setPrenom($prenom)
    {
        $this->prenom = $prenom;
        return $this;
    }

    public function getPrenom()
    {
        return $this->prenom;
    }

    public function setEmail($email)
    {
        $this->email = $email;
        return $this;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function setSexe($sexe)
    {
        $this->sexe = $sexe;
        return $this;
    }

    public function getSexe()
    {
        return $this->sexe;
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

    public function setCp($cp)
    {
        $this->cp = $cp;
        return $this;
    }

    public function getCp()
    {
        return $this->cp;
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

    public function setStatut($statut)
    {
        $this->statut = $statut;
        $this->setRole();
        return $this;
    }

    public function setRole(){
        if($this->statut == 3)
            $this->role = "ROLE_ADMIN";
        else
            $this->role = "ROLE_USER";
        return $this;
    }

    public function getRole(){
        return $this->role;
    }

    public function getStatut()
    {
        return $this->statut;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        //if(is_int($id))
            $this->id = $id;
        return $this;
    }

    public function setBookings($bookings)
    {
        $this->bookings = $bookings;
        return $this;
    }

    public function getBookings()
    {
        return $this->bookings;
    }
    public function setTotalMontant($totalMontant)
    {
        $this->totalMontant = $totalMontant;
        return $this;
    }

    public function getTotalMontant()
    {
        return $this->totalMontant;
    }

    public function connexion(){
        $session = Lib\App::getSession();
        $repository = new Repository\MembreRepository;
        $member = $repository->tryConnexion($this->pseudo, $this->mdp);
        if(is_object($member)){
            $id = $member->getId();
            if(!empty($id)){
                $session->setUser($id);
                return true;   
            }
            else return false;
        }
        return false;
    }

    public function validations(){
        $return  = array();
        if(strlen($this->pseudo) < 3) 
            $return['pseudo'] = 'Le pseudonyme doit dépasser 3 caractères.';
        if(strlen($this->mdp) < 3) 
            $return['mdp'] = 'Le mot-de-passe doit dépasser 3 caractères.';
        if(strlen($this->nom) < 3) 
            $return['nom'] = 'Le nom de passe doit dépasser 3 caractères.';
        if(strlen($this->prenom) < 3) 
            $return['prenom'] = 'Le prénom de passe doit dépasser 3 caractères.';
        if(!filter_var($this->email, FILTER_VALIDATE_EMAIL)) 
            $return['email'] = 'Adresse email invalide.';
        if(strlen($this->ville) < 3) 
            $return['ville'] = 'Ville invalide.';
        if(strlen($this->cp) < 3) 
            $return['cp'] = 'Code postal invalide.';
        if(strlen($this->adresse) < 5) 
            $return['adresse'] = 'Adresse invalide.';        
        return $return;
    }  

}
  
