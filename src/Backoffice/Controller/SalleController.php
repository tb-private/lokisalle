<?php

namespace Backoffice\Controller;

use Controller\Controller;
use Lib\Session;
use Lib\App;
use Lib;
use Entity;
use Form\Form;
use Form\Field;

class SalleController extends Controller
{
   public function sallesAction()
   {
       $this->filterAdmin();
       $session    = Session::getInstance();
       $router    = App::getRouter();
       $rooms    = $this->getRepository('Salle')->findAll();
       $frontRooms  = array();
       if (!empty($rooms)) {
           foreach ($rooms as $room) {
               $frontRooms[] = array(
                    'id'            => $room->getId(),
                    'Nom'           => $room->getTitre(),
                    'Photo'         => $room->getPhotoHtml(),
                    'Pays'          => $room->getPays(),
                    'Ville'         => $room->getVille(),
                    'Adresse'       => $room->getAdresse(),
                    'Code postal'   => $room->getCp(),
                    'Description'   => $room->getDescription(),
                    'Capacité'      => $room->getCapacite(),
                    ' '             => $router->getRouteLink(array('admin_salle_edit', 'id' => $room->getId()), 'éditer'),
                    'supprimer'   => $router->getRouteLink(array('admin_salle_delete', 'id' => $room->getId()), 'X'),

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
        $this->filterAdmin();
        $request = App::getRequest();
        $session = App::getSession();
        $options  = explode(',', $options);
        $id       = (int) $options[0];
        $room     = $this->getRepository('Salle')->find($id);
        $editForm = '';
        if (is_object($room)) {
            if ($request->postExists('edit-salle')) {
                $room->hydrate($request->postDataArray());
                $img = $this->postFile('photo');
                if (!empty($img)) {
                    $room->setPhoto($img);
                };

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
                'form'  => $editForm,
            )
        );
    }
    public function salleCreateAction()
    {
        $this->filterAdmin();
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
            $createForm = $this->createCreateSalleForm()->toHtml();
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

    public function deleteAction($options)
    {
        $this->filterAdmin();
        $request = Lib\App::getRequest();
        $session = Lib\App::getSession();
        $router = Lib\App::getRouter();
        $options  = explode(',', $options);
        $id       = (int) $options[0];
        $salle     = $this->getRepository('Salle')->find($id);
        $editForm = '';
        if (is_object($salle)) {
            if ($salle->isDeletable()) {
                $session->addSuccess('La salle à été supprimée.');
                $router->redirect('admin_salles');
            } else {
                $session->addError('Cette salle ne peut être supprimée car elle est associée a une commande.');
                $router->redirect('admin_salles');
            }
        } else {
            $session->addError('Cette salle n\'existe pas.');
            $router->redirect('admin_salles');
        }
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
