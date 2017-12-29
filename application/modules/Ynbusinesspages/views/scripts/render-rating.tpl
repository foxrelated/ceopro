<?php if ($this->li) : ?>
<li class="business-rating">
    <div>
<?php endif; ?>

    <?php $x = 1; ?>

    <?php for (; $x <= $this->rating; $x++): ?>
        <span class="ynbusinesspages_rating_star_generic ynicon yn-star"></span>
    <?php endfor; ?>

    <?php if ((round($this->rating) - $this->rating) > 0): $x ++; ?>
        <span class="ynbusinesspages_rating_star_generic ynicon yn-star-half-o"></span>
    <?php endif; ?>

    <?php if ($x <= 5) :?>
        <?php for (; $x <= 5; $x++ ) : ?>
            <span class="ynbusinesspages_rating_star_generic ynicon yn-star rating_start_disable"></span>
        <?php endfor; ?>
    <?php endif; ?>

<?php if ($this->li) : ?>
    </div>
</li>
<?php endif; ?>