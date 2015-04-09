<?php

namespace Backoffice\Controller;

use Controller\Controller;
use Lib\Session;
use Lib\Router;
use Lib\App;
use Lib;
use Entity;
use Form;
use Form\Field;

class ProduitController extends Controller
{
    public function showAction($id)
    {
        $session    = Session::getInstance();
        $router     = new Router();
        $Prepository = $this->getRepository('Produit');
        $product    = $Prepository->findAvailable($id);

        if (empty($product)) {
            $message = "La salle portant l'identifiant $id n'a pas étée trouvée.";
            $router->redirect('product_list');
        }
        $roomId     = $product->getSalle();
        $Srepository = $this->getRepository('Salle');
        $Crepository = $this->getRepository('Avis');
        $room       = $Srepository->find($roomId);
        $noteAverage       = $Srepository->getNoteAverage($roomId);
        $comments   = $Srepository->getAvis($roomId);
        $commentForm = '';
        if ($session->is_connected()) {
            $userId = $session->getUser()->getId();
            $request    = Lib\App::getRequest();
            $allreadyPost = $Crepository->hasComment($roomId, $userId);

            if ($request->postExists('new-comment') && $allreadyPost != true) {
                if ($roomId == $request->postData('salle_id') && $userId == $request->postData('membre_id')) {
                    $avis = new Entity\Avis($request->postDataArray());
                    $date = new \DateTime();
                    $date = date('Y-m-d G:i:s', $date->getTimestamp());
                    $avis->setDate($date);
                    if ($avis->save()) {
                        $session->addSuccess('Votre avis à bien été enregistré, Merci!');
                    } else { // if a strange problem occurs ?
                        $session->addError('Il semblerait que vos informations soient incorrectes, veuillez rédiger l\'avis à nouveau. si le problème persiste, veuillez contacter l\'administrateur du site.');
                        $commentForm = $this->createCommentForm($roomId);
                        $commentForm->hydrate($request->postDataArray());
                    }
                } else {
                    $session->addError('Il semblerait que vos informations soient incorrectes, veuillez rédiger l\'avis à nouveau. si le problème persiste, veuillez contacter l\'administrateur du site.');
                    $commentForm = $this->createCommentForm($roomId);
                    //$commentForm->hydrate($request->postDataArray());
                }
            } elseif ($allreadyPost) {
                $commentForm = 'Vous avez déjà laissé un commentaire sur cette salle. Merci de votre contribution.';
            } else {
                $commentForm = $this->createCommentForm($roomId);
                $commentForm->toHtml();
            }
        } else {
            $link = $router->getRouteLink('login', 'connecté');
            $commentForm = "Il faut être $link pour pouvoir poster un commentaire.";
        }

        $addForm = $this->createAddForm($product);

        $FrontProduct = array(
            'id'         => $product->getId(),
            'name'       => 'coucou ='.$room->getTitre(),
            'imageUrl'   => $room->getPhoto(),
            'stardDate'  => $product->getDateArriveeFr(),
            'endDate'    => $product->getDateDepartFr(),
            'country'    => $room->getPays(),
            'city'       => $room->getVille(),
            'adress'     => $room->getAdresse(),
            'zip'        => $room->getCp(),
            'price'      => $product->getPrix(),
            'capacity'   => $room->getCapacite(),
            'description' => $room->getDescription(),
            'category'   => $room->getCategorie(),
            'note'       => number_format($noteAverage[0], 1, ',', '&nbsp;'),
            'add'        => $addForm->toHtml(),
        );

        $frontComments = array();

        foreach ($comments as $comment) {
            $frontComments[] = array(
                'author' => $comment->getMembre()->getPseudo(),
                'date'   => $comment->getDate(),
                'note'   => $comment->getNote(),
                'comment' => $comment->getCommentaire(),
            );
        }

        $related = $Prepository->findRelated($product, 3);
        if (!empty($related)) {
            $frontRelated = array();
            foreach ($related as $product) {
                $roomId = $product->getSalle();
                $roomRelated   = $this->getRepository('Salle')->find($roomId);
                $form   = $this->createAddForm($product);
                $frontRelated[] = array(
                    'id'        => $product->getId(),
                    'name'      => $roomRelated->getTitre(),
                    'imageUrl'  => $roomRelated->getPhoto(),
                    'stardDate' => $product->getDateArriveeFr(),
                    'endDate'   => $product->getDateDepartFr(),
                    'city'      => $roomRelated->getVille(),
                    'price'     => $product->getPrix(),
                    'capacity'  => $roomRelated->getCapacite(),
                    'add'       => $form->toHtml(),
               );
            }
        }
        if (is_object($commentForm)) {
            $commentForm = $commentForm->toHtml();
        }

        return  $this->render(
            'layout.php',
            'product.php',
            array(
                'title'      => $room->getTitre(),
                'h1'         => $room->getTitre(),
                'product'    => $FrontProduct,
                'user'       => $session->getUser(),
                'comments'   => $frontComments,
                'commentForm' => $commentForm,
                'related'    => $frontRelated,
            )
        );
    }
    public function listAction()
    {
        $session    = Session::getInstance();
        $products   = $this->getRepository('Produit')->findAllAvailable();

        $offers     = array();
        if (!empty($products)) {
            foreach ($products as $product) {
                $roomId = $product->getSalle();
                $room = $this->getRepository('Salle')->find($roomId);
                $addForm = $this->createAddForm($product);
                $offers[] = array(
                    'id'        => $product->getId(),
                    'name'      => $room->getTitre(),
                    'imageUrl'  => $room->getPhoto(),
                    'stardDate' => $product->getDateArriveeFr(),
                    'endDate'   => $product->getDateDepartFr(),
                    'city'      => $room->getVille(),
                    'price'     => $product->getPrix(),
                    'capacity'  => $room->getCapacite(),
                    'add'       => $addForm->toHtml(),
               );
            }
        }

        return  $this->render(
            'layout.php',
            'productList.php',
            array(
                'title'     => 'Lokisalle',
                'h1'        => 'Toute nos offres',
                'offers'    => $offers,
                'user'      => $session->getUser(),
            )
        );
    }
    public function searchAction()
    {
        $session = Session::getInstance();
        $request = App::getRequest();
        $searchForm = $this->createSearchform();
        $result = '';
        if ($request->postExists('search')) {
            $keywords = '';
            $date = new \DateTime();
            $keywords = $request->postData('keywords');
            $date = $request->postData('date');
            if (!empty($keywords)) {
                $searchForm->setFieldValue('keywords',  htmlspecialchars($keywords));
            }
            if (!empty($date)) {
                $searchForm->setFieldValue('date', htmlspecialchars($request->postData('date')));
                $date = \DateTime::createFromFormat('d/m/Y', $request->postData('date'));
                if (!is_object($date)) {
                    $date = new \DateTime();
                    $session->addError('La date renseignée semble être demandée dans un format invalide, veuillez utilisez l\'outils de sélection fournis ou rentrer une date au format "jj/mm/yyyy"');
                }
            } else {
                $date = new \DateTime();
            }
            $products = $this->getRepository('Produit')->findSearch($date->getTimestamp(), $keywords);
            $result = count($products);
            if (empty($result)) {
                $session->addError('Aucune salle trouvée pour ces critères.');
            }
        } else {
            $products   = $this->getRepository('Produit')->findAllAvailable();
        }

        $offers  = array();
        if (!empty($products)) {
            foreach ($products as $product) {
                $roomId = $product->getSalle();
                $room = $this->getRepository('Salle')->find($roomId);
                $form = $addForm = $this->createAddForm($product);
                $offers[] = array(
                    'id'        => $product->getId(),
                    'name'      => $room->getTitre(),
                    'imageUrl'  => $room->getPhoto(),
                    'stardDate' => $product->getDateArriveeFr(),
                    'endDate'   => $product->getDateDepartFr(),
                    'city'      => $room->getVille(),
                    'price'     => $product->getPrix(),
                    'capacity'  => $room->getCapacite(),
                    'add'       => $form->toHtml(),
               );
            }
        }

        return  $this->render(
            'layout.php',
            'productList.php',
            array(
                'title'     => 'Recherche',
                'h1'        => 'Votre recherche',
                'offers'    => $offers,
                'user'      => $session->getUser(),
                'searchForm' => $searchForm->toHtml(),
                'result'    => $result,
            )
        );
    }

//REST

