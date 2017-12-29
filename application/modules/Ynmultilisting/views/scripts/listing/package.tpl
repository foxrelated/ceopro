<?php if($this -> currentPackage) :?>

<div class="ynmultilisting-available-packages">
    <h3><?php echo $this -> translate('Current package');?></h3>
    <?php if($this -> listing -> status == 'expired') :?>
   		<h4 style="color:red"><?php echo $this -> translate('Your package is now expired. Please make payment to publish your listing again')?></h4>
	<?php endif;?>
</div>

<div class="ynmultilisting-package-item">
    <div class="package-name"><?php echo $this -> currentPackage->title?></div>
    <div class="package-price-group">

        <div class="package-price">
            <?php 
                $price = ($this -> currentPackage->price > 0) ? $this->locale()->toCurrency($this -> currentPackage->price, $this -> currentPackage->currency) : $this->translate('Free');
            ?>
            <span class="price"><?php echo $price; ?></span>
        </div>

        <?php if($this -> listing -> status == 'expired') :?>
        <?php echo $this->htmlLink(array(
            'route' => 'ynmultilisting_specific',
            'action' => 'package-change',
            'listing_id' => $this -> listing ->getIdentity(),
            'packageId' => $this -> currentPackage->getIdentity(),
        ), '<button class="package-button">'.$this -> translate('Make Payment').'</button>', array('class' => 'smoothbox'))?>
       <?php endif;?>
    </div>

    <table class="package-duration-support">
        <tr class="package-duration-support-rows">
            <td class="package-duration-support-rows-title"><h6><?php echo $this->translate('duration') ?></h6></td>
            <td>
                <span class="duration">
                    <?php echo '<i class="fa fa-clock-o"></i>'.$this->translate(array('%s day', '%s days', $this -> currentPackage->valid_amount), $this -> currentPackage->valid_amount); ?>
                </span>
            </td>
        </tr>

        <tr class="package-duration-support-rows">
             <td class="package-duration-support-rows-title"><h6><?php echo $this->translate('support')?></h6></td>
             <td>
                <span class="support">
                <?php foreach($this -> currentPackage->getAvailableFeatures() as $feature) :?>
                    <i class="fa fa-check"></i><?php echo $this -> translate($feature);?> <br>
                     <?php endforeach;?>
                 </span>
            </td>

        </tr>
    </table>

    <div class="package-modules-description">
        <?php echo $this -> currentPackage->description;?>
    </div>
    
</div> 

<?php endif;?>

<div class="ynmultilisting-available-packages">
    <h3><?php echo $this -> translate('Available packages');?></h3>
    <p><?php echo $this -> translate('YNMULTILISTING_DASHBOARD_PACKAGE_DESC');?></p>
</div>


<?php $count = 0; ?>
<?php foreach ($this->packages as $package) : ?>
    <?php if ($package->isViewable()): ?>
        <?php $count++;?>
        <div class="ynmultilisting-package-item">
            <div class="package-name"><?php echo $package->title?></div>
            <div class="package-price-group">
                <?php 
                    if(isset($this -> currentPackage))
                    {
                        $labelBtn = $this -> translate('Change Package');
                    }
                    else
                    {
                        $labelBtn = $this -> translate('Make Payment');
                    }               
                ?>

                <div class="package-price">
                    <?php 
                        $price = ($package->price > 0) ? $this->locale()->toCurrency($package->price, $package->currency) : $this->translate('Free');
                    ?>
                    <span class="price"><?php echo $price; ?></span>
                </div>

                
                <?php echo $this->htmlLink(array(
                    'route' => 'ynmultilisting_specific',
                    'action' => 'package-change',
                    'listing_id' => $this -> listing ->getIdentity(),
                    'packageId' => $package->getIdentity(),
                ), '<button class="package-button">'.$labelBtn.'</button>', array('class' => 'smoothbox'))?>
               
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
                     <td>
                        <span class="support">
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

