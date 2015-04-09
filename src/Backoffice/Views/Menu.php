<?php

namespace Backoffice\Views;

use Lib\Session;

class Menu
{
    private $router;
    private $links = array();
    private $config;

    public function __construct()
    {
        $this->router = new \Lib\Router();
        $session      = Session::getInstance();

        $user         = $session->getUser();
        $role         = 'ROLE_VISITOR';
        if (!empty($user)) {
            $role     = $user->getRole();
        }
        switch ($role) {
            case 'ROLE_VISITOR':
                $this->links = $this->getVisitorLinks();
                break;
            case 'ROLE_USER':
                $this->links = $this->getUserLinks();
                break;
            case 'ROLE_ADMIN':
                $this->links = $this->getAdminLinks();
                break;

            default:
                $this->links = $this->getVisitorLinks();
                break;
        }
    }

    public function __toString()
    {
        return $this->toHtml();
    }

    public function toHtml()
    {
        $html = '<ul class="nav-list">';
        foreach ($this->links as $name => $params) {
            $link = $this->router->getRouteLink($params['route'], $name, $params['classes']);

            if (isset($params['submenu'])) {
                $html .= "<li class='sub-menu nav-link'>$link";
                $html .= '<ul class="sub-menu-buton">';
                foreach ($params['submenu'] as $subname => $subparams) {
                    $sublink = $this->router->getRouteLink($subparams['route'], $subname, $subparams['classes']);
                    $html .= "<li class='sub-link'>$sublink</li>";
                }
                $html .= '</ul>';
            } else {
                $html .= "<li class='nav-link'>$link";
            }

            $html .= '</li>';
        }
        $html .= '</ul>';

        return $html;
    }

    private function getVisitorLinks()
    {
        return array(
            'Accueil'          => array('route' => 'home', 'classes' => 'fa fa-home'),
            'Réservation'      => array('route' => 'product_list', 'classes' => 'fa fa-flag'),
            'Recherche'        => array('route' => 'product_search', 'classes' => 'fa fa-search'),
            'Créer un compte'  => array('route' => 'register', 'classes' => 'fa fa-user'),
            'Se connecter'     => array('route' => 'login', 'classes' => 'fa fa-plug'),
        );
    }

    private function getUserLinks()
    {
        return array(
            'Accueil'           => array('route' => 'home', 'classes' => 'fa fa-home'),
            'Réservation'       => array('route' => 'product_list', 'classes' => 'fa fa-flag'),
            'Recherche'         => array('route' => 'product_search', 'classes' => 'fa fa-search'),
            'Voir votre Panier' => array('route' => 'cart', 'classes' => 'fa fa-shopping-cart'),
            'Profil' => array('route' => 'account', 'classes' => 'fa fa-user'),
            'Se déconnecter'    => array('route' => 'logout', 'classes' => 'fa fa-plug'),
        );
    }

    private function getAdminLinks()
    {
        return array(
            'Accueil'          => array('route' => 'home', 'classes' => 'fa fa-home'),
            'Produits disponibles' => array('route' => 'product_list', 'classes' => 'fa fa-flag'),
            'Recherche'        => array('route' => 'product_search', 'classes' => 'fa fa-search'),
            'Voir votre Panier' => array('route' => 'cart', 'classes' => 'fa fa-shopping-cart'),
            'Profil' => array('route' => 'account', 'classes' => 'fa fa-user'),
            'Se déconnecter'   => array('route' => 'logout', 'classes' => 'fa fa-power-off'),
            'Administration'   => array(
                'route' => 'admin_home',
                'classes' => 'admin fa fa-bar-chart',
                'submenu' => array(
                    'Salles'       => array('route' => 'admin_salles',     'classes' => 'fa fa-bar-chart'),
                    'Avis'         => array('route' => 'admin_avis',       'classes' => 'fa fa-bar-chart'),
                    'Produits'     => array('route' => 'admin_produits',   'classes' => 'fa fa-bar-chart'),
                    'Promos'       => array('route' => 'admin_promotions', 'classes' => 'fa fa-bar-chart'),
                    'Membres'      => array('route' => 'admin_membres',    'classes' => 'fa fa-bar-chart'),
                    'Statistiques' => array('route' => 'admin_stats',      'classes' => 'fa fa-bar-chart'),
                    'Commandes'    => array('route' => 'admin_commandes',  'classes' => 'fa fa-bar-chart'),
                    'Newsletters'  => array('route' => 'admin_newsletter', 'classes' => 'fa fa-bar-chart'),
                    ),
                ),
        );
    }
}
