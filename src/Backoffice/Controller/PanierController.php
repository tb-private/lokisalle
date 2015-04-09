<?php

namespace Lokisalle\Bundle\LokiBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Request;
use Lokisalle\Bundle\LokiBundle\Entity\Promotion;
use Lokisalle\Bundle\LokiBundle\Form\PromotionPanierType;

class PanierController extends Controller
{
    private $session;

    public function indexAction(Request $request)
    {
        $this->session = $request->getSession();
        $this->createPanier();
        $panier = $this->renderPanier();

        $entity = new Promotion();
        $form = $this->testPromoForm($entity);

        return $this->render(
        'LokisalleLokiBundle:Default:panier.html.twig',
        array('panier' => $panier,
              'promo' => $form->createView(), )

      );
    }

    public function addAction(Request $request, $id)
    {
        $this->session = $request->getSession();
        $this->createPanier();

    //verifier que le produit existe
      $em = $this->getDoctrine()->getManager();
        $produit = $em->getRepository('LokisalleLokiBundle:Produit')->findOneById($id);

    //si oui  && que le produit n'est pas commandé (etat = 0), l'ajouter au panier
      if (!empty($produit) && $produit->getEtat() == 0) {
          $this->addPanier($id);
      }

        return $this->redirect($this->generateUrl('panier'));
    }

    public function deleteOneAction(Request $request, $id)
    {
        $this->session = $request->getSession();
        $em = $this->getDoctrine()->getManager();
        $produit = $em->getRepository('LokisalleLokiBundle:Produit')->findOneById($id);

      //si oui, retirer du panier
      if (!empty($produit)) {
          $this->deletePanier($id);
      }

        return $this->redirect($this->generateUrl('panier'));
    }

    public function clearAction(Request $request)
    {
        $session = $request->getSession();
        $session->clear();

        return $this->redirect($this->generateUrl('lokisalle_loki_homepage'));
    }

// --------------------------
//   Fonctions du controller
// --------------------------

    public function createPanier()
    {
        if (!$this->session->has('panier')) {
            $this->session->set('panier', array());
        }
    }

    public function addPanier($id)
    {
        $panier = $this->session->get('panier');
        if (array_search($id, $panier) === false) {
            $panier[] = $id;
            $this->session->set('panier', $panier);

            return true;
        }

        return false;
    }

    public function deletePanier($id)
    {
        $panier = $this->session->get('panier');
        $search = array_search($id, $panier);
        if ($search !== false) { // $search deviens false si array search ne trouve rien.
          array_splice($panier, $search, 1);
            $this->session->set('panier', $panier);

            return true;
        }

        return false;
    }

    public function renderPanier()
    {
        $panier = $this->session->get('panier');

        $repositoryProduit = $this->getDoctrine()->getManager()->getRepository('LokisalleLokiBundle:Produit');
        $render = array();

        foreach ($panier as $key => $id) {
            $produit = $repositoryProduit->findOneById($id);
            $salle = $produit->getSalle();

            $render[] = array(
        'Produit' => $produit->getId(),
        'Salle' => $salle->getTitre(),
        'Photo' => $salle->getPhoto(),
        'Ville' => $salle->getVille(),
        'Capacité' => $salle->getCapacite(),
        'DateArrivee' => $produit->getDateArrivee(),
        'DateDepart' => $produit->getDateDepart(),
        'Prix' => $produit->getPrix(),
        );
        }

        return $render;
    }

    public function testPromoAction(Request $request)
    {
        $entity = new Promotion();
        $form = $this->testPromoForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $test = $entity->getId();
            if ($test != false) {
                $test = $entity->getId();
                exit();
            }
        }
        echo 'raté';
        exit();

        return $this->render('LokisalleLokiBundle:Default:panier.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a Promotion entity.
     *
     * @param Promotion $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function testPromoForm(Promotion $entity)
    {
        $form = $this->createForm(new PromotionPanierType(), $entity, array(
            'action' => $this->generateUrl('test_promo'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Appliquer'));

        return $form;
    }
}
