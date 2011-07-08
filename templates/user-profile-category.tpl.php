<?php
// $Id: user-profile-category.tpl.php,v 1.1.2.2 2010/10/14 05:36:19 jmburnz Exp $
?>
<section<?php print $attributes?>>
  <?php print render($render_title); ?>
  <dl<?php print $content_attributes; ?>>
    <?php print $profile_items; ?>
  </dl>
</section>
