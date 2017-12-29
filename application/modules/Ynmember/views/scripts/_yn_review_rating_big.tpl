<div class="ynmember_block">
    <?php for ($x = 1; $x <= $this->rate_number; $x++): ?>
        <span style="height: 40px; width: 25px;" class="ynmember_rating_star_generic ynmember_rating_star_big"></span>
    <?php endfor; ?>
    <?php if ((round($this->rate_number) - $this->rate_number) > 0): $x ++; ?>
        <span style="height: 40px; width: 25px;" class="ynmember_rating_star_generic ynmember_rating_star_big_half"></span>
    <?php endif; ?>
    <?php if ($x <= 5) :?>
        <?php for (; $x <= 5; $x++ ) : ?>
            <span style="height: 40px; width: 25px;" class="ynmember_rating_star_generic ynmember_rating_star_big_disabled"></span>
        <?php endfor; ?>
    <?php endif; ?>
</div>