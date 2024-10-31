<?php
/**
 * @package reefiris
 * @author Keith Thornhill
 * @version 1.0
 */
/*
Plugin Name: reefiris
Plugin URI: http://www.reefiris.com
Description: Display your reef tank parameters from reefiris.com on your blog
Author: Keith Thornhill
Version: 1.0
Author URI: http://www.afex2win.com
*/


function reefiris_widget_init() {
    if ( !function_exists('register_sidebar_widget') )
		return;

	function widget_reefiris($args) {
		extract($args);

		$options = get_option('widget_reefiris');
		$title = $options['title'];
		$username = $options['username'];
        
		echo $before_widget . $before_title . $title . $after_title;
		if ($username) {
		    echo "
		        <style>
		            ul.reefiris_parameters {
		                margin-left: 1.5em !important;
		            }
		            li.reefiris_tank, li.reefiris_parameter {
		                list-style: disc outside;
		                margin-left: 1.5em !important;
		            }
		        </style>
		    ";
		    echo '<a class="reefiris_details_link" href="http://www.reefiris.com/profiles/'.$username.'/data" target="_new">View details</a><br/><br/>';
            // fetch and parse xml
            $sxe = new SimpleXMLElement('http://www.reefiris.com/profiles/'.$username.'/data.xml', NULL, TRUE);
            echo '<ul class="reefiris_tanks">';
            foreach ($sxe->tank as $tank) {
                echo '<li class="reefiris_tank"><a class="reefiris_tank_link" href="'.$tank->url.'" target="_new">'.$tank->name.'</a></li>';
                echo '<ul class="reefiris_parameters">';
                foreach ($tank->parameter as $param) {
                    echo '<li class="reefiris_parameter">'.$param->name.': '.$param->value.'</li>';
                }
                echo '</ul>';
            }
            echo '</ul>';
		} else {
		    ?>
		    Please input your reefiris username in the widget settings.
		    <?
		}
		echo $after_widget;
	}

	function widget_reefiris_control() {
		$options = get_option('widget_reefiris');

		if ( !is_array($options) )
			$options = array('title'=>'my aquarium stats from reefiris.com','username'=>'');

		if ( $_POST['reefiris-submit'] ) {
			$options['title'] = strip_tags(stripslashes($_POST['reefiris-title']));
			$options['username'] = strip_tags(stripslashes($_POST['reefiris-username']));
			update_option('widget_reefiris', $options);
		}

		$title = htmlspecialchars($options['title'], ENT_QUOTES);
		$username = htmlspecialchars($options['username'], ENT_QUOTES);
		
		?>
		<p style="text-align:left;">
		    <label for="reefiris-title"><?=__('Title:')?>
		        <input style="width: 200px;" id="reefiris-title" name="reefiris-title" type="text" value="<?=$title?>" />
		    </label>
		</p>
		<p style="text-align:left;">
		    <label for="reefiris-username"><?=__('reefiris Username:')?>
		        <input style="width: 100px;" id="reefiris-title" name="reefiris-username" type="text" value="<?=$username?>" /><br/>
		        <i>Don't have a reefiris account? <a href="http://www.reefiris.com/signup">Get one here</a></i>
		    </label>
		</p>
		<input type="hidden" id="reefiris-submit" name="reefiris-submit" value="1" />
		<?
	}

	register_sidebar_widget(array('reefiris', 'widgets'), 'widget_reefiris');

	register_widget_control(array('reefiris', 'widgets'), 'widget_reefiris_control', 300, 200);
}


// hooks
add_action('widgets_init', 'reefiris_widget_init');

?>