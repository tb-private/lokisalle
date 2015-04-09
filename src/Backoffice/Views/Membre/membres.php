
<?php if (!empty($membres)) : ?>
  <section class=" section members-recap">
   <?php
    $tabCaption = $h1;
    $TabId = 'members-table';
    $tabElements = $membres;
    include __DIR__.'/../compoments/table.php';
   ?>

</section>
<?php else : ?>
	<div class="notice">
		<p>Il n'y a pas encore de membre sur le site</p>
	</div>
<?php endif; ?>
