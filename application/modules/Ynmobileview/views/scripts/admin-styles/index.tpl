<?php
/**
 * @package    Ynmobileview
 * @copyright  YouNet Company
 * @license    http://auth.younetco.com/license.html
 */

?>
<h2><?php echo $this->translate("YouNet Mobile View Plugin") ?></h2>


<?php if( count($this->navigation) ): ?>
  <div class='tabs'>
    <?php
      echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
  </div>
<?php endif; ?>
<br />

<div class='clear'>
  <div class='settings'>
    <form class="global_form">
      <div>
      <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'ynmobileview', 'controller' => 'styles', 'action' => 'add'), $this->translate('Add New Style'), array(
      	'class' => 'smoothbox buttonlink',
      	'style' => 'background-image: url(' . $this->layout()->staticBaseUrl . 'application/modules/Core/externals/images/admin/new_category.png);')) ?>
      <br/>
      <?php if(count($this->styles) > 0):?>
      
      <table class='admin_table' style="padding-top: 10px">
        <thead>

          <tr>
            <th style="width: 60%"><?php echo $this->translate("Name") ?></th>
            <th style="width: 20%"><?php echo $this->translate("Status") ?></th>
            <th><?php echo $this->translate("Options") ?></th>
          </tr>

        </thead>
        <tbody>
          <?php foreach ($this->styles as $style): ?>

          <tr>
            <td><?php echo $style->title; ?></td>
            <td>
            	<?php if($style->active == 1)
				{
					echo $this->translate("Active"); 
				}
				?>
            </td>
            <td>
           		<?php 
           		if($style->active == 0)
				{
           			echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'ynmobileview', 'controller' => 'styles', 'action' => 'make-default', 'id' => $style->style_id), $this->translate('make default'), array());
           		}
				else 
				{
					echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'ynmobileview', 'controller' => 'styles', 'action' => 'remove-default', 'id' => $style->style_id), $this->translate('remove default'), array());
				}
				?>
              |
              <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'ynmobileview', 'controller' => 'styles', 'action' => 'edit', 'id' => $style->style_id), $this->translate('edit'), array(
              )) ?>
              |
              <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'ynmobileview', 'controller' => 'styles', 'action' => 'delete', 'id' => $style->style_id), $this->translate('delete'), array(
                'class' => 'smoothbox',
              )) ?>
            </td>
          </tr>

          <?php endforeach; ?>

        </tbody>
      </table>
      <?php else:?>
      <br/>
      <div class="tip">
      <span><?php echo $this->translate("There are currently no styles.") ?></span>
      </div>
      <?php endif;?>
      </div>
    </form>
  </div>
</div>
