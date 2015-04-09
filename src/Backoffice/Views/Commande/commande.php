
<?php if (!empty($commande)) : ?>
  <section class=" section products-recap">
   <?php
    $tabCaption = 'Détails de la commande';
    $TabId = 'order-table';
    $tabElements = $commande;
    include __DIR__.'/../compoments/table.php';
   ?>


</section>
<?php else : ?>
	<div class="notice">
		<p>Il n'y a pas encore de commande sur le&nbsp;site</p>
	</div>
<?php endif; ?>
