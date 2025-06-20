<?php
function floating(){ ?>
<?php $kln = get_option('floatcenter'); if(is_home()){ if($kln){ ?>
<div id="floatcenter">
<div class="ctrx" align="center"><span id="close-teaser" onclick="document.getElementById('floatcenter').style.display = 'none';" style="cursor:pointer;display:block;"><img src="<?php echo get_template_directory_uri().'/assets/img/btn_close.gif'; ?>"/></span>
<?php echo $kln; ?>
</div>
</div>
<?php } } ?>
<?php $kln = get_option('floatleft');if($kln){ ?>
<div id="teaser1" style="width:autopx; height:0; text-align:left; display: scroll;position:fixed; top:35px;left:0px;">
<div align="center"><span id="close-teaser" onclick="document.getElementById('teaser1').style.display = 'none';" style="cursor:pointer;display:block;"><img src="<?php echo get_template_directory_uri().'/assets/img/btn_close.gif'; ?>"/></span></div>
<?php echo $kln; ?>
</div>
<?php } ?>
<?php $kln = get_option('floatright');if($kln){ ?>
<div id="teaser2" style="width:autopx; height:0; text-align:right; display: scroll;position:fixed; top:35px;right:0px;">
<div align="center"><span id="close-teaser" onclick="document.getElementById('teaser2').style.display = 'none';" style="cursor:pointer;display:block;"><img src="<?php echo get_template_directory_uri().'/assets/img/btn_close.gif'; ?>"/></span></div>
<?php echo $kln; ?>
</div>
<?php } ?>
<?php $kln = get_option('floatbottom');if($kln){ ?>
<div id="teaserbottom" style="width: 100%;text-align: center;display: scroll;position:fixed;bottom: 0;margin: 0 auto;z-index: 103;">
<div align="center"><span id="close-teaser" onclick="document.getElementById('teaserbottom').style.display = 'none';" style="cursor:pointer;display:block;"><img src="<?php echo get_template_directory_uri().'/assets/img/btn_close.gif'; ?>"/></span></div>
<div class="blox kln">
<?php echo $kln; ?>
</div>
</div>
<?php } ?>
<?php $kln = get_option('floattop');if($kln){ ?>
<div id="teaser4" class="teaser4" style="width: 100%;text-align: center;display: scroll;position:fixed;top: 0;margin: 0 auto;z-index: 103;">
<div class="blox">
<?php echo $kln; ?>
</div>
<div align="center"><span id="close-teaser" onclick="document.getElementById('teaser4').style.display = 'none';" style="cursor:pointer;display:block;"><img src="<?php echo get_template_directory_uri().'/assets/img/btn_close.gif'; ?>"/></span></div>
</div>
<?php } ?>
<?php }

