<?php
/**
 * Handles the registration of the Meeting custom post type.
 * @author Jim Barnes
 * @since 1.0.0
 **/
if ( ! class_exists( 'UCF_Meeting_PostType' ) )  {
	class UCF_Meeting_PostType {
		/**
		 * Registers the custom post type.
		 * @author Jim Barnes
		 * @since 1.0.0
		 **/
		public static function register() {
			$labels = apply_filters(
				'ucf_meeting_labels',
				array(
					'singular' => 'Meeting',
					'plural'   => 'Meetings',
					'slug'     => 'meetings'
				)
			);
			register_post_type( 'meeting', self::args( $labels ) );
			add_action( 'add_meta_boxes', array( 'UCF_Meeting_PostType', 'register_metabox' ) );
			add_action( 'save_post', array( 'UCF_Meeting_PostType', 'save_metabox' ) );
		}

		/**
		 * Adds a metabox to the Document custom post type.
		 * @author Jim Barnes
		 * @since 1.0.0
		 **/
		public static function register_metabox() {
			add_meta_box(
				'ucf_meetings_metabox',
				'Meeting Fields',
				array( 'UCF_Meeting_PostType', 'register_metafields' ),
				'meeting',
				'normal',
				'high'
			);
		}

		/**
		 * Adds metafields to the metabox
		 * @author Jim Barnes
		 * @since 1.0.0
		 * @param $post WP_POST object
		 **/
		public static function register_metafields( $post ) {
			wp_enqueue_script('jquery-ui-datepicker');
			wp_nonce_field( 'ucf_meeting_nonce_save', 'ucf_meeting_nonce' );
			$date = get_post_meta( $post->ID, 'ucf_meeting_date', TRUE );
			$start_time = get_post_meta( $post->ID, 'ucf_meeting_start_time', TRUE );
			$end_time = get_post_meta( $post->ID, 'ucf_meeting_end_time', TRUE );
?>
			<div class="custom-field">
				<p class="label">
					<label for="ucf_meeting_date">Date</label>
				</p>
				<div class="input-wrap">
					<input type="date" id="ucf_meeting_date" name="ucf_meeting_date" <?php echo ( ! empty( $date ) ) ? 'value="' . $date . '"' : ''; ?>>
				</div>
			</div>
			<div class="custom-field">
				<p class="label">
					<label for="ucf_meeting_start_time">Start Time</label>
				</p>
				<div class="input-wrap">
					<input type="time" id="ucf_meeting_start_time" name="ucf_meeting_start_time" <?php echo ( ! empty( $start_time ) ) ? 'value="' . $start_time . '"' : '' ?>>
				</div>
			</div>
			<div class="custom-field">
				<p class="label">
					<label for="ucf_meeting_end_time">End Time</label>
				</p>
				<div class="input-wrap">
					<input type="time" id="ucf_meeting_end_time" name="ucf_meeting_end_time" <?php echo ( ! empty( $end_time ) ) ? 'value="' . $end_time . '"' : '' ?>>
				</div>
			</div>
<?php
		}

		/**
		 * Handles saving the data in the metabox
		 * @author Jim Barnes
		 * @since 1.0.0
		 * @param $post WP_POST object
		 **/
		public static function save_metabox( $post_id ) {
			$post_type = get_post_type( $post_id );

			// If this isn't a meeting, return.
			if ( "meeting" !== $post_type ) return;

			if ( isset( $_POST['ucf_meeting_date'] ) ) {
				// Ensure date is valid.
				$date = sanitize_text_field( $_POST['ucf_meeting_date'] );

				try {
					$temp = new DateTime( sanitize_text_field( $_POST['ucf_meeting_date'] ) );
				} catch( Exception $e ) {
					$date = null;
				}

				if ( $date ) {
					update_post_meta( $post_id, 'ucf_meeting_date', $date );
				}
			}

			if ( isset( $_POST['ucf_meeting_start_time'] ) ) {
				// Ensure is valid time.
				update_post_meta( $post_id, 'ucf_meeting_start_time', sanitize_text_field( $_POST['ucf_meeting_start_time'] ) );
			}

			if ( isset( $_POST['ucf_meeting_end_time'] ) ) {
				// Ensure is valid time.
				update_post_meta( $post_id, 'ucf_meeting_end_time', sanitize_text_field( $_POST['ucf_meeting_end_time'] ) );
			}
		}

		/**
		 * Returns an array of labels for the custom post type.
		 * @author Jim Barnes
		 * @since 1.0.0
		 * @param $singular string | The singular form for the CPT labels.
		 * @param $plural string | The plural form for the CPT labels.
		 * @return Array
		 **/
		public static function labels( $singular, $plural ) {
			return array(
				'name'                  => _x( $plural, 'Post Type General Name', 'ucf_meetings' ),
				'singular_name'         => _x( $singular, 'Post Type Singular Name', 'ucf_meetings' ),
				'menu_name'             => __( $plural, 'ucf_meetings' ),
				'name_admin_bar'        => __( $singular, 'ucf_meetings' ),
				'archives'              => __( $plural . ' Archives', 'ucf_meetings' ),
				'parent_item_colon'     => __( 'Parent ' . $singular . ':', 'ucf_meetings' ),
				'all_items'             => __( 'All ' . $plural, 'ucf_meetings' ),
				'add_new_item'          => __( 'Add New ' . $singular, 'ucf_meetings' ),
				'add_new'               => __( 'Add New', 'ucf_meetings' ),
				'new_item'              => __( 'New ' . $singular, 'ucf_meetings' ),
				'edit_item'             => __( 'Edit ' . $singular, 'ucf_meetings' ),
				'update_item'           => __( 'Update ' . $singular, 'ucf_meetings' ),
				'view_item'             => __( 'View ' . $singular, 'ucf_meetings' ),
				'search_items'          => __( 'Search ' . $plural, 'ucf_meetings' ),
				'not_found'             => __( 'Not found', 'ucf_meetings' ),
				'not_found_in_trash'    => __( 'Not found in Trash', 'ucf_meetings' ),
				'featured_image'        => __( 'Featured Image', 'ucf_meetings' ),
				'set_featured_image'    => __( 'Set featured image', 'ucf_meetings' ),
				'remove_featured_image' => __( 'Remove featured image', 'ucf_meetings' ),
				'use_featured_image'    => __( 'Use as featured image', 'ucf_meetings' ),
				'insert_into_item'      => __( 'Insert into ' . $singular, 'ucf_meetings' ),
				'uploaded_to_this_item' => __( 'Uploaded to this ' . $singular, 'ucf_meetings' ),
				'items_list'            => __( $plural . ' list', 'ucf_meetings' ),
				'items_list_navigation' => __( $plural . ' list navigation', 'ucf_meetings' ),
				'filter_items_list'     => __( 'Filter ' . $plural . ' list', 'ucf_meetings' ),
			);
		}

		/**
		 * Returns the arguments for registering the custom post type.
		 * @author Jim Barnes
		 * @since 1.0.0
		 * @param $singular string | The singular form for the CPT labels.
		 * @param $plural string | The plural form for the CPT labels.
		 * @return Array
		 **/
		public static function args( $labels ) {
			$singular = ucwords( $labels['singular'] );
			$plural = ucwords( $labels['plural'] );
			$slug = $labels['slug'];

			$args = array(
				'label'                 => __( $plural, 'ucf_meetings' ),
				'description'           => __( $plural, 'ucf_meetings' ),
				'labels'                => self::labels( $singular, $plural ),
				'supports'              => array( 'title', 'revisions', ),
				'taxonomies'            => self::taxonomies(),
				'hierarchical'          => false,
				'public'                => true,
				'show_ui'               => true,
				'show_in_menu'          => true,
				'menu_position'         => 5,
				'menu_icon'             => 'dashicons-admin-users',
				'show_in_admin_bar'     => true,
				'show_in_nav_menus'     => true,
				'can_export'            => true,
				'has_archive'           => true,		
				'exclude_from_search'   => false,
				'publicly_queryable'    => true,
				'capability_type'       => 'post',
			);

			$args = apply_filters( 'ucf_meetings_post_type_args', $args );

			return $args;
		}

		/**
		 * Returns a list of taxonomies to add during post type registration.
		 * @author Jim Barnes
		 * @since 1.0.0
		 * @return Array<string> 
		 **/
		public static function taxonomies() {
			$taxonomies = array(
				'post_tag'
			);

			$taxonomies = apply_filters( 'ucf_meetings_taxonomies', $taxonomies );

			foreach( $taxonomies as $taxonomy ) {
				if ( ! taxonomy_exists( $taxonomy ) ) {
					unset( $taxonomies[$taxonomy] );
				}
			}

			return $taxonomies;
		}
	}
}
