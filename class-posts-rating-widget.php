<?php
class Posts_Rating_Widget extends WP_Widget {

	public function __construct() {
		$widget_ops = array(
			'classname' => 'posts_rating_widget',
			'description' => 'Displays recent posts with ratings.'
		);
		parent::__construct('posts_rating_widget', 'Posts Rating Widget', $widget_ops);

		add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
		add_action('wp_ajax_rate_post', array($this, 'rate_post'));
		add_action('wp_ajax_nopriv_rate_post', array($this, 'rate_post'));
	}

	public function widget($args, $instance) {
		echo $args['before_widget'];
		if (!empty($instance['title'])) {
			echo $args['before_title'] . apply_filters('widget_title', $instance['title']) . $args['after_title'];
		}

		$num_posts = !empty($instance['num_posts']) ? absint($instance['num_posts']) : 5;
		$show_rating = !empty($instance['show_rating']) ? $instance['show_rating'] : false;
		$rating_position = !empty($instance['rating_position']) ? $instance['rating_position'] : 'bottom-left';

		$this->display_posts($num_posts, $show_rating, $rating_position);

		echo $args['after_widget'];
	}

	public function form($instance) {
		$title = !empty($instance['title']) ? $instance['title'] : __('Последние статьи', 'text_domain');
		$num_posts = !empty($instance['num_posts']) ? absint($instance['num_posts']) : 5;
		$show_rating = !empty($instance['show_rating']) ? $instance['show_rating'] : false;
		$rating_position = !empty($instance['rating_position']) ? $instance['rating_position'] : 'bottom-left';

		?>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('title')); ?>"><?php _e('Title:', 'text_domain'); ?></label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>" name="<?php echo esc_attr($this->get_field_name('title')); ?>" type="text" value="<?php echo esc_attr($title); ?>">
        </p>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('num_posts')); ?>"><?php _e('Number of Posts:', 'text_domain'); ?></label>
            <input class="tiny-text" id="<?php echo esc_attr($this->get_field_id('num_posts')); ?>" name="<?php echo esc_attr($this->get_field_name('num_posts')); ?>" type="number" step="1" min="1" value="<?php echo esc_attr($num_posts); ?>" size="3">
        </p>
        <p>
            <input class="checkbox" type="checkbox" <?php checked($show_rating, 'on'); ?> id="<?php echo esc_attr($this->get_field_id('show_rating')); ?>" name="<?php echo esc_attr($this->get_field_name('show_rating')); ?>" />
            <label for="<?php echo esc_attr($this->get_field_id('show_rating')); ?>"><?php _e('Show Rating:', 'text_domain'); ?></label>
        </p>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('rating_position')); ?>"><?php _e('Rating Panel Position:', 'text_domain'); ?></label>
            <select id="<?php echo esc_attr($this->get_field_id('rating_position')); ?>" name="<?php echo esc_attr($this->get_field_name('rating_position')); ?>" class="widefat">
                <option value="bottom-left" <?php selected($rating_position, 'bottom-left'); ?>><?php _e('Bottom-Left', 'text_domain'); ?></option>
                <option value="bottom-center" <?php selected($rating_position, 'bottom-center'); ?>><?php _e('Bottom-Center', 'text_domain'); ?></option>
                <option value="bottom-right" <?php selected($rating_position, 'bottom-right'); ?>><?php _e('Bottom-Right', 'text_domain'); ?></option>
            </select>
        </p>
		<?php
	}

	public function update($new_instance, $old_instance) {
		$instance = array();
		$instance['title'] = (!empty($new_instance['title'])) ? strip_tags($new_instance['title']) : '';
		$instance['num_posts'] = (!empty($new_instance['num_posts'])) ? absint($new_instance['num_posts']) : 0;
		$instance['show_rating'] = (!empty($new_instance['show_rating'])) ? strip_tags($new_instance['show_rating']) : '';
		$instance['rating_position'] = (!empty($new_instance['rating_position'])) ? strip_tags($new_instance['rating_position']) : '';
		return $instance;
	}

	public function display_posts($num_posts, $show_rating, $rating_position) {
		$recent_posts = wp_get_recent_posts(array(
			'numberposts' => $num_posts,
			'post_status' => 'publish'
		));

		if (count($recent_posts) == 0) {
			echo '<p>' . __('No posts found.', 'text_domain') . '</p>';
			return;
		}

		echo '<ul class="custom-posts-list">';
		foreach ($recent_posts as $post) {
			$post_title = get_the_title($post['ID']);
			$post_date = get_the_date('F j, Y', $post['ID']);
			$average_rating = get_post_meta($post['ID'], '_average_rating', true) ?: 0;

			echo '<li>';
			echo '<span class="post-title">' . esc_html($post_title) . '</span>';
			echo '<span class="post-date">' . esc_html($post_date) . '</span>';
			echo '<span class="post-rating">Rating: ' . esc_html($average_rating) . '</span>';
			if ($show_rating) {
				echo '<div class="rating-panel ' . esc_attr($rating_position) . '" data-post-id="' . esc_attr($post['ID']) . '">';
				echo '<span>' . __('Rate this post:', 'text_domain') . '</span>';
				for ($i = 1; $i <= 5; $i++) {
					echo '<span class="star" data-value="' . $i . '">&#9733;</span>';
				}
				echo '</div>';
			}
			echo '</li>';
		}
		echo '</ul>';
	}

	public function rate_post() {
		if (!isset($_POST['post_id']) || !isset($_POST['rating'])) {
			wp_send_json_error('Invalid data');
		}

		$post_id = absint($_POST['post_id']);
		$rating = absint($_POST['rating']);

		if ($rating < 1 || $rating > 5) {
			wp_send_json_error('Invalid rating');
		}

		$current_rating = get_post_meta($post_id, '_average_rating', true);
		$num_ratings = get_post_meta($post_id, '_num_ratings', true);

		if (!$current_rating) {
			$current_rating = 0;
		}
		if (!$num_ratings) {
			$num_ratings = 0;
		}

		$new_rating = (($current_rating * $num_ratings) + $rating) / ($num_ratings + 1);
		$num_ratings++;

		update_post_meta($post_id, '_average_rating', $new_rating);
		update_post_meta($post_id, '_num_ratings', $num_ratings);

		wp_send_json_success(array('new_rating' => $new_rating));
	}

	public function enqueue_scripts() {
		wp_enqueue_style('posts-rating-widget', plugins_url('style.css', __FILE__));
		wp_enqueue_script('posts-rating-widget', plugins_url('script.js', __FILE__), array('jquery'), null, true);

		wp_localize_script('posts-rating-widget', 'ajax_obj', array(
			'ajax_url' => admin_url('admin-ajax.php')
		));
	}

}