add_action('admin_menu', 'kln_menu');
function kln_menu() {
    add_menu_page('Ads Management', 'Ads Management', 'administrator','ads', 'ads','dashicons-screenoptions',81 );
    add_submenu_page( 'ads', 'Dashboard', 'Dashboard', 'administrator', 'ads','ads');
    $submenu['dashboard'][0][0] = 'Dashboard';
    add_action( 'admin_init', 'register_kln_settings' );
}
function register_kln_settings() {
    register_setting( 'kln-settings', 'pageall' );
    register_setting( 'kln-settings', 'pagemini' );
    register_setting( 'kln-settings', 'toprec' );
	register_setting( 'kln-settings', 'topf' );
	register_setting( 'kln-settings', 'tople' );
	register_setting( 'kln-settings', 'topls' );
	register_setting( 'kln-settings', 'tseries1' );
	register_setting( 'kln-settings', 'tseries2' );
	register_setting( 'kln-settings', 'reader1' );
    register_setting( 'kln-settings', 'reader1' );
    register_setting( 'kln-settings', 'reader2' );
	register_setting( 'kln-settings', 'overplay' );
	register_setting( 'kln-settings', 'floatcenter' );
    register_setting( 'kln-settings', 'floatleft' );
    register_setting( 'kln-settings', 'floatright' );
    register_setting( 'kln-settings', 'floatbottom' );
	register_setting( 'kln-settings', 'floattop' );
	register_setting( 'kln-settings', 'floatmobile' );
} function ads() { ?>
<div class="wrap">
<style>.wrap {padding: 20px;background: #FFF;border: 1px solid #e5e5e5;box-shadow: 0 1px 1px rgba(0,0,0,.04);}</style>
<h2>Ads Management</h2>
<form method="post" action="options.php">
    <?php settings_fields( 'kln-settings' ); ?>
    <?php do_settings_sections( 'kln-settings' ); ?>
    <table class="form-table">
		<tr valign="top">
        <th scope="row">Below Main Menu (General)</th>
        <td><textarea type="text/javascript" name="toprec" rows="10" cols="50" value="<?php echo esc_attr( get_option('toprec') ); ?>" class="large-text code"><?php echo esc_attr( get_option('toprec') ); ?></textarea></td>
        </tr>
		<tr valign="top">
        <th scope="row">Above Project Update (Homepage)</th>
        <td><textarea type="text/javascript" name="topf" rows="10" cols="50" value="<?php echo esc_attr( get_option('topf') ); ?>" class="large-text code"><?php echo esc_attr( get_option('topf') ); ?></textarea></td>
        </tr>
		<tr valign="top">
        <th scope="row">Above Latest Update (Homepage)</th>
        <td><textarea type="text/javascript" name="tople" rows="10" cols="50" value="<?php echo esc_attr( get_option('tople') ); ?>" class="large-text code"><?php echo esc_attr( get_option('tople') ); ?></textarea></td>
        </tr>
        <tr valign="top">
        <th scope="row">Above Image Area (Single Chapter)</th>
        <td><textarea type="text/javascript" name="reader1" rows="10" cols="50" value="<?php echo esc_attr( get_option('reader1') ); ?>" class="large-text code"><?php echo esc_attr( get_option('reader1') ); ?></textarea></td>
        </tr>
        <tr valign="top">
        <th scope="row">Below Image Area (Single Chapter)</th>
        <td><textarea type="text/javascript" name="reader2" rows="10" cols="50" value="<?php echo esc_attr( get_option('reader2') ); ?>" class="large-text code"><?php echo esc_attr( get_option('reader2') ); ?></textarea></td>
        </tr>
		<tr valign="top">
        <th scope="row">Below Info Series (Single Series)</th>
        <td><textarea type="text/javascript" name="tseries1" rows="10" cols="50" value="<?php echo esc_attr( get_option('tseries1') ); ?>" class="large-text code"><?php echo esc_attr( get_option('tseries1') ); ?></textarea></td>
        </tr>
		<tr valign="top">
        <th scope="row">Below Chapter Lists (Single Series)</th>
        <td><textarea type="text/javascript" name="tseries2" rows="10" cols="50" value="<?php echo esc_attr( get_option('tseries2') ); ?>" class="large-text code"><?php echo esc_attr( get_option('tseries2') ); ?></textarea></td>
        </tr>
		<tr valign="top">
        <th scope="row">Float Center</th>
        <td><textarea type="text/javascript" name="floatcenter" rows="10" cols="50" value="<?php echo esc_attr( get_option('floatcenter') ); ?>" class="large-text code"><?php echo esc_attr( get_option('floatcenter') ); ?></textarea></td>
        </tr>
        <tr valign="top">
        <th scope="row">Float Left</th>
        <td><textarea type="text/javascript" name="floatleft" rows="10" cols="50" value="<?php echo esc_attr( get_option('floatleft') ); ?>" class="large-text code"><?php echo esc_attr( get_option('floatleft') ); ?></textarea></td>
        </tr>
        <tr valign="top">
        <th scope="row">Float Right</th>
        <td><textarea type="text/javascript" name="floatright" rows="10" cols="50" value="<?php echo esc_attr( get_option('floatright') ); ?>" class="large-text code"><?php echo esc_attr( get_option('floatright') ); ?></textarea></td>
        </tr>
		<tr valign="top">
        <th scope="row">Float Top</th>
        <td><textarea type="text/javascript" name="floattop" rows="10" cols="50" value="<?php echo esc_attr( get_option('floattop') ); ?>" class="large-text code"><?php echo esc_attr( get_option('floattop') ); ?></textarea></td>
        </tr>
        <tr valign="top">
        <th scope="row">Float Bottom</th>
        <td><textarea type="text/javascript" name="floatbottom" rows="10" cols="50" value="<?php echo esc_attr( get_option('floatbottom') ); ?>" class="large-text code"><?php echo esc_attr( get_option('floatbottom') ); ?></textarea></td>
        </tr>
		<tr valign="top">
        <th scope="row">Show Float Ads on Mobile</th>
        <td>
			<select name="floatmobile">
				<option value="0" <?php if (esc_attr(get_option('floatmobile')) == '0') {echo 'selected="selected"';}?>>Disable</option>
				<option value="1" <?php if (esc_attr(get_option('floatmobile')) == '1') {echo 'selected="selected"';}?>>Enable</option>
			</select>
		</td>
        </tr>
    </table>  
    <?php submit_button(); ?>

</form>
</div>
<?php } ?>