<?php

namespace Backoffice\Controller;

use Controller\Controller;
use Lib;
use Entity;
use Form\Form;
use Form\Field;

class AvisController extends Controller
{
    public function indexAction()
    {
    }

    public function listAction()
    {
        $this->filterAdmin();
        $session      = Lib\Session::getInstance();
        $router       = Lib\App::getRouter();
        $avis   = $this->getRepository('Avis')->findAll();

        $frontAvis     = array();
        if (!empty($avis)) {
            foreach ($avis as $comment) {
                $frontAvis[] = array(
                    'id'     => $comment->getId(),
                    'Auteur' => $comment->getMembre()->getPseudo(),
                    'date'   => $comment->getDateFr(),
                    'note'   => $comment->getNote(),
                    'comment' => $comment->getCommentaire(),
                    ' '           => $router->getRouteLink(array('admin_avis_edit', 'id' => $comment->getId()), 'éditer'),
                    'supprimer'   => $router->getRouteLink(array('admin_avis_delete', 'id' => $comment->getId()), 'X'),
               );
            }
        }

        return  $this->render(
            'layout.php',
            'Aviss.php',
            array(
                'title'     => 'Avis',
                'h1'        => 'Tout les avis enregistrés',
                'promos'    => $frontAvis,
            )
        );
    }

    public function addAction($id)
    {
        $router->redirect('product_show', array('id' => $id));
    }

    public function createCommentForm($roomId)
    {
        $router = Lib\App::getRouter();
        $url = $router->getRoute('comment_add', array('id' => $roomId));
        $commentForm = new Form(new Entity\Avis());
        $userId = Lib\App::getSession()->getUser()->getId();
        $commentForm->setAction($url);
        $commentForm->add(new Field\Hidden(array('name'  => 'room-id', 'value' => $roomId)));
        $commentForm->add(new Field\Hidden(array('name'  => 'user-id', 'value' => $userId)));
        $commentForm->add(new Field\Textarea(array('name'  => 'comment')));
        $commentForm->add(new Field\Submit(array('name'  => 'new-comment', 'value' => 'commenter')));

        return $commentForm;
    }

// REST

     public function editAction($options)
     {
         $this->filterAdmin();
         $request = Lib\App::getRequest();
         $session = Lib\App::getSession();
         $options  = explode(',', $options);
         $id       = (int) $options[0];
         $avis     = $this->getRepository('Avis')->find($id);
         $editForm = '';
         if (is_object($avis)) {
             if ($request->postExists('edit-Avis')) {
                 $avis->hydrate($request->postDataArray());

                 if ($avis->update()) {
                     $session->addSuccess('Les modifications ont été enregistrées');
                     Lib\App::getRouter()->redirect('admin_avis');
                 } else { // if a strange problem occurs ?
                    $session->addError('Il semblerait que vos informations soient incorrectes, veuillez remplir le formulaire à nouveau. si le problème persiste, veuillez contacter l\'administrateur du site.');
                     $editForm = $this
                        ->createEditAvisForm($avis)
                        ->hydrate($request->postDataArray())
                        ->toHtml();
                 }
             } else {
                 $editForm = $this->createEditAvisForm($avis)->toHtml();
             }
         } else {
             $session->addError('Cette Avis n\'existe pas.');
             Lib\App::getRouter()->redirect('admin_avis');
         }

         return  $this->render(
            'layout.php',
            'avis.php',
            array(
                'title'     => 'Lokisalle - avis',
                'h1'        => 'modifier l\'avis',
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

        if ($request->postExists('create-Avis')) {
            $avis = new Entity\Avis();
            $avis->hydrate($request->postDataArray());

            if ($avis->save()) {
                $session->addSuccess('La Avis a été enregistrée.');
                Lib\App::getRouter()->redirect('admin_avis');
            } else { // if a strange problem occurs ?
                $session->addError('Il semblerait que vos informations soient incorrectes, veuillez remplir le formulaire à nouveau. si le problème persiste, veuillez contacter l\'administrateur du site.');
                $createForm = $this
                    ->createCreateAvisForm($avis)
                    ->hydrate($request->postDataArray())
                    ->toHtml();
            }
        } else {
            $createForm = $this->createCreateAvisForm()->toHtml();
        }

        return  $this->render(
            'layout.php',
            'avis.php',
            array(
                'title'     => 'Nouvelle Avis',
                'h1'        => 'Nouvelle Avis',
                'form'      => $createForm,
            )
        );
    }

    public function deleteAction($options)
    {
        $this->filterAdmin();
        $request = Lib\App::getRequest();
        $session = Lib\App::getSession();
        $options  = explode(',', $options);
        $id       = (int) $options[0];
        $avis     = $this->getRepository('Avis')->find($id);
        $editForm = '';
        if (is_object($avis)) {
            if ($avis->delete()) {
                $session->addSuccess('La Avis à été supprimée.');
                Lib\App::getRouter()->redirect('admin_avis');
            } else { // if a strange problem occurs ?
                $session->addError('Il semblerait que vos informations soient incorrectes, veuillez remplir le formulaire à nouveau. si le problème persiste, veuillez contacter l\'administrateur du site.');
                Lib\App::getRouter()->redirect('admin_avis');
            }
        }
    }

    private function createEditAvisForm($avis)
    {
        $commentForm = new Form($avis);
        $commentForm->add(new Field\Textarea(array('name'  => 'commentaire', 'label' => 'Commentaire *')));
        $commentForm->add(new Field\Select(array('name'  => 'note', 'label' => 'Note', 'options'  => array('1', '2', '3', '4', '5', '6', '7', '8', '9', '10'))));
        $commentForm->selfHydrate()
            ->add(new Field\Submit(array('name'  => 'edit-Avis', 'value' => 'Mettre à jour')));

        return $commentForm;
    }

    private function createCreateAvisForm()
    {
        $avis = new Entity\Avis();
        $createAvisForm = new Form($avis);
        $createAvisForm
            ->selfCreate()
            ->add(new Field\Submit(array('name'  => 'create-Avis', 'value' => 'Enregistrer')));

        return $createAvisForm;
    }
}
