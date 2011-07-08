<?php
//TODO: развернутый html
?>
<div<?php print $attributes; ?>>
  <?php print render($page['page']['highlighted']); ?>
  <<?php print $section_tag; print $section_attributes ?>>
    <?php if(!empty($page['title']) || $tasks['#access']): ?>
    <header<?php print $header_attributes;?>>
      <?php print render($page['title_prefix']) ?>
      <?php print render($title) ?>
      <?php print render($page['title_suffix']) ?>
      <?php print render($tasks) ?>
    </header>
    <div<?php print $content_attributes; ?>>
      <?php print render($page['page']['content']) ?>
    </div>
    <?php if(!empty($page['feed_icons'])): ?>
    <footer<?php print $footer_attributes;?>>
      <?php print render($page['feed_icons']) ?>
    </footer>
    <?php endif; ?>
    <?php endif; ?>
  </<?php print $section_tag ?>>
  <?php print render($page['page']['sidebar_first']); ?>
  <?php print render($page['page']['sidebar_second']); ?>
</div>
