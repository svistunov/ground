<?php print $doctype; ?>
<!-- paulirish.com/2008/conditional-stylesheets-vs-css-hacks-answer-neither/ -->
<!--[if lt IE 7 ]> <?php print _ground_html_conditional($language, $rdf, $head_classes, 'ie6') ?> <![endif]-->
<!--[if IE 7 ]>    <?php print _ground_html_conditional($language, $rdf, $head_classes, 'ie7') ?> <![endif]-->
<!--[if IE 8 ]>    <?php print _ground_html_conditional($language, $rdf, $head_classes, 'ie8') ?> <![endif]-->
<!--[if (gte IE 9)|!(IE)]><!--> <?php print _ground_html_conditional($language, $rdf, $head_classes, '') ?> <!--<![endif]-->
<head<?php print $rdf->profile; ?>>
  <?php print $head; ?>
  <title><?php print $head_title; ?></title>  
  <?php print $styles; ?>
  <?php print $scripts; ?>
</head>

<body<?php print $attributes;?>>
  <div id="skip-link">
    <a href="#main-content" class="element-invisible element-focusable"><?php print t('Skip to main content'); ?></a>
  </div>
  <?php print $page_top; ?>
  <?php print $page; ?>
  <?php print $page_bottom; ?>
</body>
</html>
