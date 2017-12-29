<?php
?>
<div class="ynmultilisting_block">
	<?php $attr = $this -> attr;?>
    <?php for ($x = 1; $x <= $this->item->$attr; $x++): ?>
        <span class="ynmultilisting_rating_star_generic ynicon yn-star rating_star_big"></span>
    <?php endfor; ?>
    <?php if ((round($this->item->$attr) - $this->item->$attr) > 0): $x ++; ?>
        <span class="ynmultilisting_rating_star_generic ynicon yn-star rating_star_big_half"></span>
    <?php endif; ?>
    <?php if ($x <= 5) :?>
        <?php for (; $x <= 5; $x++ ) : ?>
            <span class="ynmultilisting_rating_star_generic ynicon yn-star rating_star_big_disabled"></span>
        <?php endfor; ?>
    <?php endif; ?>
    
</div>