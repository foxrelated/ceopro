<?php
?>
<div class="ynmultilisting_block">
    <?php for ($x = 1; $x <= $this->value; $x++): ?>
        <span class="ynmultilisting_rating_star_generic ynicon yn-star rating_star_big"></span>
    <?php endfor; ?>
    <?php if ((round($this->value) - $this->value) > 0): $x ++; ?>
        <span class="ynmultilisting_rating_star_generic ynicon yn-star rating_star_big_half"></span>
    <?php endif; ?>
    <?php if ($x <= 5) :?>
        <?php for (; $x <= 5; $x++ ) : ?>
            <span class="ynmultilisting_rating_star_generic ynicon yn-star rating_star_big_disabled"></span>
        <?php endfor; ?>
    <?php endif; ?>
    
</div>