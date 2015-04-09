<div class="centerize content-wrapper">
  <div class="content-sub-wrapper">
    <div class="content-sub-sub-wrapper">
      <h1 class="font-size-4 page-title"><?php echo $h1; ?></h1>

      <?php if (!empty($promoForm)) : ?>
      <div class="promo-wrapper">
        <p class="promo-notice">Si vous disposez d'une code promo, veuillez le renseigner avant de valider votre panier. Pour recevoir régulièrement nos nouveaux code promo, vous pouvez vous inscrire à notre newsletter<?php $r->getRouteLink('newsletter', 'inscrire à la newsletter')?></p>
        <div class="cf">
        <?php echo $promoForm; ?>
        </div>

        <?php if (!empty($promotions)) : ?>
        <div class="promo-list-wrapper">
          <h2>Promotions validées pour ce panier</h2>
          <ul class="promo-list">
               <?php foreach ($promotions as $promotion):?>
                <li class="promotion">
                  code <span class="promo-title">"<?php echo $promotion['code'] ?>"</span>
                  <span class="promo-value"> d'une valeur de <?php echo $promotion['discount'] ?>€</span>
                  <span class="promo-product">, applicable à&nbsp;: <?php echo $promotion['product-title']?> </span>
                  <span class="promo-delete"><?php echo $promotion['delete'] ?></span>
                </li>
               <?php endforeach; ?>
            </ul>
          </div>
        <?php endif; ?>

      </div>
      <?php endif; ?>

    </div>
  </div>
</div>

<?php if (!empty($products)) : ?>
  <section class=" section cart-review">

   <?php
    $tabCaption = 'Produits que vous souhaitez réserver';
    $TabId = 'cart-review-table';
    $tabElements = $products;
    include __DIR__.'/../compoments/table.php';
   ?>

    <div class="centerize content-wrapper">
      <div class="content-sub-wrapper">
        <div class="content-sub-sub-wrapper">
          <div class="totals">
           <ul>
               <li>Prix Total HT.&nbsp;:<strong> <?php echo number_format($total, 2, ',', ' ') ?> €</strong></li>
               <li>TVA :<strong> 20%</strong></li>
               <li>Total promotions&nbsp;:<strong> <?php echo number_format($totalDicount, 2, ',', ' ') ?> €</strong></li>
               <li>Prix Total TTC. :<strong> <?php echo number_format(($total*1.2)-$totalDicount, 2, ',', ' ') ?> €</strong></li>
           </ul>
           <div class="validation-wrapper">
            <?php if (!empty($validateCartForm)) {
    echo $validateCartForm;
} ?>
           </div>

       </div>
     </div>
    </div>
  </section>
<?php endif; ?>
