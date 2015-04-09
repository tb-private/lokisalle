<?php

namespace Entity;
use Lib;
use Repository;
use Manager;


class Cart extends Entity
{
    private $user;
    private $session;
    private $Commande;
    private $products =  array();
    private $promotions =  array();

    public function __construct($properties = null){   
        $this->session = Lib\Session::getInstance();
        $this->user = $this->session->getUser();
        if (!($this->user instanceof Membre)){
          $this->session->addError('vous devez vous connecter ou créer un compte pour ajouter un produit au panier');
          Lib\App::getRouter()->redirect('login');
          Lib\cart::destroy($this);
        }
        else {
            $this->HrydrateFromSession();
        }
    }
    public static function destroy(&$cart) {
        unset($cart);
        return true;
    }

    public function store(){
        $this->session->delete("cart");
        $this->session->delete("promo");
        $this->session->set("cart", $this->products);
        $this->session->set("promo", $this->promotions);
    }

    public function reset(){
        $this->session->delete("cart");
        $this->session->delete("promo");
        $this->products = array();
        $this->promotions = array();
    }

    public function HrydrateFromSession(){
        if($this->session->exists("cart")){
            $this->products = $this->session->get("cart");
        }
        if($this->session->exists("promo")){
            $this->promotions = $this->session->get("promo");
        }
    }
    /*************************   Products   ***********************************/

    public function getProducts(){
        return $this->products;
    }
    public function add(Produit $product, $promo = null)
    {
        $id = (string) $product->getId();        
        if(array_key_exists($id, $this->products)){
            return false;
        }
        else{
            $this->products[$id] = array(
                'id'    => $id, 
                'promo' => $promo,
                'price' => $product->getPrix(),
            );
        }
        return $this;
    }
    public function remove($id)
    {
        $id = (int) $id;       
        if(!array_key_exists($id, $this->products)){ 
            return false;
        }
        else{
            if (!is_null($this->products[$id]['promo'])) {
                $promoId = $this->products[$id]['promo'];
                $this->removePromotion($promoId);
            }
            unset($this->products[$id]);
            return true;
        }
    }

    /*************************   Promotions   ***********************************/
    public function getPromotions(){
        return $this->promotions;
    }

    public function addPromotion(Promotion $promotion)
    {
        $id = (string) $promotion->getId();
        $product = null;
        $repository = new Repository\ProduitRepository();
        foreach ($this->getProducts() as $p) {
            $p = $repository->find($p['id']);
            if($p->getPromotionId() == $id){
                $product = $p;
                $titre = $p->getSalleEntity()->getTitre();
                break;
            }
        }

        if(is_object($product)){
            if(array_key_exists($id, $this->promotions)){
                return false;
            }
            else{
                $this->products[$product->getId()]['promo'] = $id;
                $this->promotions[$id] = array(
                    'id'       => $id, 
                    'code'     => $promotion->getCodePromo(),
                    'discount' => $promotion->getReduction(),
                    'product'  => $product->getId(),
                    'product-title'  => $titre,
                );
            }
        }
    }
    public function removePromotion($id)
    {
        $id = (int) $id;       
        if(!array_key_exists($id, $this->promotions)){ 
            return false;
        }
        else{            
            $product = $this->promotions[$id]['product'];
            $this->products[$product]['promo'] = null;
            unset($this->promotions[$id]);
            return true;
        }
    }

    
    /*************************/
    public function validations(){
        return false;
    }  

}
  
