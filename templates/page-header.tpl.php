<?php
/*
<header <?php print $attributes ?>>
  <?php if(!empty($page['logo'])): ?>
    <a rel="home" title="<?php print t('Home page');?>" href="<?php print url('<front>');?>"><div id="logo"><img src="<?php print check_url($page['logo']) ?>" alt="<?php print check_plain(variable_get('site_name', '')) .' '. t('logo') ?>" /></div></a>
  <?php endif; ?>
  <?php if (!empty($page['site_name']) || !empty($page['site_slogan'])): ?>
  <hgroup id="name-and-slogan">
    <?php  if (!empty($page['site_name'])): ?>
      <h1 id="site-name" ><?php print l("<span>{$page['site_name']}</span>", '<front>', array('html' => true, 'attributes' => array('rel' => 'home', 'title' => t('Home')))) ?></h1>
    <?php endif; ?>
    <?php if($site_slogan['#access']): ?>
      <h2 <?php print drupal_attributes($site_slogan['#attributes']) ?>>
      <?php print $site_slogan['#value'] ?>
      </h2>
    <?php endif; ?>
  </hgroup>
  <?php endif; ?>
</header>
*/
?>

<header <?php print $attributes ?>>
  <?php print render($logo); ?>
  <?php if (!empty($page['site_name']) || !empty($page['site_slogan'])): ?>
  <hgroup<?php print drupal_attributes($group_attributes_array) ?>>
    <?php print render($site_name); ?>
    <?php print render($site_slogan); ?>
  </hgroup>
  <?php endif; ?>
</header>
