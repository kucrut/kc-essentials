<?php

/**
 * @package KC_Essentials
 * @version 0.1
 *
 * @subpackage Widget_Enhancements
 *
 */


class kcEssentials_widget_custom_id_class {

	public static function init() {
		# Custom widget ID & classes
		# 0. Add fields on widget configuration form
		add_filter( 'widget_form_callback', array(__CLASS__, '_fields'), 10, 2 );

		# 1. Update widget options
		add_filter( 'widget_update_callback', array(__CLASS__, '_save'), 10, 2 );

		# 2. Modify widget markup
		add_filter( 'dynamic_sidebar_params', array(__CLASS__, '_set') );
	}


	/**
	 * Add custom ID/classes fields to widget configuration form
	 *
	 */
	public static function _fields( $instance, $widget ) {
		$customs = array(
			'id'	=> array(
				__('Custom ID', 'kc-essentials'),
				__('Custom ID', 'kc-essentials')
			),
			'class'	=> array(
				__('Custom classes', 'kc-essentials'),
				__('Custom classes, <small>(separate with spaces)</small>', 'kc-essentials')
			)
		);

		$output = "<div class='kcwe'>\n";
		foreach ( $customs as $c => $l ) {
			if ( !isset($instance["custom_{$c}"]) )
				$instance["custom_{$c}"] = '';

			$f_current = $instance["custom_{$c}"];
			$f_name	= "widget-{$widget->id_base}[{$widget->number}][custom_{$c}]";
			$f_id		= "widget-{$widget->id_base}-{$widget->number}-custom_{$c}";

			$output .= "\t<p>\n";


			if ( !isset($data[$c]) || empty($data[$c]) ) {
				$output .= "\t\t<label for='{$f_id}'>{$l[1]}</label>\n";
				$output .= "\t\t<input type='text' name='{$f_name}' id='{$f_id}' class='widefat' value='{$f_current}'/>\n";
			}
			else {
				$f_opt = array();
				foreach ( explode( ' ', $data[$c]) as $o )
					$f_opt[] = array('value' => $o, 'label' => $o);

				if ( $c == 'id' ) {
					$f_type = 'select';
					$f_class = 'widefat';
				} else {
					$f_type = 'checkbox';
					$f_name .= '[]';
					$f_class = '';
					$f_current = explode( ' ', $f_current );
				}

				$output .= "\t\t<label for='{$f_id}'>{$l[0]}</label><br />\n";
				$output .= kcForm::field(array(
					'attr'	=> array(
						'id'		=> $f_id,
						'name'	=> $f_name,
						'class'	=> $f_class
					),
					'type'		=> $f_type,
					'options'	=> $f_opt,
					'current'	=> $f_current
				));
			}
			$output .= "\t</p>\n";
		}
		$output .= "</div>\n";

		echo $output;

		return $instance;
	}


	/**
	 * Sanitize widget's custom ID and/or classes
	 *
	 */
	public static function _save( $instance, $new_instance ) {
		foreach ( array('id', 'class') as $c ) {
			# 0. Add/Update
			if ( !empty($new_instance["custom_{$c}"]) ) {
				if ( $c == 'id' )
					$instance["custom_{$c}"] = trim( sanitize_html_class($new_instance["custom_{$c}"]) );
				else
					$instance["custom_{$c}"] = trim( kc_essentials_sanitize_html_classes($new_instance["custom_{$c}"]) );
			}

			# 1. Delete
			else {
				unset( $instance["custom_{$c}"] );
			}
		}

		return $instance;
	}


	/**
	 * Add custom ID/classes to widget's markup
	 *
	 */
	public static function _set( $params ) {
		global $wp_registered_widgets;
		$widget_id	= $params[0]['widget_id'];
		$widget_obj	= $wp_registered_widgets[$widget_id];

		if ( !isset($widget_obj['callback'][0]) || !is_object($widget_obj['callback'][0]) )
			return $params;

		$widget_opt	= get_option($widget_obj['callback'][0]->option_name);
		$widget_num	= $widget_obj['params'][0]['number'];

		# 0. Custom ID
		if ( isset($widget_opt[$widget_num]['custom_id']) )
			$params[0]['before_widget'] = preg_replace( '/id=".*?"/', "id=\"{$widget_opt[$widget_num]['custom_id']}\"", $params[0]['before_widget'], 1 );

		# 1. Custom Classes
		if ( isset($widget_opt[$widget_num]['custom_class']) )
			$params[0]['before_widget'] = preg_replace( '/class="/', "class=\"{$widget_opt[$widget_num]['custom_class']} ", $params[0]['before_widget'], 1 );

		return $params;
	}
}

kcEssentials_widget_custom_id_class::init();

?>