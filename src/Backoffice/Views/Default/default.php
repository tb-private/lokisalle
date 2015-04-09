<div class="centerize content-wrapper">
	<div class="content-sub-wrapper">
		<div class="content-sub-sub-wrapper">
			<h1 class="page-title"><?php echo $h1 ?></h1>
			<p>LokiSalle vous propose un large choix de salles de r&eacute;union de diff&eacute;rentes dimensions pouvant accueillir de 10 &agrave; 100 personnes sur Paris, Bordeaux, Marseille et Lyon.</p>
			<p>Nous disponsons de petites salles pour travailler avec vos collaborateurs et vos fournisseurs ou pour recevoir vos clients, mais aussi de tr&egrave;s grandes salle pour les grandes occasions.</p>
			<p>Toutes les salles propos&eacute;es disposent de toutes les commodit&eacute;s pour la r&eacute;ussite de vos meetings.</p>
			<p>Que ce soit pour une r&eacute;union d'une heure comme pour un s&eacute;minaire d'une journ&eacute;e voire plus, les salles de r&eacute;union LokiSalle, vous propose gratuitement la pr&eacute;sence d'une h&ocirc;tesse qui accueillera tous les participants pour les aiguiller vers la salle que vous avez r&eacute;serv&eacute;. Elle sera &agrave; votre service pour pr&eacute;parer des petits d&eacute;jeuners, sandwichs ou plateaux repas, ou encore r&eacute;server un restaurant ou un taxi.</p>
			<p>LokiSalle mets tout en &#339;uvre pour vous simplifier la vie et concourir &agrave;.
		</div>
	</div>
</div>



<?php if (!empty($lastOffers)) : ?>
<?php
    $productsGrid = $lastOffers;
    $productsGridClass = 'last-offers';
    include __DIR__.'/../compoments/product-grid.php';
?>
<?php endif; ?>
