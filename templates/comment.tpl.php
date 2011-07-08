<article<?php print $attributes; ?>>

  <header<?php print $header_attributes;?>>
    <?php print render($title_prefix); ?>
    <?php print render($render_title); ?>
    <?php print render($title_suffix); ?>
    <?php if ($new): ?>
      <mark class="new"><?php print $new ?></mark>
    <?php endif; ?>
    <?php if (!empty($unpublished)): ?>
      <mark class="unpublished"><?php print $unpublished; ?></mark>
    <?php endif; ?>
  </header>

  <?php print $picture; ?>

  <footer<?php print $footer_attributes;?>>
    <?php print $permalink; ?>
    <?php print $submitted;?>
  </footer>

  <div<?php print $content_attributes; ?>>
    <?php
      hide($content['links']);
      print render($content);
    ?>
  </div>

  <?php if ($signature): ?>
    <div<?php print $signature_attributes;?>><?php print $signature ?></div>
  <?php endif; ?>

  <?php if (!empty($content['links'])): ?>
    <nav<?php print $links_attributes?>><?php print render($content['links']); ?></nav>
  <?php endif; ?>
</article>
