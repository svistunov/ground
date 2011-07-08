<?php /*
<?php if(!empty($page['main_menu']) || !empty($page['secondary_menu'])): ?>
<nav<?php print $attributes ?>>
  <?php if(!empty($page['main_menu'])) print theme('links__system_main_menu', array('links' => $main_menu['#links'],
    'attributes' =>  $main_menu['#attributes'], 'heading' =>  $main_menu['#heading'])); ?>
  <?php if(!empty($page['secondary_menu'])) print theme('links__system_secondary_menu', array('links' => $secondary_menu['#links'],
    'attributes' =>  $secondary_menu['#attributes'], 'heading' =>  $secondary_menu['#heading'])); ?>
</nav>
<?php endif; ?>
<?php */ ?>


<?php if(!empty($page['main_menu']) || !empty($page['secondary_menu'])): ?>
<nav<?php print $attributes ?>>
  <?php print render($main_menu); ?>
  <?php print render($secondary_menu); ?>
</nav>
<?php endif; ?>
