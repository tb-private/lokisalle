<?php

namespace Backoffice\Controller;

use Controller\Controller;
use Lib\Session;
use Lib;
use Entity;
use Form;

class MembreController extends Controller
{
    public function showAction($id)
    {
        $repository = $this->getRepository('Membre');
        $user = $repository->find($id);

        return  $this->render(
            'layout.php',
            'profile.php',
            array(
                'title' => 'Application',
                'user' => $user, )
            );
    }

    public function logoutAction()
    {
        $session = Session::getInstance();
        $router  = Lib\App::getRouter();
        $session->delete('user');

        if (!is_object($session->getUser())) {
            $session->addSuccess('vous êtes maintenant déconnecté');
        }
        $router->redirect('home');
    }

    public function loginAction()
    {
        $session = Lib\App::getSession();
        $request = Lib\App::getRequest();
        $router  = Lib\App::getRouter();
        $form = '';

        if (is_object($session->getUser())) {
            $link = $router->getRouteLink('logout', 'déconnecter');
            $session->addError("vous êtes déjà connecté. veuillez vous $link si vous souhaiter vous connexter avec un autre compte.");
            $router->redirect('home');
        } else {
            if ($request->postExists('login')) {
                $member = new Entity\Membre(array(
                      'pseudo' => $request->postData('pseudo'),
                      'mdp'    => $request->postData('mdp'),
                    )
                  );
                if ($member->Connexion()) {
                    if (is_object($session->getUser())) { // if user is stored in session
                            $session->addSuccess('vous êtes maintenant connecté');
                        $router->redirect('account');
                    } else { // if a strange problem occurs ?
                            $session->addError('Il semblerait que votre identifiant ou mot de passe soit incorrect, si le problème persiste, veuillez contacter l\'administrateur du site..');
                        $form = $this->addLoginForm();
                    }
                } else { //if connexion failed
                        $session->addError('Identifiant ou mot de passe incorrect.');
                    $form = $this->addLoginForm();
                }
            } else {
                $form = $this->addLoginForm();
            }
        }
        if (is_object($form)) {
            $form = $form->toHtml();
        }

        return $this->render(
            'layout.php',
            'login.php',
            array(
                'title' => 'Connexion',
                'form'   =>  $form,
            )
        );
    }

    public function registerAction()
    {
        $session = Lib\App::getSession();
        $request = Lib\App::getRequest();
        $router  = Lib\App::getRouter();
        $form = '';

        if (is_object($session->getUser())) { //shouldn't be here !
            $link = $router->getRouteLink('logout', 'déconnecter');
            $session->addError("vous êtes connecté. veuillez vous $link pour créer un compte.");
            $router->redirect('home');
        } else {
            if ($request->postExists('register')) {
                $member = new Entity\Membre($request->postDataArray());
                if ($member->save()) {
                    $member->connexion();
                    if (is_object($session->getUser())) { // if user is stored in session
                        $session->addSuccess('vous êtes maintenant inscrit, bienvenue!');
                        $router->redirect('product_list');
                    } else { // if a strange problem occurs ?
                        $session->addError('Il semblerait que vos informations soient incorrectes, veuillez remplir le formulaire à nouveau. si le problème persiste, veuillez contacter l\'administrateur du site.');
                        $form = $this->createRegisterForm();
                        $form->hydrate($request->postDataArray());
                    }
                } else {
                    $form = $this->createRegisterForm();
                    $form->hydrate($request->postDataArray());
                }
            } else {
                $form = $this->createRegisterForm();
            }
        }
        if (is_object($form)) {
            $form = $form->toHtml();
        }

        return $this->render(
            'layout.php',
            'login.php',
            array(
                'title' => 'Inscription',
                'h1' => 'Inscription nouveau membre',
                'form'  =>  $form,
            )
        );
    }

