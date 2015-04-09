<?php

namespace Backoffice\Controller;

use Controller\Controller;
use Lib;
use Entity;
use Form;
use Form\Field;

class PromotionController extends Controller
{
    public function listAction()
    {
        $this->filterAdmin();
        $session      = Lib\Session::getInstance();
        $router       = Lib\App::getRouter();
        $promotions   = $this->getRepository('Promotion')->findAll();

        $frontPromos     = array();
        if (!empty($promotions)) {
            foreach ($promotions as $promotion) {
                if ($promotion->getCodePromo() == 'aucune') {
                    continue;
                }
                $frontPromos[] = array(
                    'id'          => $promotion->getId(),
                    'code promo'  => $promotion->getCodePromo(),
                    'Réduction'   => $promotion->getReduction(),
                    ' '           => $router->getRouteLink(array('admin_promotion_edit', 'id' => $promotion->getId()), 'éditer'),
                    'supprimer'   => $router->getRouteLink(array('admin_promotion_delete', 'id' => $promotion->getId()), 'X'),
               );
            }
        }

        return  $this->render(
            'layout.php',
            'promotions.php',
            array(
                'title'     => 'Promotions',
                'h1'        => 'Toute les pormotions enregistrées',
                'promos'    => $frontPromos,
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
        $promotion     = $this->getRepository('Promotion')->find($id);
        $editForm = '';
        if (is_object($promotion)) {
            if ($request->postExists('edit-promotion')) {
                $promotion->hydrate($request->postDataArray());

                if ($promotion->update()) {
                    $session->addSuccess('Les modifications ont été enregistrées');
                    Lib\App::getRouter()->redirect('admin_promotions');
                } else { // if a strange problem occurs ?
                    $session->addError('Il semblerait que vos informations soient incorrectes, veuillez remplir le formulaire à nouveau. si le problème persiste, veuillez contacter l\'administrateur du site.');
                    $editForm = $this
                        ->createEditpromotionForm($promotion)
                        ->hydrate($request->postDataArray())
                        ->toHtml();
                }
            } else {
                $editForm = $this->createEditpromotionForm($promotion)->toHtml();
            }
        } else {
            $session->addError('Cette promotion n\'existe pas.');
            Lib\App::getRouter()->redirect('admin_promotions');
        }

        return  $this->render(
            'layout.php',
            'promotion.php',
            array(
                'title'     => 'Lokipromotion',
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

        if ($request->postExists('create-promotion')) {
            $promotion = new Entity\Promotion();
            $promotion->hydrate($request->postDataArray());

            if ($promotion->save()) {
                $session->addSuccess('La promotion a été enregistrée.');
                Lib\App::getRouter()->redirect('admin_promotions');
            } else { // if a strange problem occurs ?
                $session->addError('Il semblerait que vos informations soient incorrectes, veuillez remplir le formulaire à nouveau. si le problème persiste, veuillez contacter l\'administrateur du site.');
                $createForm = $this
                    ->createCreatepromotionForm($promotion)
                    ->hydrate($request->postDataArray())
                    ->toHtml();
            }
        } else {
            $createForm = $this->createCreatepromotionForm()->toHtml();
        }

        return  $this->render(
            'layout.php',
            'promotion.php',
            array(
                'title'     => 'Nouvelle promotion',
                'h1'        => 'Nouvelle promotion',
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
        $promotion     = $this->getRepository('Promotion')->find($id);
        $editForm = '';
        if (is_object($promotion)) {
            if ($promotion->delete()) {
                $session->addSuccess('La promotion à été supprimée.');
                Lib\App::getRouter()->redirect('admin_promotions');
            } else { // if a strange problem occurs ?
                $session->addError('Il semblerait que vos informations soient incorrectes, veuillez remplir le formulaire à nouveau. si le problème persiste, veuillez contacter l\'administrateur du site.');
                Lib\App::getRouter()->redirect('admin_promotions');
            }
        }
    }

    private function createEditpromotionForm($promotion)
    {
        $editpromotionForm = new Form\Form($promotion);
        $editpromotionForm
            ->selfCreate()
            ->selfHydrate()
            ->add(new Field\Submit(array('name'  => 'edit-promotion', 'value' => 'Mettre à jour')));

        return $editpromotionForm;
    }

    private function createCreatepromotionForm()
    {
        $promotion = new Entity\Promotion();
        $createpromotionForm = new Form\Form($promotion);
        $createpromotionForm
            ->selfCreate()
            ->add(new Field\Submit(array('name'  => 'create-promotion', 'value' => 'Enregistrer')));

        return $createpromotionForm;
    }
}
