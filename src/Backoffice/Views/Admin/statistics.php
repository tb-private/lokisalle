
<?php if (!empty($TopNoteRooms)) : ?>
  <section class=" section rooms-recap">
   <?php
    $tabCaption = 'Top 5 des salles les mieux notées';
    $TabId = 'top-note-table';
    $tabElements = $TopNoteRooms;
    include __DIR__.'/../compoments/table.php';
   ?>
	</section>
<?php endif; ?>

<?php if (!empty($topSold)) : ?>
  <section class=" section top-sold-recap">
   <?php
    $tabCaption = 'Top 5 des salles les plus vendues';
    $TabId = 'top-sold-table';
    $tabElements = $topSold;
    include __DIR__.'/../compoments/table.php';
   ?>
</section>
<?php endif; ?>

<?php if (!empty($topBookers)) : ?>
  <section class=" section top-bookers-recap">
   <?php
    $tabCaption = 'Top 5 des membres aillant le plus commandé';
    $TabId = 'top-bookers-table';
    $tabElements = $topBookers;
    include __DIR__.'/../compoments/table.php';
   ?>
</section>
<?php endif; ?>
<?php if (!empty($topBuyers)) : ?>
  <section class=" section top-buyers-recap">
   <?php
    $tabCaption = 'Top 5 des membres ayant le plus dépensé';
    $TabId = 'top-buyers-table';
    $tabElements = $topBuyers;
    include __DIR__.'/../compoments/table.php';
   ?>
</section>
<?php endif; ?>
