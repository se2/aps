<?php
/**
 * Created by PhpStorm.
 * User: DeveloperP1
 * Date: 6/6/2016
 * Time: 4:30 PM
 */
?>
<div class="wrap">
	<h1>Shop Settings</h1>

	<form method="post" action="options.php" novalidate="novalidate">
		<?php
		settings_fields('vg_option_group');
		do_settings_sections( 'vg_option_group' );
		?>
		<table class="form-table">
			<tbody><tr>
				<th scope="row"><label for="fuelsurcharge">% Fuel Surcharge</label></th>
				<td><input name="fuelsurcharge" type="number" id="fuelsurcharge" value="<?php echo get_option('fuelsurcharge') ?>" class="regular-text"></td>
			</tr>
			<tr>
				<th scope="row"><label for="profit">% Profit</label></th>
				<td><input name="profit" type="number" id="profit" value="<?php echo get_option('profit') ?>" class="regular-text"></td>
			</tr>
			<tr>
				<th scope="row"><label for="exchangerate">Exchange Rate</label></th>
				<td><input name="exchangerate" type="number" id="exchangerate" value="<?php echo get_option('exchangerate') ?>" class="regular-text"></td>
			</tr>
			<tr>
				<th scope="row"><label for="Remoteareasurcharge">Remote Area Surcharge (HKD)</label></th>
				<td><input name="remoteareasurcharge" type="number" id="Remoteareasurcharge" value="<?php echo get_option('remoteareasurcharge') ?>" class="regular-text"></td>
			</tr>
			</tbody>
		</table>
		<?php submit_button(); ?>
	</form>
</div>
