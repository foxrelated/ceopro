<?php if($this -> notCreateMore):?>
<div class="tip">
	<span><?php echo $this -> translate("Your listings are reach limit. Please delete some listings for creating new."); ?></span>
</div>		
<?php return; endif;?>

<div class="ynmultilisting-package-all">
    <h2><?php echo $this->translate("All Packages") ?></h2>

    <div id="create-listing-step-one">
        <?php $count = 0; ?>
        <?php foreach ($this->packages as $package) : ?>
            <?php if ($package->isViewable()): ?>
                <?php $count++;?>
                <div class="ynmultilisting-package-item">
                    <div class="package-name"><?php echo $package->title?></div>
                    <div class="package-price-group">

                        <div class="package-price">
                            <?php 
                                $price = ($package->price > 0) ? $this->locale()->toCurrency($package->price, $package->currency) : $this->translate('Free');
                            ?>
                            <span class="price"><?php echo $price?></span>
                        </div>
                        
                        <?php echo $this->htmlLink(array(
                            'route' => 'ynmultilisting_general',
                            'action' => 'create-step-two',
                            'package_id' => $package->getIdentity()
                        ), '<button class="package-button">'.$this->translate('Create Listing').'</button>', array())?>
                       
                    </div>
                        

                    <table class="package-duration-support">
                        <tr class="package-duration-support-rows">
                            <td class="package-duration-support-rows-title"><h6><?php echo $this->translate('duration') ?></h6></td>
                            <td>
                                <span class="duration">
                                    <?php echo '<i class="fa fa-clock-o"></i>'.$this->translate(array('%s day', '%s days', $package->valid_amount), $package->valid_amount); ?>
                                </span>
                            </td>
                        </tr>


                        <tr class="package-duration-support-rows">
                            <td class="package-duration-support-rows-title"><h6><?php echo $this->translate('support')?></h6></td>
                            <td><span class="support">
                            <?php foreach($package->getAvailableFeatures() as $feature) :?>
                            <i class="fa fa-check"></i><?php echo $this -> translate($feature);?> <br>
                             <?php endforeach;?>
                             </span>
                             </td>
                        </tr>
                    </table>
                   
                    <div class="package-modules-description">
                        <?php echo $package->description;?>
                    </div>               
                </div> 
            <?php endif; ?>
        <?php endforeach; ?>
        <?php if($count == 0) :?>
        	<div class = 'tip'>
    			<span>
    	   			<?php echo $this->translate('There are no available packages.'); ?>
    	   		</span>
      		</div>
        <?php endif;?>
    </div>
</div>