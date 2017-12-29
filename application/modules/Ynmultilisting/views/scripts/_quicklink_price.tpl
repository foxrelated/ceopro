<div id="prices-wrapper" class="form-wrapper">
	<div id="prices-label" class="form-label">
	    <label>
            <?php echo $this->translate('Price');?>
        </label>
	</div>
	<div id="prices-element" class="form-element">
	    <table id="prices-table">
	        <tr class="label">
	            <td><?php echo $this->translate('Price from')?></td>
	            <td><?php echo $this->translate('to')?></td>
	        </tr>
	        <?php if (isset($this->params['price-from'])) : ?>
	        <?php foreach ($this->params['price-from'] as $index=>$from) : ?>
	        <tr class="prices-input">
                <td><input type="text" class="price-from" name="price-from[]" value="<?php echo $from?>"/></td>
                <td><input type="text" class="price-to" name="price-to[]" value="<?php echo $this->params['price-to'][$index]?>" /></td>
                <td><select name="price-currency[]">
                <?php foreach ($this->currencies as $key => $value):?>
                   <option value="<?php echo $key?>" <?php if ($key == $this->params['price-currency'][$index]) echo 'selected'?>><?php echo $this->translate($value)?></option>
                <?php endforeach;?>
                </select></td>
                <td <?php if ($index == 0) echo 'id="add-more-price"'?> class="price-action">
                    <i class="fa <?php echo ($index == 0) ? 'fa-plus' : 'fa-minus';?>"></i>
                </td>
            </tr>
	        <?php endforeach;?>
	        <?php elseif($this->quicklink) : ?>
	        <?php $count = 0;?>
	        <?php foreach ($this->quicklink->price as $price) : ?>
	        <?php $price = unserialize($price);?>
            <tr class="prices-input">
                <td><input type="text" class="price-from" name="price-from[]" value="<?php if (isset($price['from'])) echo $price['from'];?>"/></td>
                <td><input type="text" class="price-to" name="price-to[]" value="<?php if (isset($price['to'])) echo $price['to'];?>" /></td>
                <td><select name="price-currency[]">
                <?php foreach ($this->currencies as $key => $value):?>
                   <option value="<?php echo $key?>" <?php if ($key == $price['currency']) echo 'selected'?>><?php echo $this->translate($value)?></option>
                <?php endforeach;?>
                </select></td>
                <td <?php if ($count == 0) echo 'id="add-more-price"'?> class="price-action">
                    <i class="fa <?php echo ($count == 0) ? 'fa-plus' : 'fa-minus';?>"></i>
                </td>
            </tr>
            <?php $count ++;?>
            <?php endforeach;?>
	        <?php else: ?>
	        <tr class="prices-input">
	            <td><input type="text" class="price-from" name="price-from[]"/></td>
	            <td><input type="text" class="price-to" name="price-to[]"/></td>
	            <td><select name="price-currency[]">
	           	<?php $defaultCurrency = Engine_Api::_() -> getApi('settings', 'core') -> getSetting('payment.currency', 'USD');?>
	            <?php foreach ($this->currencies as $key => $value):?>
	               <option <?php if ($key == $defaultCurrency) echo 'selected'?> value="<?php echo $key?>"><?php echo $this->translate($value)?></option>
	            <?php endforeach;?>
	            </select></td>
	            <td id="add-more-price" class="price-action">
	                <i class="fa fa-plus"></i>
	            </td>
	        </tr>
	        <?php endif;?>
	    </table>
	</div>
</div>