    public function accountAction()
    {
        $session = Lib\App::getSession();
        $request = Lib\App::getRequest();
        $router  = Lib\App::getRouter();
        $form = '';
        $frontBookings = '';

        if (!is_object($session->getUser())) { //shouldn't be here !
            $session->addError('vous devez vous connecter pour accèder à cette page.');
            $router->redirect('login');
        }
        if ($request->postExists('register')) {
            $member = $session->getUser();
            $member->hydrate($request->postDataArray());
            if ($member->update()) {
                $session->addSuccess('Vos informations ont bien étée modifiées');
                $form = $this->createRegisterForm();
                $form->setFieldValue('register', 'Modifier vos informations');
                $form->remove('pseudo');
                $form->hydrate($session->getUser()->getRecordable());
            } else {
                $session->addError('erreur lors de l\'enregistrement de vos informations.');
            }
        } else {
            $form = $this->createRegisterForm();
            $form->setFieldValue('register', 'Modifier vos informations');
            $form->remove('pseudo');
            $form->hydrate($session->getUser()->getRecordable());
        }
        if (is_object($form)) {
            $form = $form->toHtml();
        }
        $Crepository = $this->getRepository('Commande');
        $bookings = $Crepository->userBookings($session->getUser());

        foreach ($bookings as $booking) {
            $products = $booking->getProductsEntity();
            $titles = array();
            foreach ($products as $product) {
                $titles[] = $product->getSalleEntity()->getTitre();
            }
            $frontBookings[] = array(
                'Numéro'    => $booking->getId(),
                'Montant'   => $booking->getMontant(),
                'date'      => $booking->getDateFr(),
                'Salles réservées' => implode(',', $titles),
            );
        }

        return $this->render(
            'layout.php',
            'profile.php',
            array(
                'title' => 'Inscription',
                'h1'    => 'Vos informations',
                'form'  =>  $form,
                'bookings'  =>  $frontBookings,
            )
        );
    }
// Admin *********************************

