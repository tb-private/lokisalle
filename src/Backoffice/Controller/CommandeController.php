<?php

namespace Backoffice\Controller;

use Controller\Controller;
use Lib;
use Entity;
use Repository;

class CommandeController extends Controller
{
    private $products;
    private $promotions;

    public function __construct()
    {
        $this->filterUser();
        $this->setProducts();
        $this->setPromotions();
    }

    public function newAction()
    {
        $request     = Lib\App::getRequest();
        $session     = Lib\Session::getInstance();
        $router      = Lib\App::getRouter();

        if ($request->postExists('payment')) {
            $membre_id = $session->getUser()->getId();
            $date = new \DateTime();
            $date = date('Y-m-d G:i:s', $date->getTimestamp());
            $montant = $this->getMontant();

            $commande = new Entity\Commande();
            $commande->setMembre_id($membre_id);
            $commande->setDate($date);
            $commande->setMontant($montant);

            if ($commande->save()) {
                $commandeId = Repository\CommandeRepository::getLastId();
                $detailsRecorded = true;
                foreach ($this->products as $product) {
                    $details = new Entity\DetailsCommande();
                    $details->setCommande_id($commandeId);
                    $details->setProduit_id($product['id']);
                    if (!$details->save()) {
                        $detailsRecorded = false;
                    } else {
                        $product = $this->getRepository('Produit')->find($product['id']);
                        $product->setEtat(1);
                        $product->update();
                    }
                    $cart = new Entity\Cart();
                    $cart->reset();
                }

                // delete order here based on last Id.
                $session->addSuccess('Payment enregistré, merci de votre confiance.');
                Lib\App::getRouter()->redirect('home');
            } else {
                $session->addError('La page de payment pas n\'est pas accessible via URL');
            }
        } else {
            $session->addError('La page de payment pas n\'est pas accessible via URL');
        }
        $router->redirect('cart');
    }

    public function ListAction()
    {
        $this->filterAdmin();
        $session      = Lib\Session::getInstance();
        $router       = Lib\App::getRouter();
        $commandes   = $this->getRepository('Commande')->findAll();

        $frontCommandes     = array();
        if (!empty($commandes)) {
            foreach ($commandes as $commande) {
                $frontCommandes[] =  array_merge($commande->getRecordable(), array(
                    'id' => $commande->getId(),
                    'voir les détails'   => $router->getRouteLink(array('admin_commande_show', 'id' => $commande->getId()), '#'),
                    ));
            }
        }

        return  $this->render(
            'layout.php',
            'commandes.php',
            array(
                'title'     => 'commandes',
                'h1'        => 'Toute les commandes enregistrées',
                'commandes'    => $frontCommandes,
            )
        );
    }

    public function detailsAction($id)
    {
        $this->filterAdmin();
        $session      = Lib\Session::getInstance();
        $router       = Lib\App::getRouter();
        $commandes   = $this->getRepository('DetailsCommande')->findByCommand($id);
        $FrontCommande  = array();
        if (!empty($commandes)) {
            foreach ($commandes as $commande) {
                $order   = $this->getRepository('Commande')->find($commande->getCommande_id());
                $member  = $this->getRepository('Membre')->find($order->getMembre_id());
                $product = $this->getRepository('Produit')->find($commande->getProduit_id());
                $salle   = $this->getRepository('Salle')->find($product->getSalle_id());
                $FrontCommande[] = array_merge($commande->getRecordable(), array(
                    'salle' => $salle->getTitre(),
                    'Client' => $member->getPseudo(),
                    'commande numéro' => $id,
                    'commande numéro' => $id,
                ));
            }
        }

        return  $this->render(
            'layout.php',
            'commande.php',
            array(
                'title'     => 'commandes',
                'h1'        => 'Détails de la commande n°'.$id,
                'commande' => $FrontCommande,
            )
        );
    }

/************* forms  *************/

    public function getMontant()
    {
        $total = 0;
        $totalDicount = 0;
        foreach ($this->products as $productId => $array) {
            $total += (float) $array['price'];
        }
        if (!empty($this->promotions)) {
            $PromoRepository = $this->getRepository('Promotion');
            foreach ($this->promotions as $promotionId => $array) {
                $totalDicount += (float) $array['discount'];
            }
        }

        return ($total*1.2)-$totalDicount;
    }

    public function setPromotions()
    {
        $cart = new Entity\Cart();
        $this->promotions = $cart->getPromotions();
    }

    public function setProducts()
    {
        $cart = new Entity\Cart();
        $this->products = $cart->getProducts();
    }
}
