<!DOCTYPE html>
<!--[if lt IE 9]>      <html class="no-js lt-ie9" lang="fr-FR"> <![endif]-->
<!--[if IE 9]>         <html class="no-js ie9" lang="fr-FR"> <![endif]-->
<!--[if gt IE 9]><!--> <html class="no-js" lang="fr-FR"> <!--<![endif]-->
<head>
	<meta charset="utf-8">
	<title><?php echo $title ?></title>
	<meta name="viewport" content="width=device-width">
	<link rel="icon" type="image/png" href="<?php echo SITEURL ?>/favicon.png" />
	<link rel="profile" href="http://gmpg.org/xfn/11" />
	<!--[if lt IE 9]>
	<script src="//html5shiv.googlecode.com/svn/trunk/html5.js"></script>
	<![endif]-->
	<link href="//maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css" rel="stylesheet">
	<link rel="stylesheet" href="<?php echo SITEURL ?>/css/jquery-ui.css" />
	<link rel="stylesheet" href="<?php echo SITEURL ?>/css/jquery-ui.structure.css" />
	<link rel="stylesheet" href="<?php echo SITEURL ?>/css/jquery-ui.theme.css" />
	<!-- <link rel="stylesheet" href="<?php //echo SITEURL ?>/css/footable.standalone.css" />
	<link rel="stylesheet" href="<?php //echo SITEURL ?>/css/footable.core.css" />

 -->
	<link rel="stylesheet" href="<?php echo SITEURL ?>/css/style.css" />
 	<script type="text/javascript" src="<?php echo SITEURL ?>/js/vendor/jquery-1.9.0.min.js"></script>
 	<script type="text/javascript" src="<?php echo SITEURL ?>/js/vendor/jquery-ui.min.js"></script>
 	<script type="text/javascript" src="<?php echo SITEURL ?>/js/vendor/footable.js"></script>
 	<script type="text/javascript" src="<?php echo SITEURL ?>/js/scripts.js"></script>
</head>
<body class="font-style-1 <?php echo $bodyClasses; ?>">
	<div id="main">
		<div class="sub-wrapper">
			<header class='header main-header'>
				<nav class='nav'>
					<?php echo new Backoffice\Views\Menu(); ?>
				</nav>
			</header>

			<div class="main-content">
				<?php echo $content ?>
			</div>

			<?php $s->html() ?>
			<footer class='footer main-footer'>
				<nav class='nav'>
					<?php echo new Backoffice\Views\Footer(); ?>
				</nav>
			</footer>
	</div><!-- /#main -->
</body>
