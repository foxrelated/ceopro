<?php if ($this->products) : ?>
<div class="admin-featured-products">
    <h3 class="sep">
        <span><?php echo $this->translate('Certified Products') ?></span>
    </h3>

    <div id="admin-featured-products">
        <ul>
            <?php foreach ($this->products as $product) : ?>
                <li>
                    <a href="<?php echo Engine_Settings::get('warehouse.website_url'); ?>/marketplace/app/<?php echo $product['slug']; ?>" target="_blank">
                        <img src="<?php echo $product['logo']['url']; ?>" />
                        <span>
                <?php echo $product['name']; ?>
                            <span>
                    by <?php echo $product['expert']['name']; ?>
                </span>
            </span>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>

        <div class="admin-featured-view-more" style="margin-bottom: 30px;">
            &#187; <a href="https://www.socialengine.com/marketplace/" target=="_blank">View More Certified Products</a>
        </div>
    </div>
</div>
<?php endif; ?>
