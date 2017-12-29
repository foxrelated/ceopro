<?php

/**
 * YouNetCo
 *
 * @category   Application_Extensions
 * @package    Ynvideo
 * @author     YouNetCo
 */
?>
<div class="ynadvalbum_start_rating clearfix">
    <?php for ($x = 1; $x <= $this->subject->rating; $x++): ?>
        <span class="ynicon yn-star"></span>
    <?php endfor; ?>
    <?php if ((round($this->subject->rating) - $this->subject->rating) > 0): ?>
        <span class="ynicon yn-star-half-o"></span>
    <?php endif; ?>
    <?php
    	$disabled_star = 5 - $this->subject->rating;
    	for ($s = 1; $s <= $disabled_star; $s++):
    ?>
          <span class="ynicon yn-star ynadvalbum_disable"></span>
    <?php endfor; ?>
</div>