   public function adminListAction()
   {
       $this->filterAdmin();
       $session      = Lib\Session::getInstance();
       $router       = Lib\App::getRouter();
       $produits   = $this->getRepository('Produit')->findAll();

       $frontProduits     = array();
       if (!empty($produits)) {
           foreach ($produits as $produit) {
               $frontProduits[] =  array(
                    'id'          => $produit->getId(),
                    'Arrivée'     => $produit->getDateArriveeFr(),
                    'Départ'      => $produit->getDateDepartFr(),
                    'Prix'      => $produit->getPrix().'€',
                    'salle'       => $produit->getSalleEntity()->getTitre(),
                    'promotion'   => $produit->getPromotionEntity()->getCode_promo(),
                    ' '           => $router->getRouteLink(array('admin_produit_edit', 'id' => $produit->getId()), 'éditer'),
                    'supprimer'   => $router->getRouteLink(array('admin_produit_delete', 'id' => $produit->getId()), 'X'),
               );
           }
       }

       return  $this->render(
            'layout.php',
            'produits.php',
            array(
                'title'     => 'produits',
                'h1'        => 'Tout les produits enregistrés',
                'products'    => $frontProduits,
            )
        );
   }

    public function editAction($options)
    {
        $this->filterAdmin();
        $request = Lib\App::getRequest();
        $session = Lib\App::getSession();
        $options  = explode(',', $options);
        $id       = (int) $options[0];
        $produit     = $this->getRepository('produit')->find($id);
        $editForm = '';
        $error = false;

        if (is_object($produit)) {
            if ($request->postExists('edit-produit')) {
                $produit->hydrate($request->postDataArray());
                $produit->setSalle($request->postData('salle_id'));
                $datearrivee = \DateTime::createFromFormat('d/m/Y', $request->postData('date_arrivee'));
                $datedepart = \DateTime::createFromFormat('d/m/Y', $request->postData('date_depart'));

                if (!is_object($datearrivee)) {
                    $session->addError('La date d\'arrivée renseignée est dans format invalide, veuillez utilisez l\'outils de sélection fournis ou rentrer une date au format "jj/mm/yyyy"');
                    $editForm = $this
                    ->createEditproduitForm($produit)
                    ->hydrate($request->postDataArray())
                    ->toHtml();
                    $error = true;
                }
                if (!is_object($datedepart)) {
                    $session->addError('La date d\'arrivée renseignée est dans format invalide, veuillez utilisez l\'outils de sélection fournis ou rentrer une date au format "jj/mm/yyyy"');
                    $editForm = $this
                    ->createEditproduitForm($produit)
                    ->hydrate($request->postDataArray())
                    ->toHtml();
                    $error = true;
                }
                if (!$error) {
                    $produit->setDate_arrivee($datearrivee->format('Y-m-d H:i:s'));
                    $produit->setDate_depart($datedepart->format('Y-m-d H:i:s'));
                    if ($produit->update()) {
                        $session->addSuccess('Les modifications ont été enregistrées');
                        Lib\App::getRouter()->redirect('admin_produits');
                    } else { // if a strange problem occurs ?
                    $session->addError('Il semblerait que vos informations soient incorrectes, veuillez remplir le formulaire à nouveau. si le problème persiste, veuillez contacter l\'administrateur du site.');
                        $editForm = $this
                        ->createEditproduitForm($produit)
                        ->hydrate($request->postDataArray())
                        ->toHtml();
                    }
                }
            } else {
                $editForm = $this->createEditproduitForm($produit)->toHtml();
            }
        } else {
            $session->addError('Cette produit n\'existe pas.');
            Lib\App::getRouter()->redirect('admin_produits');
        }

        return  $this->render(
            'layout.php',
            'produit.php',
            array(
                'title'     => 'Lokiproduit',
                'h1'        => 'édition du code',
                'form'      => $editForm,
            )
        );
    }
    public function CreateAction()
    {
        $this->filterAdmin();
        $request = Lib\App::getRequest();
        $session = Lib\App::getSession();
        $createForm = '';
        $error = false;

        if ($request->postExists('create-produit')) {
            $produit = new Entity\produit();
            $produit->hydrate($request->postDataArray());
            $produit->setSalle($request->postData('salle_id'));
            $datearrivee = \DateTime::createFromFormat('d/m/Y', $request->postData('date_arrivee'));
            $datedepart = \DateTime::createFromFormat('d/m/Y', $request->postData('date_depart'));

            if (!is_object($datearrivee)) {
                $session->addError('La date d\'arrivée renseignée est dans format invalide, veuillez utilisez l\'outils de sélection fournis ou rentrer une date au format "jj/mm/yyyy"');
                $createForm = $this
                  ->createCreateproduitForm($produit)
                  ->hydrate($request->postDataArray())
                  ->toHtml();
                $error = true;
            }
            if (!is_object($datedepart)) {
                $session->addError('La date d\'arrivée renseignée est dans format invalide, veuillez utilisez l\'outils de sélection fournis ou rentrer une date au format "jj/mm/yyyy"');
                $createForm = $this
                  ->createCreateproduitForm($produit)
                  ->hydrate($request->postDataArray())
                  ->toHtml();
                $error = true;
            }
            if (!$error) {
                $produit->setDate_arrivee($datearrivee->format('Y-m-d H:i:s'));
                $produit->setDate_depart($datedepart->format('Y-m-d H:i:s'));

                if ($produit->save()) {
                    $session->addSuccess('La produit a été enregistré.');
                    Lib\App::getRouter()->redirect('admin_produits');
                } else { // if a strange problem occurs ?
                    $session->addError('Il semblerait que vos informations soient incorrectes, veuillez remplir le formulaire à nouveau. si le problème persiste, veuillez contacter l\'administrateur du site.');
                    $createForm = $this
                        ->createCreateproduitForm($produit)
                        ->hydrate($request->postDataArray())
                        ->toHtml();
                }
            }
        } else {
            $createForm = $this->createCreateproduitForm()->toHtml();
        }

        return  $this->render(
            'layout.php',
            'produit.php',
            array(
                'title'     => 'Nouveau produit',
                'h1'        => 'Nouveau produit',
                'form'      => $createForm,
            )
        );
    }

