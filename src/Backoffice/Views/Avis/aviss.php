
<?php if (!empty($promos)) : ?>
  <section class=" section comments-recap">
   <?php
    $tabCaption = 'Liste des Avis';
    $TabId = 'comment-table';
    $tabElements = $promos;
    include __DIR__.'/../compoments/table.php';
   ?>

</section>
<?php else : ?>
	<div class="notice">
		<p>Il n'y a pas encore d'avis sur le site</p>
	</div>
<?php endif; ?>
