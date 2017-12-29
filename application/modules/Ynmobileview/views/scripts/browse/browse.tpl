<?php
/**
 * @package    Ynmobileview
 * @copyright  YouNet Company
 * @license    http://auth.younetco.com/license.html
 */

?>

<div class="browse_menu">
  <ul class="navigation">
    <?php $count = 0;
      foreach( $this->navigation as $item ):
        $count++;
        $attribs = array_diff_key(array_filter($item->toArray()), array_flip(array(
        'reset_params', 'route', 'module', 'controller', 'action', 'type',
        'visible', 'label', 'href'
        )));
        
        if( !isset($attribs['active']) ){
          $attribs['active'] = false;
        }
      ?>
        <li<?php echo($attribs['active']?' class="active"':'')?>>
          <?php echo $this->htmlLink($item->getHref(), $this->translate($item->getLabel()), $attribs) ?>
        </li>
    <?php endforeach; ?>
  </ul>
</div>