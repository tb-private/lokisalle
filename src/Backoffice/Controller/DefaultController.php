<?php

namespace Backoffice\Controller;

use Controller\Controller;
use Lib\Session;
use Lib;
use Entity;
use Form\Form;
use Form\Field;

class DefaultController extends Controller
{
    public function indexAction()
    {
        $products   = $this->getRepository('Produit')->findAllAvailable(3);
        $offers     = array();
        $session    = Session::getInstance();

        foreach ($products as $product) {
            $roomId = $product->getSalle();
            $room = $this->getRepository('Salle')->find($roomId);
            $form = $addForm = $this->createAddForm($product);
            $offers[] = array(
            'id'        => $product->getId(),
            'name'      => $room->getTitre(),
            'imageUrl'  => $room->getPhoto(),
            'stardDate' => $product->getDateArrivee(),
            'endDate'   => $product->getDateDepart(),
            'city'      => $room->getVille(),
            'price'     => $product->getPrix(),
            'capacity'  => $room->getCapacite(),
            'add'       => $form->toHtml(),
           );
        }

        return $this->render(
            'layout.php',
            'default.php',
            array(
                'title'      => 'Lokisalle',
                'h1'         => 'Lokisalle',
                'lastOffers' => $offers,
                'user'       => $session->getUser(),
            )
        );
    }

    public function imprintAction()
    {
        return $this->render(
            'layout.php',
            'imprint.php',
            array(
                'title'      => 'Mentions légales',
                'h1'         => 'Mentions légales',
            )
        );
    }
    public function termsAction()
    {
        return $this->render(
            'layout.php',
            'terms.php',
            array(
                'title'      => 'Conditions Générales de Ventes',
                'h1'         => 'Conditions Générales de Ventes',
            )
        );
    }
    public function sitemapAction()
    {
        return $this->render(
            'layout.php',
            'sitemap.php',
            array(
                'title'      => 'sitemap',
                'h1'         => 'Plan du site',
            )
        );
    }

    public function noRouteAction()
    {
        $products   = $this->getRepository('Produit')->findAllAvailable(2);
        $offers     = array();
        $session    = Session::getInstance();
        $session->addError('La page demandée nest introuvable.');

        foreach ($products as $product) {
            $roomId = $product->getSalle();
            $room = $this->getRepository('Salle')->find($roomId);
            $form = $addForm = $this->createAddForm($product);
            $offers[] = array(
            'id'        => $product->getId(),
            'name'      => $room->getTitre(),
            'imageUrl'  => $room->getPhoto(),
            'stardDate' => $product->getDateArrivee(),
            'endDate'   => $product->getDateDepart(),
            'city'      => $room->getVille(),
            'price'     => $product->getPrix(),
            'capacity'  => $room->getCapacite(),
            'add'       => $form->toHtml(),
           );
        }

        return $this->render(
            'layout.php',
            '404.php',
            array(
                'title'      => 'Lokisalle',
                'lastOffers' => $offers,
                'user'       => $session->getUser(),
            )
        );
    }

    public function contactAction()
    {
        $request    = Lib\App::getRequest();
        $session    = Lib\App::getSession();
        $router    = Lib\App::getRouter();
        $contactForm = $this->createContactForm();

        if ($request->postExists('contact')) {
            $mail = htmlspecialchars(stripslashes($request->postData('mail')));
            $message = htmlspecialchars(
                stripslashes(
                    nl2br($request->postData('message'))
                    )
                );

            if (!filter_var($mail, FILTER_VALIDATE_EMAIL)) {
                $session->addError('Veuillez indique une adresse mail valide');
                $contactForm->hydrate($request->postDataArray());
            } elseif (empty($message)) {
                $session->addError('Veuillez rentrer un message!');
                $contactForm->hydrate($request->postDataArray());
            } else {
                $msg = '';
                if ($session->connected) {
                    $name = $session->getUser()->getNom().' '.$session->getUser()->getPrenom();
                } else {
                    $name = 'Visiteur';
                }
                $to = 'thomas.bethmont@gmail.com';
                $subject = 'Lokisalle - demande de contact';
                $msg .= 'Mail envoyé depuis lokisalle par '.$mail."\r\n";
                $msg .= '----'."\r\n";
                $msg .= $message."\r\n";
                $msg .= '----'."\r\n";
                $headers = 'From: '.$name.' <'.$mail.'>'."\r\n\r\n";

                mail($to, $subject, $msg, $headers);
                $session->addSuccess('Votre message à bien été enregistré. Merci de voter intéret pour Lokisalle.');
                $router->redirect('product_list');
            }
        }

        return  $this->render(
            'layout.php',
            'contact.php',
            array(
                'title'     => 'Contact',
                'h1'        => 'Nous contacter',
                'user'      => $session->getUser(),
                'contactForm' => $contactForm->toHtml(),
            )
        );
    }

    public function createContactForm()
    {
        $router = Lib\App::getRouter();
        $commentForm = new Form(new Entity\Avis());
        $commentForm->add(new Field\Text(array('name'  => 'mail', 'label'  => 'Votre email')));
        $commentForm->add(new Field\Textarea(array('name'  => 'message')));
        $commentForm->add(new Field\Submit(array('name'  => 'contact', 'value' => 'Envoyer le message')));

        return $commentForm;
    }

    public function createAddForm($product)
    {
        $addForm = new Form(new Entity\Produit());
        $router  = new Lib\Router();
        $url     = $router->getRoute('cart_add');
        $addForm->setAction($url);
        $addForm->add(new Field\Hidden(array('name'  => 'id', 'value' => $product->getId())));
        $addForm->add(new Field\Submit(array('name'  => 'add-to-cart', 'value' => 'Ajouter au panier')));

        return $addForm;
    }
}
