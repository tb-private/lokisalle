<?php

namespace Backoffice\Controller;

use Controller\Controller;
use Lib;
use Lib\Session;
use Lib\Router;
use Entity;
use Form\Form;
use Form\Field;

class CartController extends Controller
{
    public function showAction()
    {
        $session    = Session::getInstance();
        $router     = Lib\App::getRouter();
        $cart       = new Entity\Cart();
        $Prepository = $this->getRepository('Produit');
        $Srepository = $this->getRepository('Salle');

        $FrontProducts = array();
        $FrontPromotions = array();
        $promoFrom  = $this->createPromoform();
        $products   = $cart->getProducts();
        $promotions = $cart->getPromotions();
        $total = 0;
        $totalDicount = 0;
        $validateCartForm = '';

        if (!empty($products)) {
            foreach ($products as $productId => $array) {
                $product = $Prepository->find($productId);
                $roomId = $product->getSalle();
                $room = $this->getRepository('Salle')->find($roomId);
                $FrontProducts[] = array(
                    'Photo'         => $room->getPhotoHtml(),
                    'Nom'           => $room->getTitre(),
                    'Date de début' => $product->getDateArriveeFr(),
                    'Date de fin'   => $product->getDateDepartFr(),
                    'Ville'         => $room->getVille(),
                    'capacité'      => $room->getCapacite(),
                    'Prix HT'       => $array['price'],
                    'Supprimer'     => $router->getRouteLink(array('cart_remove', 'id' => $productId), 'X'),
               );
                $total += (float) $array['price'];
            }

            if (!empty($promotions)) {
                $PromoRepository = $this->getRepository('Promotion');
                foreach ($promotions as $promotionId => $array) {
                    $promotion = $PromoRepository->find($promotionId);

                    $FrontPromotions[] = array(
                        'code'      => $promotion->getCodePromo(),
                        'product-title'      => $array['product-title'],
                        'discount' => $promotion->getReduction(),
                        'delete' => $router->getRouteLink(array('cart_delete_promo', 'id' => $promotionId), 'X'),
                   );
                    $totalDicount += (float) $array['discount'];
                }
            }
            $validateCartForm = $this->validateCartForm()->toHtml();
        } else {
            $link = $router->getRouteLink('product_list', 'voir nos produits.');
            $session->addSuccess("Votre panier est vide pour le moment. $link");
        }

        return  $this->render(
            'layout.php',
            'cart.php',
            array(
                'title'      => 'Votre panier',
                'h1'         => 'Votre panier',
                'products'   => $FrontProducts,
                'promotions' => $FrontPromotions,
                'user'       => $session->getUser(),
                'promoForm'  => $promoFrom->toHtml(),
                'total'      => $total,
                'totalDicount'  => $totalDicount,
                'validateCartForm'  => $validateCartForm,
            )
        );
    }

    public function addAction()
    {
        $request     = Lib\App::getRequest();
        $session     = Session::getInstance();
        $router      = Lib\App::getRouter();

        if ($request->postExists('add-to-cart')) {
            $id = $request->postData('id');

            $cart        = new Entity\Cart();
            $Prepository = $this->getRepository('Produit');
            $product     = $Prepository->find($id);
            if (is_object($product)) {
                $cart->add($product);
                $cart->store();
            }
        }

        $router->redirect('cart');
    }

    public function removeAction($id)
    {
        $request     = Lib\App::getRequest();
        $session     = Session::getInstance();
        $router      = Session::getInstance();
        $id = (int) $id[0];
        if (!empty($id)) {
            $router      = Lib\App::getRouter();
            $cart        = new Entity\Cart();
            if ($cart->remove($id)) {
                $session->addSuccess('le Produit à bien été retiré de votre panier.');
                $cart->store();
            };
        }
        $router->redirect('cart');
    }

    public function addPromotionAction($code)
    {
        $request     = Lib\App::getRequest();
        $session     = Session::getInstance();
        $router      = Lib\App::getRouter();
        if ($request->postExists('discount')) {
            $code          = $request->postData('code');
            $Prepository   = $this->getRepository('Promotion');
            $promotion     = $Prepository->findByCode($code);
            if (is_object($promotion)) {
                $cart          = new Entity\Cart();

                $cart->addPromotion($promotion);
                $cart->store();
            }
        }
        $router->redirect('cart');
    }

    public function removePromotionAction($id)
    {
        $request     = Lib\App::getRequest();
        $session     = Session::getInstance();
        $router      = Session::getInstance();

        $id = (int) $id[0];

        if (!empty($id)) {
            $router      = Lib\App::getRouter();
            $cart        = new Entity\Cart();
            if ($cart->removePromotion($id)) {
                $session->addSuccess('le code promo a bien été annulé.');
                $cart->store();
            } else {
                $session->addSuccess('le code promo n\'a pas pu être annulé.');
            }
        }
        $router->redirect('cart');
    }

/************* forms  *************/

    public function createSearchform()
    {
        $searchForm = new Form(new Entity\Produit(), 'post');
        $searchForm->add(new Field\Text(array(
            'label' => 'Par mot clef',
            'name' => 'keywords',
            )));
        $searchForm->add(new Field\Text(array(
            'label' => 'A partir de la date&nbsp;:',
            'name' =>  'date',
            )));
        $searchForm->add(new Field\Submit(array(
            'value' => 'Recherche',
            'name' => 'search',
            )));

        return $searchForm;
    }
    public function createPromoform()
    {
        $router = new Router();
        $promoForm = new Form(new Entity\Promotion(), 'post');
        $url = $router->getRoute('cart_add_promo');
        $promoForm->setAction($url);
        $promoForm->add(new Field\Text(array(
            'label' => 'Utiliser un code promo',
            'name'  => 'code',
            )));
        $promoForm->add(new Field\Submit(array(
            'value' => 'Appliquer',
            'name'  => 'discount',
            )));

        return $promoForm;
    }
    public function validateCartform()
    {
        $router = Lib\App::GetRouter();
        $session = Lib\App::GetSession();
        $validationForm = new Form(null, 'post');
        $url = $router->getRoute('commande_new');
        $validationForm->setAction($url);
        $validationForm->add(new Field\Submit(array(
            'value' => 'Effectuer le paiement',
            'name'  => 'payment',
            )));

        return $validationForm;
    }
}
