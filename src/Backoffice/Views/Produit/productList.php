<?php if (!empty($searchForm)) : ?>
	<div class="<?php if (empty($result)) {
    echo 'centerize';
} else {
    echo 'compact';
} ?> content-wrapper">
		<div class="content-sub-wrapper">
			<div class="content-sub-sub-wrapper">
				<div class="search-wrapper cf">
					<div class="search-wrapper">
						<h1 class="page-title"><?php echo $h1; ?></h1>
						<?php echo $searchForm; ?>
					</div>
				</div>
			</div>
		</div>
	</div>
<?php endif; ?>

<?php
    $productsGrid = $offers;
    $productsGridClass = 'list-all';
    include __DIR__.'/../compoments/product-grid.php';
?>
