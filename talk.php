<?php
/*
Plugin Name: Chicago Neighborhood Talk Widget Powered By Everyblock 
Plugin URI: http://tirespider.com/
Description: Add a Chicago Neighborhood Talk Widget to a post or page.
Version: 1.0
Author: Jeff S
Author URI: http://tirespider.com/
License: GPLv2 or later
*/

/*
This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/

class ChicagotalkWidget extends WP_Widget {

	public static $metro = "chicago";
	public static $schema = "talk";
	
	public function ChicagotalkWidget()
	{
		$widget_ops = array('classname' => 'ChicagotalkWidget', 'description' => 'Embeds a Chicago Neighborhood Talk Widget' );
		$this->WP_Widget('ChicagotalkWidget', 'Chicago Neighborhood Talk Widget', $widget_ops);
	}
  
	function form($instance) {
		$instance = wp_parse_args( (array) $instance, array( 'widgetWidth' => '', 'widgetHeight' => ''));
		if(!isset($instance['widgetWidth']) || strlen($instance['widgetWidth']) == 0) { 
			$widgetWidth = "300"; 
		} else {
			$widgetWidth = $instance['widgetWidth'];
		}
		
		if(!isset($instance['widgetHeight']) || strlen($instance['widgetHeight']) == 0) { 
			$widgetHeight = "500"; 
		} else {
			$widgetHeight = $instance['widgetHeight'];
		}
		
	?>
	  <p><label for="<?php echo $this->get_field_id('widgetWidth'); ?>">Width: <input class="widefat" id="<?php echo $this->get_field_id('widgetWidth'); ?>" name="<?php echo $this->get_field_name('widgetWidth'); ?>" type="text" value="<?php echo attribute_escape($widgetWidth); ?>" /></label></p>
	  <p><label for="<?php echo $this->get_field_id('widgetHeight'); ?>">Height: <input class="widefat" id="<?php echo $this->get_field_id('widgetHeight'); ?>" name="<?php echo $this->get_field_name('widgetHeight'); ?>" type="text" value="<?php echo attribute_escape($widgetHeight); ?>" /></label></p>
	<?php
  }
  
  function update($new_instance, $old_instance) 
  {
		$instance = $old_instance;
		
		if(!isset($new_instance['widgetWidth']) || strlen($new_instance['widgetWidth']) == 0) { 
			$instance['widgetWidth'] = "300"; 
		} else {
			$instance['widgetWidth'] = $new_instance['widgetWidth'];
		}
		
		if(!isset($new_instance['widgetHeight']) || strlen($new_instance['widgetHeight']) == 0) { 
			$instance['widgetHeight'] = "500"; 
		} else {
			$instance['widgetHeight'] = $new_instance['widgetHeight'];
		}
		
		return $instance;
  }

  function widget($args, $instance) {
	extract($args, EXTR_SKIP);
	echo "<iframe src=\"" . plugins_url( 'widget.php', __FILE__ ) . "?metro=" .  self::$metro . "&schema=" . self::$schema . "&width=" . $instance['widgetWidth'] . "&height=" . $instance['widgetHeight'] . "&url=" . ABSPATH . "\" width=\"". $instance['widgetWidth'] . "\" //height=\"" . $instance['widgetHeight'] . "\" frameborder='0' scrolling='no'></iframe>";
  }
}
 
add_action('widgets_init', create_function('', 'return register_widget("ChicagotalkWidget");') );

function display_chicago_talk_widget($atts) {
	$atts = extract(shortcode_atts( array('width' => '300', 'height' => '500'), $atts, 'display_chicago_talk_widget'));
	
	$code = "<iframe src=\"" . plugins_url( 'widget.php', __FILE__ ) . "?metro=" . ChicagotalkWidget::$metro . "&schema=" . ChicagotalkWidget::$schema . "&width=" . $width . "&height=" . $height . "&url=" . ABSPATH . "\" width=\"". $width . "\" //height=\"" . $height . "\" frameborder='0' scrolling='no'></iframe>";
	 
	return get_html_for_embed_chicago_talk(stripslashes($code));
}

function get_html_for_embed_chicago_talk($embedCode) {
	return '<div class="widget_block">' . stripslashes($embedCode) .'</div>';  
}

add_shortcode('display_chicago_talk_widget', display_chicago_talk_widget);

if(!function_exists('add_everyblock_api_key_slug')) {
	add_action('admin_menu', 'add_everyblock_api_key_slug');
	
	function add_everyblock_api_key_slug() {
		add_submenu_page( 'plugins.php', 'Everyblock API Key', 'Everyblock API Key', 'activate_plugins', 'everyblock_api_key_slug', 'everyblock_api_key_callback' );
	}
}

if(!function_exists('everyblock_api_key_callback')) {
	function everyblock_api_key_callback() {
		$api_key = get_option('everyblock_api_key', false);
		if(isset($_POST['everyblock_api_key'])) {
			if($api_key === false) {
				add_option('everyblock_api_key', $_POST['everyblock_api_key']);
			} else if ($_POST['everyblock_api_key'] != $api_key) {
				update_option('everyblock_api_key', $_POST['everyblock_api_key']);
			}
			
			$api_key = $_POST['everyblock_api_key'];
		}
		
		?>
			<div class="wrap">
				<h2>Everyblock API Key</h2>
				<form method="post" onsubmit="window.location = window.location"> 
					Everyblock API Key: <input type="text" maxlength="70" style="width:500px;" id="everyblock_api_key" name="everyblock_api_key" value="<?php if(isset($api_key) && ($api_key) !== false) { echo $api_key; } ?>"/>
					<?php submit_button("Update API Key"); ?>
				</form>
			</div>
		<?php
	}
}
?>