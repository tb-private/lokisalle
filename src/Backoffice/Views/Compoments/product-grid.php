<?php if (!empty($productsGrid)) : ?>
    <section class="section <?php echo $productsGridClass ?> products-grid">
    <?php $i = 0; ?>
    <?php foreach ($productsGrid as $productInGrid):?><!--
        --><article class="article offer product font-style-1 <?php if (($i+1)%4 === 0) {
    echo 'fourth';
}?> <?php if (($i+1)%3 === 0) {
    echo 'third';
}?> <?php if (($i+1)%5 === 0) {
    echo 'fifth';
}?>">
            <header class="header">
                <div class="image">
                    <a title="<?php echo $productInGrid['name']; ?>" href="<?php $r->route('product_show', array('id' => $productInGrid['id'])) ?>">
                    <img title="<?php echo $productInGrid['name']; ?>" alt='Photographie de <?php echo $productInGrid['name']; ?>' src="<?php echo SITEURL ?>/images/rooms/<?php echo $productInGrid['imageUrl']; ?>" alt="<?php echo $productInGrid['name']; ?>" height="100" />
                    </a>
                </div>
                <div class="offer-actions cf">
                    <?php if ($s->connected) : ?>
                        <?php echo $productInGrid['add']; ?>
                    <?php else : ?>
                     <p><a class"login-link" href="">Connectez-vous pour l'ajouter au panier</a></p>
                    <?php endif ?>
                    <p class="price font-style-6"><?php echo $productInGrid['price']; ?> €</p>
                </div>

            </header>

            <div class="offer-description product-content">
                <a href="<?php echo $r->route('product_show', array('id' => $productInGrid['id'])); ?>">
                <h3><?php echo $productInGrid['name']; ?></h3>
                </a>
                <p class="availability">Disponible du <?php echo $productInGrid['stardDate']; ?><br/>
                au <?php echo $productInGrid['endDate'];?></p>
                <p class="city">Située à <?php echo $productInGrid['city']; ?><p>
                <p class="capacity">Pouvant acceuillir <?php echo $productInGrid['capacity']; ?> personnes</p>
            </div>
            <footer class="footer cf">

            </footer>

        </article><?php $i++; ?><!--
    --><?php endforeach; ?>
    </section>
<?php endif; ?>
