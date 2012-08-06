<?php

/**
 * Widget logic module
 * @package KC_Essentials
 */


class kcEssentials_widget_logic {
	private static $data = array();

	public static function init() {
		# Custom widget ID & classes
		# 0. Add fields on widget configuration form
		add_filter( 'in_widget_form', array(__CLASS__, '_fields'), 10, 3 );

		# 1. Update widget options
		add_filter( 'widget_update_callback', array(__CLASS__, '_save'), 10, 4 );

		# 2. Remove widgets from sidebars as needed
		add_filter( 'sidebars_widgets', array(__CLASS__, '_filter_widgets') );

		# Widget config form scripts & styles
		add_action( 'load-widgets.php', array('kcEssentials_widgets', '_actions') );
	}



	/**
	 * Add logic fields to widget configuration form
	 *
	 */
	public static function _fields( $widget, $return, $instance ) {
		$f_id			= $widget->get_field_id('kc-logic');
		$f_name		= $widget->get_field_name('kc-logic');
		$setting	= kcEssentials_widgets::get_setting( $widget->id ); ?>
<div class="kcwe">
	<p>
		<label for="<?php echo $widget->get_field_id('kc-logic-enable') ?>"><?php _e('Logic status:', 'kc-essentials') ?></label>
		<?php echo kcForm::field( array(
			'type'    => 'select',
			'attr'    => array(
				'id'         => $widget->get_field_id('kc-logic-enable'),
				'name'       => $widget->get_field_name('kc-logic-enable'),
				'class'      => 'hasdep kc-logic-enable',
				'data-child' => "#{$f_id}-logics"
			),
			'options' => array(
				'0' => __('Disable', 'kc-essentials'),
				'1' => __('Enable', 'kc-essentials')
			),
			'none'    => false,
			'current' => ( isset($setting['kc-logic-enable']) && $setting['kc-logic-enable'] ) ? true : false
		) );
		?>
	</p>
	<p id="<?php echo $f_id ?>-logics" data-dep="1">
		<label for="<?php echo $f_id ?>"><?php _e('Logic locations:', 'kc-essentials') ?></label>
		<?php echo kcForm::field( array(
			'type'    => 'select',
			'attr'    => array(
				'id'       => $f_id,
				'name'     => "{$f_name}[]",
				'multiple' => true
			),
			'options' => array(
				'is_home'              => __('Homepage', 'kc-essentials'),
				'is_front_page'        => __('Static front page', 'kc-essentials'),
				'is_singular'          => __('Singular', 'kc-essentials'),
				'is_page'              => __('Page', 'kc-essentials'),
				'is_page_template'     => __('Custom page template', 'kc-essentials'),
				'is_single'            => __('Single post', 'kc-essentials'),
				'is_sticky'            => __('Sticky post', 'kc-essentials'),
				'is_attachment'        => __('Attachment', 'kc-essentials'),
				'is_archive'           => __('Archive', 'kc-essentials'),
				'is_post_type_archive' => __('Post type archive', 'kc-essentials'),
				'is_category'          => __('Category', 'kc-essentials'),
				'is_tag'               => __('Tag', 'kc-essentials'),
				'is_tax'               => __('Taxonomy', 'kc-essentials'),
				'is_author'            => __('Author', 'kc-essentials'),
				'is_404'               => __('404', 'kc-essentials'),
				'is_search'            => __('Search page', 'kc-essentials'),
				'is_paged'             => __('Paged archive', 'kc-essentials'),
				'is_year'              => __('Year archive', 'kc-essentials'),
				'is_month'             => __('Month archive', 'kc-essentials'),
				'is_date'              => __('Date archive', 'kc-essentials'),
				'is_day'               => __('Day archive', 'kc-essentials'),
				'is_new_day'           => __('New day', 'kc-essentials'),
				'is_time'              => __('Time archive', 'kc-essentials'),
				'is_preview'           => __('Preview page', 'kc-essentials'),
				'is_user_logged_in'    => __('Logged in user', 'kc-essentials'),
			),
			'none'    => false,
			'current' => isset($setting['kc-logic']) ? $setting['kc-logic'] : array()
		) );
		?>
	</p>
</div>
	<?php
		$return = null;
	}


	public static function _save( $instance, $new, $old, $widget ) {
		$setting = kcEssentials_widgets::get_setting( $widget->id );
		$setting['kc-logic-enable'] = ( isset($new['kc-logic-enable']) && $new['kc-logic-enable'] ) ? true : false;
		if ( isset($new['kc-logic']) )
			$setting['kc-logic'] = $new['kc-logic'];
		kcEssentials_widgets::save_setting( $widget->id, $setting );

		return $instance;
	}


	public static function _filter_widgets( $sidebars_widgets ) {
		if ( is_admin() )
			return $sidebars_widgets;

		$settings = get_option( 'kc_essentials_we' );
		if ( !$settings )
			return $sidebars_widgets;

		foreach ( $sidebars_widgets as $sidebar => $widgets ) {
			if ( $sidebar == 'wp_inactive_widgets' )
				continue;

			foreach ( $widgets as $idx => $widget ) {
				if (
					!isset($settings[$widget]['kc-logic-enable'])
					|| !$settings[$widget]['kc-logic-enable']
					|| !isset($settings[$widget]['kc-logic'])
					|| !is_array($settings[$widget]['kc-logic'])
					|| empty($settings[$widget]['kc-logic'])
				)
					continue;

				foreach ( $settings[$widget]['kc-logic'] as $func ) {
					if ( call_user_func($func) === true )
						continue 2;
				}

				unset( $widgets[$idx] );
			}
			$sidebars_widgets[$sidebar] = $widgets;
		}

		return $sidebars_widgets;
	}
}

kcEssentials_widget_logic::init();

?>