<section<?php print $attributes; ?>>
  <?php if ($content['comments'] && $title['#access']): ?>
    <header<?php print $header_attributes; ?>>
    <?php print render($title_prefix); ?>
    <?php print render($title) ?>
    <?php print render($title_suffix); ?>
    </header>
  <?php endif; ?>
  
  <div<?php print $content_attributes; ?>>
    <?php print render($content['comments']); ?>
  </div>

  <?php if ($content['comment_form']): ?>
    <footer<?php print $footer_attributes; ?>>
      <?php print render($form_title) ?>
      <?php print render($content['comment_form']); ?>
    </footer> <!-- /#comment-form -->
  <?php endif; ?>
</section> <!-- /#comments -->