    public function deleteAction($options)
    {
        $this->filterAdmin();
        $request = Lib\App::getRequest();
        $session = Lib\App::getSession();
        $router = Lib\App::getRouter();
        $options  = explode(',', $options);
        $id       = (int) $options[0];
        $produit     = $this->getRepository('Produit')->find($id);

        if (is_object($produit)) {
            if ($produit->isDeletable()) {
                $session->addSuccess('La produit à été supprimée.');
                Lib\App::getRouter()->redirect('admin_produits');
            } else {
                $session->addError('Ce produit ne peut être supprimé car il est associé a une commande.');
                $router->redirect('admin_produits');
            }
        }
    }

// forms *************************************************

    private function createEditproduitForm($produit)
    {
        $editproduitForm = new Form\Form($produit);
        $editproduitForm
            ->selfCreate()
            ->remove('salle_id')
            ->add(new Field\EntitySelect(array(
                'label' => 'Salle',
                'name'  => 'salle_id',
                'value' => $produit->getSalleEntity()->getId(),
                'options'  => array('Salle', 'titre'),
                )))
            ->remove('promotion_id')
            ->add(new Field\EntitySelect(array(
                'label' => 'Promotion',
                'name'  => 'promotion_id',
                'value' => $produit->getPromotionEntity()->getId(),
                'options'  => array('Promotion', 'code_promo'),
                )))
            ->selfHydrate()
            ->setFieldValue('date_arrivee', $produit->getDateArriveeFormat())
            ->setFieldValue('date_depart', $produit->getDateDepartFormat())
            ->add(new Field\Submit(array('name'  => 'edit-produit', 'value' => 'Mettre à jour')));

        return $editproduitForm;
    }
    private function createCreateproduitForm()
    {
        $produit = new Entity\produit();
        $createproduitForm = new Form\Form($produit);
        $createproduitForm
            ->selfCreate()
            ->remove('salle_id')
            ->add(new Field\EntitySelect(array(
                'label' => 'Salle',
                'name'  => 'salle_id',
                'options'  => array('Salle', 'titre'),
                )))
            ->remove('promotion_id')
            ->add(new Field\EntitySelect(array(
                'label' => 'Promotion',
                'name'  => 'promotion_id',
                'options'  => array('Promotion', 'code_promo'),
                )))
            ->add(new Field\Submit(array('name'  => 'create-produit', 'value' => 'Enregistrer')));

        return $createproduitForm;
    }

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

