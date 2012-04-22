<?php

/**
 * @package KC_Essentials
 * @version 0.1
 */


class kc_widget_term extends WP_Widget {
	var $defaults;

	function __construct() {
		$widget_ops = array( 'classname' => 'kcw_term', 'description' => __('Display a list of taxonomy terms', 'kc-essentials') );
		$control_ops = array( 'width' => 275 );
		parent::__construct( 'kcw_term', 'KC Terms', $widget_ops, $control_ops );
		$this->defaults = array(
			'title'       => '',
			'taxonomy'    => 'category',
			'orderby'     => 'name',
			'order'       => 'ASC',
			'misc'        => array( 'hierarchical', 'hide_empty' ),
			'debug'       => 0
		);
	}


	function update( $new, $old ) {
		//echo '<pre>'.print_r( $new, true).'</pre>';exit;
		return $new;
	}


	function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, $this->defaults );
		$title    = strip_tags( $instance['title'] );
	?>
		<h5 class="kcw-head" title="<?php _e('Show/hide', 'kc-essentials') ?>"><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Widget title', 'kc-essentials') ?></label></h5>
		<ul class="kcw-control-block">
			<li>
				<?php echo kcForm::input(array(
					'attr'    => array('id' => $this->get_field_id('title'), 'name' => $this->get_field_name('title'), 'class' => 'widefat'),
					'current' => $title
				)) ?>
			</li>
		</ul>

		<h5 class="kcw-head" title="<?php _e('Show/hide', 'kc-essentials') ?>"><?php _e('Config', 'kc-essentials') ?></h5>
		<ul class="kcw-control-block">
			<li>
				<label for="<?php echo $this->get_field_id('taxonomy'); ?>"><?php _e('Taxonomy', 'kc-essentials') ?></label>
				<?php echo kcForm::field(array(
					'type'    => 'select',
					'attr'    => array('id' => $this->get_field_id('taxonomy'), 'name' => $this->get_field_name('taxonomy')),
					'options' => kcSettings_options::$taxonomies,
					'current' => $instance['taxonomy'],
					'none'    => false
				)) ?>
			</li>
			<li>
				<label for="<?php echo $this->get_field_id('orderby'); ?>"><?php _e('Order by', 'kc-essentials') ?></label>
				<?php echo kcForm::field(array(
					'type'    => 'select',
					'attr'    => array('id' => $this->get_field_id('orderby'), 'name' => $this->get_field_name('orderby')),
					'options' => array( 'name' => __('Name', 'kc-essentials'), 'ID' => 'ID'),
					'current' => $instance['orderby'],
					'none'    => false
				)) ?>
			</li>
			<li>
				<label for="<?php echo $this->get_field_id('order'); ?>"><?php _e('Order', 'kc-essentials') ?></label>
				<?php echo kcForm::field(array(
					'type'    => 'select',
					'attr'    => array('id' => $this->get_field_id('order'), 'name' => $this->get_field_name('order')),
					'options' => array( 'name' => __('Ascending', 'kc-essentials'), 'ID' => __('Descending', 'kc-essentials') ),
					'current' => $instance['order'],
					'none'    => false
				)) ?>
			</li>
			<li>
				<label><?php _e('Misc.', 'kc-essentials') ?></label>
				<div class="checks">
					<?php echo kcForm::field(array(
						'type'    => 'checkbox',
						'attr'    => array('id' => $this->get_field_id('misc'), 'name' => $this->get_field_name('misc').'[]'),
						'options' => array(
							'hierarchical'       => __('Hierarchical', 'kc-essentials'),
							'hide_empty'         => __('Hide empty', 'kc-essentials'),
							'show_count'         => __('Show count', 'kc-essentials'),
							'use_desc_for_title' => __('Use desc. for title', 'kc-essentials')
						),
						'current' => $instance['misc']
					)) ?>
				</div>
			</li>
		</ul>
	<?php }


	function widget( $args, $instance ) {
		$misc = array(
			'hierarchical'       => false,
			'hide_empty'         => false,
			'show_count'         => false,
			'use_desc_for_title' => false,
			'echo'               => false,
			'title_li'           => ''
		);
		if ( isset($instance['misc']) && !empty($instance['misc']) ) {
			$_misc = $instance['misc'];
			unset( $instance['misc'] );

			foreach ( $_misc as $m )
				$misc[$m] = true;
		}
		$instance += $misc;

		$list = wp_list_categories( $instance );
		if ( !$list )
			return;

		$output  = $args['before_widget'];
		if ( $title = apply_filters( 'widget_title', $instance['title'] ) )
			$output .= $args['before_title'] . $title . $args['after_title'];
		$output .= "<ul>\n{$list}</ul>\n";
		$output .= $args['after_widget'];

		echo $output;
	}
}
?>