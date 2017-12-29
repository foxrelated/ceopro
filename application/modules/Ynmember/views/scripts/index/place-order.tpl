<h2><?php echo $this->translate('Add to Featured Members');?></h2>
<br/>
<ul>
	<li>
		<?php echo $this->translate('Fee to be featured member in 1 day is %s.',  $this -> locale()->toCurrency($this->feature_fee, $this->currency) )?></p>
	</li>
	<li>	
			<?php echo $this -> translate(array("You have chosen %s day to be featured member.", "You have chosen %s days to be featured member.", $this -> day), $this -> day) ?>
			<?php echo $this -> translate('Total cost: %s', $this -> locale()->toCurrency($this->feature_fee*$this->day, $this->currency))?>
	</li>	
</ul>	

<br/><br/>

<h3 style="margin-bottom: 10px"><?php echo $this->translate('Select gateway to place order'); ?></h3>   
<div>
	<div>
		<form method="post" action="<?php echo $this->escape($this->url(array('controller'=>'index','action' => 'update-order'), 'ynmember_general', true)) ?>" class="global_form" enctype="application/x-www-form-urlencoded">
			<div class="form-elements">
				<div id="buttons-wrapper" class="form-wrapper">
					<?php foreach( $this->gateways as $gatewayInfo ):
						$gateway = $gatewayInfo['gateway'];
						$plugin = $gatewayInfo['plugin'];
						$first = ( !isset($first) ? true : false );
						?>
						<button style="margin-top: 5px" type="submit" name="gateway_id" value="<?php echo $gateway->gateway_id ?>">
							<?php echo $this->translate('Pay with')." ".$this->translate($gateway->title) ?>
						</button>
						<?php echo $this->translate(' or ') ?>

					<?php endforeach; ?>
					
					<?php  if(($this->allowPayCredit) == '1') :?>
						<button name='type' value='paycredit' style="margin-top: 5px" type="submit" >
							<?php echo $this->translate('Pay with Credit') ?>
						</button>
						<?php echo $this->translate(' or ') ?>
					<?php endif; ?>
					<a href="<?php echo $this->url(array('controller' => 'profile',	'action' => 'index','id' => $this->viewer->getIdentity()),'user_profile',true); ?>"> <?php echo $this->translate('cancel') ?> </a>
				</div>
			</div>
		</form>
	</div>
</div>

