
<?php if (!empty($promos)) : ?>
  <section class=" section rooms-recap">
   <?php
    $new =  $r->getRouteLink('admin_promotion_create', 'Ajouter une promotions ?', 'new-entity');
    $tabCaption = "Liste des Promotions existantes - $new";
    $TabId = 'promotion-table';
    $tabElements = $promos;
    include __DIR__.'/../compoments/table.php';
   ?>

</section>
<?php endif; ?>
