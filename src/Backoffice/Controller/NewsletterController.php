<?php

namespace Backoffice\Controller;

use Controller\Controller;
use Lib;
use Entity;
use Form\Form;
use Form\Field;

class NewsletterController extends Controller
{
 public function subscribeAction()
 {
     $this->filterUser();
     $request    = Lib\App::getRequest();
     $session    = Lib\App::getSession();
     $user = $session->getUser();
     $router     = Lib\App::getRouter();
     $repository = $this->getRepository('Newsletter');
     $checkSubscribed = $repository->findMembre($user);

     $subscribeForm = $this->createSubscribeForm();
     if ($checkSubscribed) {
         $subscribeForm->setFieldValue('subscribe', 'se désabonner');
     }

     if ($request->postExists('subscribe')) {
         $id = $user->getId();
         $newsletter        = new Entity\Newsletter();
         $newsletter->setMembreId($id);
         if (!$checkSubscribed) {
             if ($newsletter->save()) {
                 $session->addSuccess('Votre est maintenant abonné, Merci!');
             } else { // if a strange problem occurs ?
                    $session->addError('Erreur lors de l\'enregistrement. si le problème persiste, veuillez contacter l\'administrateur du site.');
                 $subscribeForm = $this->createSubscribeForm();
             }
         } else {
             if ($repository->unsubscribe($user)) {
                 $session->addSuccess('Votre est maintenant désabonné.');
             } else { // if a strange problem occurs ?
                    $session->addError('Erreur lors de l\'opération. si le problème persiste, veuillez contacter l\'administrateur du site.');
                 $subscribeForm = $this->createSubscribeForm();
             }
         }
     }
     $checkSubscribed = $repository->findMembre($user);

     $subscribeForm = $this->createSubscribeForm();
     if ($checkSubscribed) {
         $subscribeForm->setFieldValue('subscribe', 'se désabonner');
     }

     return  $this->render(
            'layout.php',
            'subscribe.php',
            array(
                'title'     => 'Newsletter',
                'h1'        => 'S\'abonner à la newsletter',
                'form'      => $subscribeForm->toHtml(),
            )
        );
 }

    public function createAction()
    {
        $this->filterAdmin();
        $request    = Lib\App::getRequest();
        $session    = Lib\App::getSession();
        $router     = Lib\App::getRouter();
        $newsletterForm = $this->createNewsletterForm();

        if ($request->postExists('newsletter')) {
            $author = htmlspecialchars(stripslashes($request->postData('author')));
            $subject = htmlspecialchars(stripslashes($request->postData('subject')));
            $message = htmlspecialchars(
                stripslashes(
                    nl2br($request->postData('message'))
                    )
                );

            if (!filter_var($author, FILTER_VALIDATE_EMAIL)) {
                $session->addError('Veuillez indiquer une adresse mail valide');
                $newsletterForm->hydrate($request->postDataArray());
            } elseif (empty($subject)) {
                $session->addError('Veuillez indiquer un objet ');
                $newsletterForm->hydrate($request->postDataArray());
            } elseif (empty($message)) {
                $session->addError('Veuillez rentrer un message!');
                $newsletterForm->hydrate($request->postDataArray());
            } else {
                $msg = '';

                $mails    = $this->getRepository('Newsletter')->getMails();
                if (empty($mails)) {
                    $session->addError('Vous n\'avez pas ecnore de membre inscrit à la newsletter');
                    $newsletterForm->hydrate($request->postDataArray());
                } else {
                    $successCount = 0;
                    $failCount = 0;
                    foreach ($mails as $mail) {
                        $to         = $mail['email'];
                        $msg        .= 'Mail envoyé depuis lokisalle par '.$author."\r\n";
                        $msg        .= '----'."\r\n";
                        $msg        .= $message."\r\n";
                        $msg        .= '----'."\r\n";
                        $headers    = 'From: '.$author.' <'.$author.'>'."\r\n\r\n";

                        $success = mail($to, $subject, $msg, $headers);
                        if ($success) {
                            $successCount++;
                        } else {
                            $failCount++;
                        }
                    }
                    if ($successCount) {
                        $session->addSuccess("Votre message à bien été envoyé $successCount fois.");
                    }
                    if ($failCount) {
                        $session->addError("Il y a eut $failCount erreurs lors de l'envoi.");
                    }
                }
            }
        }

        return  $this->render(
            'layout.php',
            'create.php',
            array(
                'title'     => 'Envoyer une newsletter',
                'h1'        => 'Rédaction d\'une newsletter',
                'user'      => $session->getUser(),
                'form'      => $newsletterForm->toHtml(),
            )
        );
    }

    public function createNewsletterForm()
    {
        $router = Lib\App::getRouter();
        $NewsletterForm = new Form(new Entity\Membre());
        $NewsletterForm->add(new Field\Text(array('name'  => 'author', 'label'  => 'Expéditeur')));
        $NewsletterForm->add(new Field\Text(array('name'  => 'subject',  'label'  => 'Sujet')));
        $NewsletterForm->add(new Field\Textarea(array('name'  => 'message')));
        $NewsletterForm->add(new Field\Submit(array('name'  => 'newsletter', 'value' => 'Envoyer le message aux membres')));

        return $NewsletterForm;
    }

    public function createSubscribeForm()
    {
        $subscribeForm = new Form(new Entity\Newsletter());
        $subscribeForm->add(new Field\Submit(array('name'  => 'subscribe', 'value' => 's\'abonner')));

        return $subscribeForm;
    }
}