    public function listAction()
    {
        $this->filterAdmin();
        $session      = Lib\Session::getInstance();
        $router       = Lib\App::getRouter();
        $membres   = $this->getRepository('Membre')->findAll();

        $frontMembres     = array();
        if (!empty($membres)) {
            foreach ($membres as $membre) {
                $frontMembres[] = array_merge($membre->getRecordable(), array(
                    'id'          => $membre->getId(),
                    'statut'       => $membre->getRole(),
                    'supprimer'   => $router->getRouteLink(array('admin_membre_delete', 'id' => $membre->getId()), 'X'),
               ));
            }
        }

        return  $this->render(
            'layout.php',
            'membres.php',
            array(
                'title'     => 'Membres',
                'h1'        => 'Tout les membres de Lokisalle',
                'membres'    => $frontMembres,
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
        $membre     = $this->getRepository('Membre')->find($id);
        if (is_object($membre)) {
            if ($membre->delete()) {
                $session->addSuccess('La membre à été supprimé.');
                Lib\App::getRouter()->redirect('admin_membres');
            } else { // if a strange problem occurs ?
                $session->addError('Un problème non-identifié a empêché la suppression du membre .');
                Lib\App::getRouter()->redirect('admin_membres');
            }
        }
    }

    public function resetPasswordAction()
    {
        $this->filterVisitor();
        $request = Lib\App::getRequest();
        $session = Lib\App::getSession();
        $router = Lib\App::getRouter();

        $resetForm = $this->resetPasswordForm();
        $resetForm->hydrate($request->postDataArray());

        if ($request->postExists('reset-password')) {
            if ($request->postExists('email') && filter_var($request->postData('email'), FILTER_VALIDATE_EMAIL)) {
                $repository = $this->getRepository('Membre');
                $mail       = $request->postData('email');
                $member     = $repository->findByMail($mail);
                if (is_object($member)) {
                    $newpassword = substr(strtolower(md5(uniqid(rand()))), 2, 8);
                    $member->setMdp($newpassword);
                    if ($member->update()) {
                        $author = 'lokisalle@thomasbethmont.fr';
                        $to         = $mail;
                        $subject    = 'Lokisalle - Demande de renouvellement de Mot de passe';
                        $message = "Suite a votre demande de renouvellement de mot de passe depuis le site de Lokisalle, voici votre nouveau mot de passe : $newpassword";
                        $message        .= '----'."\r\n";
                        $headers    = 'From: '.$author.' <'.$author.'>'."\r\n\r\n";
                        $success = mail($to, $subject, $message, $headers);
                        if ($success) {
                            $session->addSuccess("Le nouveau mot de passe a été envoyé à l'adresse $mail .");
                            $router->redirect('login');
                        } else {
                            $session->addError("Une erreur est survenue lors de l'envoi de l'email. Veuillez réessayer dans quelques instants.");
                        }
                    } else {
                        $session->addError("Nous ne sommes actuellement pas en mesure de renouveller le mot de passe associé à l\'adresse : $mail.");
                    }
                } else {
                    $session->addError('Cette adresse email n\'est associée a aucun compte.');
                }
            } else {
                $session->addError('Adresse email non-valide.');
            }
        }

        if (is_object($resetForm)) {
            $resetForm = $resetForm->toHtml();
        }

        return $this->render(
            'layout.php',
            'reset-password.php',
            array(
                'title' => 'Nouveau mot de passe',
                'h1' => 'Nouveau mot de passe',
                'form'  =>  $resetForm,
            )
        );
    }

// Forms *********************************

   private function addLoginForm()
   {
       $addForm = new Form\Form(new Entity\Produit());
       $addForm->add(new Form\Field\Text(array(
            'value' => 'admin',
            'name'  => 'pseudo',
            )));
       $addForm->add(new Form\Field\Password(array(
            'value' => 'ifocop123',
            'name'  => 'mdp',
            )));
       $addForm->add(new Form\Field\Submit(array(
            'value' => 'Vous connecter',
            'name' => 'login',
            )));

       return $addForm;
   }
    private function resetPasswordForm()
    {
        $resetForm = new Form\Form(new Entity\Membre());
        $resetForm->add(new Form\Field\Text(array(
            'label' => 'Email',
            'name'  => 'email',
            )));
        $resetForm->add(new Form\Field\Submit(array(
            'value' => 'renouveller le mot de passe',
            'name' => 'reset-password',
            )));

        return $resetForm;
    }

    private function createRegisterForm()
    {
        $registerForm = new Form\Form(new Entity\Membre());
        $registerForm->add(new Form\Field\Text(array(
            'label' => 'Pseudonyme',
            'name'  => 'pseudo',
            )));

        $registerForm->add(new Form\Field\Text(array(
            'label' => 'Nom',
            'name'  => 'nom',
            )));
        $registerForm->add(new Form\Field\Text(array(
            'label' => 'Prénom',
            'name'  => 'prenom',
            )));
        $registerForm->add(new Form\Field\Text(array(
            'label' => 'Email',
            'name'  => 'email',
            )));
        $registerForm->add(new Form\Field\Password(array(
            'label' => 'mot de passe',
            'name'  => 'mdp',
            )));
        $registerForm->add(new Form\Field\Select(array(
            'label' => 'Sexe',
            'name'  => 'sexe',
            'options'  => array('homme', 'femme', 'autre'),
            )));
        $registerForm->add(new Form\Field\Text(array(
            'label' => 'Adresse',
            'name'  => 'adresse',
            )));
        $registerForm->add(new Form\Field\Text(array(
            'label' => 'Ville',
            'name'  => 'ville',
            )));
        $registerForm->add(new Form\Field\Text(array(
            'label' => 'Code&nbsp;postal',
            'name'  => 'cp',
            )));
        $registerForm->add(new Form\Field\Submit(array(
            'value'  => 's\'inscrire',
            'name'  => 'register',
            )));

        return $registerForm;
    }
}
