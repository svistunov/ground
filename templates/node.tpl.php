<article<?php print $attributes; ?>>

  <?php if ($render_title['#access']): ?>
    <header<?php print $header_attributes?>>
      <?php print render($title_prefix); ?>
      <?php print render($render_title) ?>
      <?php print render($title_suffix); ?>
    </header>
  <?php endif; ?>

  <?php if ($display_submitted || $user_picture): ?>
    <footer<?php print $footer_attributes ?>>
      <?php print $user_picture; ?>
      <?php print $submitted; ?>
    </footer>
  <?php endif; ?>

  <div<?php print $content_attributes; ?>>
  <?php
    hide($content['comments']);
    hide($content['links']);
    print render($content);
  ?>
  </div>

  <?php if (!empty($content['links'])): ?>
    <nav<?php print $nav_attributes; ?>><?php print render($content['links']); ?></nav>
  <?php endif; ?>

  <?php print render($content['comments']); ?>

</article>
