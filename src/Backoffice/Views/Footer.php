<?php

namespace Backoffice\Views;

use Lib\Session;

class Footer
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
            $html .= "<li class='nav-link'>$link</li>";
        }
        $html .= '</ul>';

        return $html;
    }

    private function getVisitorLinks()
    {
        return array(
            'Mentions légales'    => array('route' => 'cms_imprint', 'classes' => 'link'),
            'C.G.V.'              => array('route' => 'cms_terms', 'classes' => 'link'),
            'Plan du site'        => array('route' => 'sitemap', 'classes' => 'link'),
            'imprimer cette page' => array('route' => 'void', 'classes' => 'link print-link'),
            'Contact'             => array('route' => 'contact', 'classes' => 'link'),
        );
    }

    private function getUserLinks()
    {
        return array(
            'Mentions légales'      => array('route' => 'cms_imprint', 'classes' => 'link'),
            'C.G.V.'                => array('route' => 'cms_terms', 'classes' => 'link'),
            'Plan du site'          => array('route' => 'sitemap', 'classes' => 'link'),
            'imprimer cette page'   => array('route' => 'void', 'classes' => 'link print-link'),
            'Newsletter'            => array('route' => 'newsletter_subscribe', 'classes' => 'link'),
            'Contact'               => array('route' => 'contact', 'classes' => 'link'),
        );
    }

    private function getAdminLinks()
    {
        return array(
            'Mentions légales'      => array('route' => 'cms_imprint', 'classes' => 'link'),
            'C.G.V.'                => array('route' => 'cms_terms', 'classes' => 'link'),
            'Plan du site'          => array('route' => 'sitemap', 'classes' => 'link'),
            'imprimer cette page'   => array('route' => 'void', 'classes' => 'link print-link'),
            'Newsletter'            => array('route' => 'newsletter_subscribe', 'classes' => 'link'),
            'Contact'               => array('route' => 'contact', 'classes' => 'link'),
        );
    }
}
