<?php

/**
 * @file
 * Default theme implementation to display a region.
 */
?>
<?php if ($content): ?>
  <section<?php print $attributes ?>>
    <h2<?php print $title_attributes?>><?php print $title ?></h2>
    <?php print $content; ?>
  </section>
<?php endif; ?>
