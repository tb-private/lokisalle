<?php

namespace Backoffice\Controller;

use Controller\Controller;
use Lib\Session;
use Lib\App;
use Entity;
use Form\Form;
use Form\Field;

class adminController extends Controller
{
    public function __construct()
    {
        $this->filterAdmin();
    }

    public function indexAction()
    {
        $products   = $this->getRepository('Produit')->findAll(2);
        $offers     = array();
        $session    = Session::getInstance();
        foreach ($products as $product) {
            $roomId = $product->getSalle();
            $room = $this->getRepository('Salle')->find($roomId);

            $offers[] = array(
            'id'        => $product->getId(),
            'name'      => $room->getTitre(),
            'imageUrl'  => $room->getPhoto(),
            'stardDate' => $product->getDateArrivee(),
            'endDate'   => $product->getDateDepart(),
            'city'      => $room->getVille(),
            'price'     => $product->getPrix(),
            'capacity'  => $room->getCapacite(),

           );
        }

        return $this->render(
            'layout.php',
            '../default/default.php',
            array(
                'title'      => 'Lokisalle',
                'h1'         => 'Lokisalle',
                'lastOffers' => $offers,
                'user'       => $session->getUser(),
            )
        );
    }

    public function promoAction($id)
    {
        $this->filterAdmin();
        $promos = $this->getRepository('Promotion')->findAll();
        if (!empty($promos)) {
            foreach ($promos as $promo) {
                $roomId  = $promo->getProduct();
                $room    = $this->getRepository('Salle')->find($roomId);
                $promoForm = $this->createPromoForm($promo);
                $promoArray[] = array(
                    'id'        => $promo->getId(),
                    'name'      => $room->getTitre(),
                    'imageUrl'  => $room->getPhoto(),
                    'stardDate' => $promo->getDateArriveeFr(),
                    'endDate'   => $promo->getDateDepartFr(),
                    'city'      => $room->getVille(),
                    'price'     => $promo->getPrix(),
                    'capacity'  => $room->getCapacite(),
                    'form'      => $promoForm->toHtml(),
               );
            }
        }
    }

    public function sallesAction()
    {
        $session    = Session::getInstance();
        $router     = App::getRouter();
        $rooms      = $this->getRepository('Salle')->findAll();
        $frontRooms = array();
        if (!empty($rooms)) {
            foreach ($rooms as $room) {
                $frontRooms[] = array(
                    'id'            => $room->getId(),
                    'Nom'           => $room->getTitre(),
                    'Photo'         => $room->getPhoto(),
                    'Pays'          => $room->getPays(),
                    'Ville'         => $room->getVille(),
                    'Adresse'       => $room->getAdresse(),
                    'Code postal'   => $room->getCp(),
                    'Description'   => $room->getDescription(),
                    'Capacité'      => $room->getCapacite(),
                    ' '             => $router->getRouteLink(array('admin_salle_edit', 'id' => $room->getId()), 'éditer'),
                );
            }
        }

        return  $this->render(
            'layout.php',
            'salles.php',
            array(
                'title'     => 'Lokisalle',
                'h1'        => 'Toute nos offres',
                'frontRooms'    => $frontRooms,
            )
        );
    }

    public function salleEditAction($options)
    {
        $request  = App::getRequest();
        $session  = App::getSession();
        $options  = explode(',', $options);
        $id       = (int) $options[0];
        $room     = $this->getRepository('Salle')->find($id);
        $editForm = '';
        if (is_object($room)) {
            if ($request->postExists('edit-salle')) {
                $room->hydrate($request->postDataArray());

                if ($room->update()) {
                    $session->addSuccess('Les modifications ont été enregistrées');
                    App::getRouter()->redirect('admin_salles');
                } else { // if a strange problem occurs ?
                    $session->addError('Il semblerait que vos informations soient incorrectes, veuillez remplir le formulaire à nouveau. si le problème persiste, veuillez contacter l\'administrateur du site.');
                    $editForm = $this
                        ->createEditSalleForm($room)
                        ->hydrate($request->postDataArray())
                        ->toHtml();
                }
            } else {
                $editForm = $this->createEditSalleForm($room)->toHtml();
            }
        } else {
            $session->addError('Cette salle n\'existe pas.');
            App::getRouter()->redirect('admin_salles');
        }

        return  $this->render(
            'layout.php',
            'salle.php',
            array(
                'title'     => 'Lokisalle',
                'h1'        => $room->getTitre(),
                'form'      => $editForm,
            )
        );
    }

