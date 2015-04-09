
<?php if (!empty($frontRooms)) : ?>
  <section class=" section rooms-recap">
   <?php
    $new =  $r->getRouteLink('admin_salle_create', 'Ajouter une salle ?', 'new-room');
    $tabCaption = "Liste des Salles existantes - $new";
    $TabId = 'rooms-table';
    $tabElements = $frontRooms;
    include __DIR__.'/../compoments/table.php';
   ?>

</section>
<?php endif; ?>
