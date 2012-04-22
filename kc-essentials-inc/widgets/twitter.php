<?php

/**
 * @package KC_Essentials
 * @version 0.1
 */


class kc_widget_twitter extends WP_Widget {
	var $defaults;

	function __construct() {
		$widget_ops = array( 'classname' => 'kcw_twitter', 'description' => __('Simple Twitter timeline', 'kc-essentials') );
		$control_ops = array( 'width' => 300, 'height' => 450 );
		parent::__construct( 'kcw_twitter', 'KC Twitter Timeline', $widget_ops, $control_ops );
		$this->defaults = array(
			'title'       => '',
			'username'    => '',
			'expiration'  => 60,
			'count'       => 5,
			'follow_text' => __('Follow me', 'kc-essentials'),
			'show_date'   => true,
			'date_format' => 'relative',
			'date_custom' => get_option('date_format'),
			'debug'       => 0
		);
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

		<h5 class="kcw-head"><?php _e('Basic', 'kc-essentials') ?></h5>
		<ul class="kcw-control-block">
			<li>
				<label for="<?php echo $this->get_field_id('username') ?>"><?php _e('Username', 'kc-essentials') ?></label>
				<?php echo kcForm::input(array(
					'attr'    => array(
						'id'   => $this->get_field_id('username'),
						'name' => $this->get_field_name('username')
					),
					'current' => $instance['username']
				)) ?>
			</li>
			<li>
				<label for="<?php echo $this->get_field_id('expiration') ?>" title="<?php _e('Cache expiration time in minutes, minimum is 5', 'kc-essentials'); ?>"><?php _e('Expiration (m)', 'kc-essentials') ?> <small class="impo">(?)</small></label>
				<?php echo kcForm::input(array(
					'attr'    => array(
						'id'   => $this->get_field_id('expiration'),
						'name' => $this->get_field_name('expiration')
					),
					'current' => $instance['expiration']
				)) ?>
			</li>
			<li>
				<label for="<?php echo $this->get_field_id('count') ?>" title="<?php _e('Number of statuses to show, minimum is 1', 'kc-essentials') ?>"><?php _e('Count', 'kc-essentials') ?> <small class="impo">(?)</small></label>
				<?php echo kcForm::input(array(
					'attr'    => array(
						'id'   => $this->get_field_id('count'),
						'name' => $this->get_field_name('count')
					),
					'current' => $instance['count']
				)) ?>
			</li>
			<li>
				<label for="<?php echo $this->get_field_id('show_date') ?>"><?php _e('Show date', 'kc-essentials') ?></label>
				<?php echo kcForm::field(array(
					'type'    => 'select',
					'attr'    => array(
						'id'         => $this->get_field_id('show_date'),
						'name'       => $this->get_field_name('show_date'),
						'class'      => 'hasdep',
						'data-child' => '#p-'.$this->get_field_id('date_format')
					),
					'options' => kcSettings_options::$yesno,
					'none'    => false,
					'current' => $instance['show_date']
				)) ?>
			</li>
			<li id="<?php echo 'p-'.$this->get_field_id('date_format') ?>" data-dep="1">
				<label for="<?php echo $this->get_field_id('date_format') ?>"><?php _e('Date format', 'kc-essentials') ?></label>
				<?php echo kcForm::field(array(
					'type'    => 'select',
					'attr'    => array(
						'id'         => $this->get_field_id('date_format'),
						'name'       => $this->get_field_name('date_format'),
						'class'      => 'hasdep',
						'data-child' => '#p-'.$this->get_field_id('date_custom')
					),
					'options' => array(
						'relative'   => __('Relative', 'kc-essentials'),
						'relative_m' => __('Relative if &lt; 30 days', 'kc-essentials'),
						'global'     => __('Use global setting', 'kc-essentials'),
						'custom'     => __('Custom', 'kc-essentials')
					),
					'none'    => false,
					'current' => $instance['date_format']
				)) ?>
			</li>
			<li id="<?php echo 'p-'.$this->get_field_id('date_custom') ?>" data-dep="custom">
				<label for="<?php echo $this->get_field_id('date_custom') ?>" title="<?php _e('Use PHP date format', 'kc-essentials') ?>"><?php _e('Custom format', 'kc-essentials') ?> <small class="impo">(?)</small></label>
				<?php echo kcForm::input(array(
					'attr'    => array(
						'id'   => $this->get_field_id('date_custom'),
						'name' => $this->get_field_name('date_custom')
					),
					'current' => $instance['date_custom']
				)) ?>
			</li>
			<li>
				<label for="<?php echo $this->get_field_id('follow_text') ?>" title="<?php _e('Leave empty to disable', 'kc-essentials') ?>"><?php _e('Follow text', 'kc-essentials') ?> <small class="impo">(?)</small></label>
				<?php echo kcForm::input(array(
					'attr'    => array(
						'id'   => $this->get_field_id('follow_text'),
						'name' => $this->get_field_name('follow_text')
					),
					'current' => $instance['follow_text']
				)) ?>
			</li>
		</ul>
	<?php }


