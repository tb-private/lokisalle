
<?php if (!empty($commandes)) : ?>
  <section class=" section products-recap">
   <?php
    $tabCaption = 'Liste des Commandes';
    $TabId = 'order-table';
    $tabElements = $commandes;
    include __DIR__.'/../compoments/table.php';
   ?>

  <?php
  $total = 0;
  foreach ($commandes as $commande) {
      $total += $commande['montant'];
  }?>
  <div class="notice success-list">
    <p>Le chiffre d'affaire de la société est de&nbsp;: <strong><?php echo $total ?>&nbsp;€</strong></p>
  </div>


</section>
<?php else : ?>
	<div class="notice">
		<p>Il n'y a pas encore de commande sur le&nbsp;site</p>
	</div>
<?php endif; ?>
