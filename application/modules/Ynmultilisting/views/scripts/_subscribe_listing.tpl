<?php $currency = Engine_Api::_() -> getApi('settings', 'core') -> getSetting('payment.currency', 'USD') ;?>
<table width="570" cellpadding="0" cellspacing="0" style="font-family: tahoma, arial, verdana, sans-serif; margin: 0 auto; background-color: #fff; font-size: 10px;">
	<tr>
		<td colspan="3" style="font-size: 12px; color: #fff; text-align: center; height: 55px; vertical-align: middle; background-color: #2995c0;">
		<!-- header -->
		<?php echo $this -> translate('Here are listings you may be interested in from ') . $this -> layout() -> siteinfo['title'] . ""; ?>
		</td>
	</tr>
	<tr>
		<td colspan="3" style="height: 5px; background-image: url(<?php echo 'http://' . $_SERVER['HTTP_HOST'] . $this->baseUrl() . '/application/modules/Ynmultilisting/externals/images/pattern_letter.png'; ?>);"></td>
	</tr>
	<?php foreach($this -> toSendListingIds as $listing_id) :?>
		<?php $listing = Engine_Api::_() -> getItem('ynmultilisting_listing', $listing_id);?>
		<?php if(!empty($listing)) :?>
		<tr>
			<td width="70" style="padding: 10px; vertical-align: top;">
				<div style="width: 70px; height: 70px; overflow: hidden;">
					<!-- image -->
					<?php if($listing -> getPhotoUrl()) :?>
						<?php echo "<img style='height: 70px; width: 70px;' width='70' height='70' src='" .'http://' . $_SERVER['HTTP_HOST'] . $listing -> getPhotoUrl() ."'>". ""; ?>
					<?php else:?>
						<?php echo "<img style='height: 70px; width: 70px;' width='70' height='70' src='" .'http://' . $_SERVER['HTTP_HOST'] . $this->baseUrl() . '/application/modules/Ynmultilisting/externals/images/no_image_for_this_product.png' ."'>". ""; ?>
					<?php endif;?>
				</div>
			</td>
		
			<td width="300" style="padding: 10px; vertical-align: top; line-height: 1.4em;">
				<div style="color: #2995c0; font-size: 12px; font-weight: bold;">
					<!-- job title -->
					<?php echo $listing->getTitle() . ""; ?>
				</div>

				<?php if($listing -> location) :?>
				<div style="color: #999999;">
					<!-- location -->
					<?php echo $listing -> location; ?>
				</div>
				<?php endif;?>
			</td>

			<td width="200" style="padding: 10px; vertical-align: top;">
				<div style="text-transform: capitalize; color: #e54549; font-size: 10px; font-weight: bold;">
				<!-- price -->
					<?php echo $this -> locale() -> toCurrency($listing->price, $currency). "<br />"; ?>
				</div>

				<div style="margin-top: 5px;">
				<!-- view listing -->
				<?php echo "<a style='display: inline-block; text-decoration: none; border: 1px solid #2995c0; color: #2995c0; padding: 3px 10px; border-radius: 3px; -webkit-border-radius: 3px; -moz-border-radius: 3px;' href='" . 'http://' . $_SERVER['HTTP_HOST'] . $listing -> getHref() . "'>" . $this -> translate('VIEW LISTING >>') . "</a>"; ?>
				</div>
			</td>
		</tr>
		<?php endif;?>
	<?php endforeach;?>

	<tr>
		<td colspan="3" style="height: 5px; background-image: url(<?php echo 'http://' . $_SERVER['HTTP_HOST'] . $this->baseUrl() . '/application/modules/Ynmultilisting/externals/images/pattern_letter.png'; ?>);"></td>
	</tr>
	<tr>
		<td colspan="3" style="font-size: 10px; color: #fff; text-align: center; height: 55px; vertical-align: middle; background-color: #d1312e; ">
		<?php if(!empty($this -> toSendListingIds)) :?>
			<!-- unsubsribe -->
			<?php $unsubscribeUrl = $this -> url(array('action' => 'unsubscribe', 'email' => $this -> email), 'ynmultilisting_general', true); ?>
			<a style="color: #fff; text-decoration: none;" href='<?php echo 'http://' . $_SERVER['HTTP_HOST'] . $unsubscribeUrl ?>'><?php echo $this -> translate('UNSUBSCRIBE >>') ?></a>
		<?php endif;?>
		</td>
	</tr>
</table>