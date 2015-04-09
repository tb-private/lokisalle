
<?php if (!empty($products)) : ?>
  <section class=" section products-recap">
   <?php
    $new =  $r->getRouteLink('admin_produit_create', 'Ajouter un produit ?', 'new-entity');

    $tabCaption = "Liste des Produits - $new";
    $TabId = 'products-table';
    $tabElements = $products;
    include __DIR__.'/../compoments/table.php';
   ?>

</section>
<?php else : ?>
	<div class="notice">
		<p>Il n'y a pas encore de produits sur le site</p>
	</div>
<?php endif; ?>
