<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Install
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: index.tpl 9747 2012-07-26 02:08:08Z john $
 * @author     John
 */
?>

<h3>Import Tools</h3>
<p>Below is a list of supported import tools that can be utilized for importing your data into SocialEngine.</p>
<p>More info: <a href="http://support.socialengine.com/questions/211/List-of-Import-Tools-for-SocialEngine" target="_blank">See KB article</a>.</p>
<br />

<ul>
  <?php foreach( $this->importers as $importer ): ?>
  <li>
    <a class="buttonlink import_version3" href="<?php echo $this->url($importer['url']) ?>">
      <?php echo $importer['title']; ?>
    </a>
    <p class="buttontext">
      <?php echo $importer['description']; ?>
    </p>
    <br />
  </li>
  <?php endforeach; ?>
</ul>