    public function createAddForm($product)
    {
        $addForm = new Form(new Entity\Produit());
        $router  = new Router();
        $url     = $router->getRoute('cart_add');
        $addForm->setAction($url);
        $addForm->add(new Field\Hidden(array('name'  => 'id', 'value' => $product->getId())));
        $addForm->add(new Field\Submit(array('name'  => 'add-to-cart', 'value' => 'Ajouter au panier')));

        return $addForm;
    }

    public function createCommentForm($roomId)
    {
        $router = Lib\App::getRouter();
        $commentForm = new Form(new Entity\Avis());
        $userId = Lib\App::getSession()->getUser()->getId();

        $commentForm->add(new Field\Hidden(array('name'  => 'salle_id', 'value' => $roomId)));
        $commentForm->add(new Field\Hidden(array('name'  => 'membre_id', 'value' => $userId)));
        $commentForm->add(new Field\Textarea(array('name'  => 'commentaire', 'label' => 'Commentaire *')));
        $commentForm->add(new Field\Select(array('name'  => 'note', 'label' => 'Notez cette salle *', 'options'  => array('1', '2', '3', '4', '5', '6', '7', '8', '9', '10'))));
        $commentForm->add(new Field\Submit(array('name'  => 'new-comment', 'value' => 'commenter')));

        return $commentForm;
    }
}
