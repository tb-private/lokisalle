<?php if (!empty($product)) : ?>
<div class="centerize content-wrapper">
	<div class="content-sub-wrapper">
		<div class="content-sub-sub-wrapper">

			<section class="section ">
				<article class="article single-product">
					<header class="header">

						<h1 class="font-size-4 page-title"><?php echo $title ?></h1>
						<div class="image" style="background-image: url(../images/rooms/<?php echo $product['imageUrl']; ?>)">

							<img class="no-display" src="<?php echo SITEURL ?>/images/rooms/<?php echo $product['imageUrl']; ?>" alt="<?php echo $product['name'].' - '.$product['city']; ?>" title="<?php echo $product['name']; ?>"/>

							<div class="price font-style-6">
								<p><?php echo $product['price']; ?> €*</p>

							</div>
							<div class="div offer-actions">
								<ul>
									<li>
										<?php if (!empty($s->connected)) : ?>
										<?php echo $product['add']; ?>
										<?php else : ?>
										<a class"login-link" href="<?php echo $r->getRoute('login')?>">Connectez-vous pour l'ajouter au&nbsp;panier</a>
										<?php endif ?>
									</li>
								</ul>
							</div>
						</div>

						<p class="note">(<strong><?php echo $product['note']?></strong>/10 sur <?php echo count($comments)?>&nbsp;avis)</p>

					</header>


						<div class="product-infos-wrapper cf">
							<div class="main-infos">
								<h2 class="font-style-4">Informations :</h2>
								<ul>
									<li>Capacité&nbsp;: <?php echo $product['capacity']; ?></li>
									<li>Catégorie&nbsp;: <?php echo $product['category']; ?></li>
									<li clas="product-detail">Date de réservation : <br>du <?php echo $product['stardDate']; ?> au <?php echo $product['endDate']; ?></li>
								</ul>
							</div>

							<div class="other-infos">
								<h2 class="font-style-4">Adresse :</h2>
								<ul>
									<li clas="product-detail"><?php echo $product['adress']; ?></li>
									<li clas="product-detail"><?php echo $product['city']; ?></li>
									<li clas="product-detail"><?php echo $product['zip']; ?></li>
									<li clas="product-detail"><?php echo $product['country']; ?></li>
								</ul>

							</div>
						</div>
					<div class="cf content">
						<div class="description">
							<?php echo $product['description']; ?>
						</div>

						<aside class="aside comments-wrapper">
							<div class="comments-sub-wrapper">
								<?php if (!empty($comments)) : ?>
								<ul class="comments-list">
								<?php foreach ($comments as $comment) : ?>
									<li class="comment font-style-5">
										<p class="comment-metas"><span><?php echo $comment['author']?></span>, le <?php echo $comment['date']?> <span class="note">(<strong><?php echo $comment['note']?></strong>/10)</span></p>
										<p class="comment-content"><?php echo $comment['comment']?></p>
									</li>
								<?php endforeach; ?>
								</ul>
								<?php else : ?>
								<p>Il n'y a pas encore de commentaire sur cette&nbsp;salle.</p>
								<?php endif; ?>
								<?php echo $commentForm; ?>
							</div>
						</aside>
					</div>
					<p class="price-notice"><em>*</em> ce prix est hors&nbsp;taxe</p>
				</article>
			</section>

		</div>
	</div>
</div>

<?php if (!empty($related)) : ?>
<?php
    $productsGrid = $related;
    $productsGridClass = 'related-offers';
    include __DIR__.'/../compoments/product-grid.php';
?>
<?php endif; //empty related ?>
<?php endif; //empty products ?>
