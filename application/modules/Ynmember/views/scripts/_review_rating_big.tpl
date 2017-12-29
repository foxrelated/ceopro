<div class="ynmember_block">
    <?php for ($x = 1; $x <= $this->rate_number; $x++): ?>
        <span class="ynicon ynmember_rating_star_generic yn-star"></span>
    <?php endfor; ?>
    <?php if ((round($this->rate_number) - $this->rate_number) > 0): $x ++; ?>
        <span class="ynicon ynmember_rating_star_generic yn-star-half-o"></span>
    <?php endif; ?>
    <?php if ($x <= 5) :?>
        <?php for (; $x <= 5; $x++ ) : ?>
            <span class="ynicon ynmember_rating_star_generic yn-star ynmember_star_disable"></span>
        <?php endfor; ?>
    <?php endif; ?>
</div>