	function update( $new, $old ) {
		$new['expiration'] = ( isset($new['expiration']) && absint($new['expiration']) >= 5 ) ? $new['expiration'] : $this->defaults['expiration'];
		$new['count'] = ( isset($new['count']) && absint($new['count']) >= 1 ) ? $new['count'] : $this->defaults['count'];
		if ( $new['date_format'] == 'custom' && $new['date_custom'] == '' )
			$new['date_custom'] = get_option( 'date_format' );

		return $new;
	}


	function widget( $args, $instance ) {
		if ( !$instance['username'] )
			return;

		$list = get_transient( "kcw_twitter_{$instance['username']}" );
		if ( !$list || count($list) < $instance['count'] ) {
			$json = wp_remote_get("http://api.twitter.com/1/statuses/user_timeline.json?screen_name={$instance['username']}&count={$instance['count']}");
			if ( is_wp_error($json) || $json['response']['code'] == '400' ) {
				return;
			}
			else {
				$list = json_decode( $json['body'], true );
				set_transient( "kcw_twitter_{$instance['username']}", $list, $instance['expiration'] * 60 );
			}
		}

		$out = "<ul>\n";
		$now = time();
		if ( $instance['count'] < count($list) )
			$list = array_slice( $list, 0, $instance['count'] );

		foreach ( $list as $idx => $item ) {
			$text = $item['text'];
			$text = apply_filters(
				'kcw_twitter_status_text',
				"<p>".preg_replace(
					array('/(^|\s)#(\w*[a-zA-Z_]+\w*)/', '/(^|\s)@(\w*[a-zA-Z_]+\w*)/'),
					array('\1#<a href="http://search.twitter.com/search?q=%23\2">\2</a>',
					'\1<a href="http://twitter.com/\2">@\2</a>'),
					$text
				)."</p>",
				$text,
				$item,
				$this,
				$instance
			);
			$out .= "<li class='item'>";
			$out .= $text;
			if ( $instance['show_date'] ) {
				$date = strtotime( $item['created_at'] );
				$diff = (int) abs( $now - $date );
				if ( $instance['date_format'] == 'relative' || ($instance['date_format'] == 'relative_m' && $diff <= 2592000) )
					$date = sprintf( __('%s ago', 'kc-essentials'), human_time_diff($date) );
				elseif ( $instance['date_format'] == 'global' || ($instance['date_format'] == 'relative_m' && $diff >= 2592000) )
					$date = sprintf( __('%1$s at %2$s', 'kc-essentials'), date(get_option('date_format'), $date), date(get_option('time_format'), $date) );
				else
					$date = date( $instance['date_custom'], $date );
				$out .= apply_filters( 'kcw_twitter_date', "<abbr class='datetime' title='{$item['created_at']}'>{$date}</abbr>", $date, $item['created_at'], $this, $instance );
			}
			$out .= "</li>\n";
		}
		$out .= "</ul>\n";

		if ( $instance['follow_text'] ) {
			$out .= apply_filters( 'kcw_twitter_follow_text', "<a href='http://twitter.com/{$instance['username']}' class='follow'><span>{$instance['follow_text']}</span></a>", $this, $instance );
		} ?>

<?php echo $args['before_widget'] ?>
<?php if ( $title = apply_filters( 'widget_title', $instance['title'], $this, $instance ) ) echo $args['before_title'] . $title . $args['after_title']; ?>
<?php	do_action( 'kcw_twitter_before_list', $this, $instance ); ?>
<?php echo $out; ?>
<?php do_action( 'kcw_twitter_after_list', $this, $instance ); ?>
<?php echo $args['after_widget']; ?>
	<?php }
}
?>