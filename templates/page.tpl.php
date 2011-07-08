<?php

/**
 * @file
 * Default theme implementation to display a single Drupal page.
 */
 //TODO: what about: print $page['page_content']? и всё
?>
<?php /*
  <div <?php print $attributes ?>>

    <?php print render($page['page_header']); ?>
    <?php print render($page['page_menu']); ?>
    <?php print render($page['page_breadcrumb']); ?>

    <?php print $messages; ?>
    <?php print render($page['help']); ?>
    
    <?php print render($page['page_content']); ?>
    
<!-- footer -->
    <?php print render($page['footer']); ?>

  </div> <!-- /#page -->
  */ ?>
  
<div <?php print $attributes ?>>
  <?php print $page_header; ?>
  <?php print $page_menu; ?>
  <?php print render($page['page_breadcrumb']); ?>
  
  <?php print $messages; ?>
  <?php print render($page['help']); ?>
  <?php print $page_content; ?>
  
  <?php print render($page['footer']); ?>
</div> <!-- /#page -->