    public function salleCreateAction()
    {
        $request = App::getRequest();
        $session = App::getSession();
        $createForm = '';

        if ($request->postExists('create-salle')) {
            $room = new Entity\Salle();
            $room->hydrate($request->postDataArray());

            if ($room->save()) {
                $session->addSuccess('La salle a été enregistrée.');
                App::getRouter()->redirect('admin_salles');
            } else { // if a strange problem occurs ?
                $session->addError('Il semblerait que vos informations soient incorrectes, veuillez remplir le formulaire à nouveau. si le problème persiste, veuillez contacter l\'administrateur du site.');
                $createForm = $this
                    ->createCreateSalleForm($room)
                    ->hydrate($request->postDataArray())
                    ->toHtml();
            }
        } else {
            $createForm = $this->createCreateSalleForm($room)->toHtml();
        }

        return  $this->render(
            'layout.php',
            'salle.php',
            array(
                'title'     => 'Nouvelle Salle',
                'h1'        => 'Nouvelle Salle',
                'form'      => $createForm,
            )
        );
    }

    public function statisticsAction()
    {
        $session    = Session::getInstance();
        $router     = App::getRouter();
        $limit      = 5;
        $rooms      = $this->getRepository('Salle')->getTopNotes($limit);
        $TopNoteRooms  = array();
        if (!empty($rooms)) {
            foreach ($rooms as $room) {
                $TopNoteRooms[] = array(
                    'Nom'           => $room->getTitre(),
                    'Note moyenne'  => number_format($room->getNote(), 1, ',', '&nbsp;'),
                    'Photo'         => $room->getPhotoHtml(),
                );
            }
        }

        $rooms    = $this->getRepository('Salle')->getTopSold($limit);
        $topSold  = array();
        if (!empty($rooms)) {
            foreach ($rooms as $room) {
                $topSold[] = array(
                    'Nom'           => $room->getTitre(),
                    'Photo'         => $room->getPhotoHtml(),
                );
            }
        }
        $users  = $this->getRepository('Membre')->getTopBookings($limit);
        $topBookers  = array();
        if (!empty($rooms)) {
            foreach ($users as $user) {
                $topBookers[] = array(
                    'Nom'           => $user->getPseudo(),
                    'Nombre de commandes' => $user->getBookings(),
                );
            }
        }
        $users  = $this->getRepository('Membre')->getTopBuyers($limit);
        $topBuyers  = array();
        if (!empty($rooms)) {
            foreach ($users as $user) {
                $topBuyers[] = array(
                    'Nom'           => $user->getPseudo(),
                    'Montant total' => $user->getTotalMontant(),
                );
            }
        }

        return  $this->render(
            'layout.php',
            'statistics.php',
            array(
                'title'     => 'Statistiques',
                'h1'        => 'Statistiques',
                'TopNoteRooms'  => $TopNoteRooms,
                'topSold'       => $topSold,
                'topBookers'    => $topBookers,
                'topBuyers'    => $topBuyers,
            )
        );
    }

    public function filterAdmin()
    {
        $session    = Session::getInstance();
        if (!$session->is_admin()) {
            $router = App::getRouter();
            $session->addError('Vous n\'avez pas accès à cette partie du site');
            $router->redirect('home');
        }

        return $this;
    }

    private function createEditSalleForm($salle)
    {
        $editSalleForm = new Form($salle);
        $editSalleForm
            ->selfCreate()
            ->selfHydrate()
            ->add(new Field\Submit(array('name'  => 'edit-salle', 'value' => 'Mettre à jour')));

        return $editSalleForm;
    }

    private function createCreateSalleForm()
    {
        $salle = new Entity\Salle();
        $createSalleForm = new Form($salle);
        $createSalleForm
            ->selfCreate()
            ->add(new Field\Submit(array('name'  => 'create-salle', 'value' => 'Enregistrer')));

        return $createSalleForm;
    }
}
