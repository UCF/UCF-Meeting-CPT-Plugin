<?php
/**
 * General class for retrieving meetings
 **/
if ( ! class_exists( 'UCF_Meeting' ) ) {
	class UCF_Meeting {
		/**
		 * Retrieves an array of meetings, filtered by provided arguments.
		 * @author Jim Barnes
		 * @since 1.0.0
		 * @param $args Array | An array of WP_Query Arguments
		 * @return Array<WP_Post>
		 **/
		public static function all( $args=array() ) {
			$defaults = array(
				'post_type'      => 'meeting',
				'posts_per_page' => -1
			);

			$args = wp_parse_args( $args, $defaults );

			$posts = get_posts( $args );

			foreach( $posts as $post ) {
				$post->metadata = get_post_meta( $post->ID );

				foreach( $post->metadata as $key=>$value ) {
					if ( is_array( $value ) && count( $value ) === 1 ) {
						$post->metadata[$key] = $value[0];
					}
				}

				$post->metadata = apply_filters( 'ucf_meeting_format_metadata', $post->metadata );
			}

			return $posts;
		}

		/**
		 * Retrieves an array of tax terms and the related posts.
		 * @author Jim Barnes
		 * @since 1.0.0
		 * @param $args Array | An array of WP_Query Arguments
		 * @return Array<string> => Array<WP_Post>
		 **/
		public static function group_by_tax( $taxonomy='post_tag', $args=array() ) {
			$posts = self::all( $args );
			$retval = array();

			foreach( $posts as $post ) {
				$terms = wp_get_post_terms( $post->ID, $taxonomy );

				foreach( $terms as $term ) {
					$name = $term['name'];
					if ( isset( $retval[$name] ) ) {
						$retval[$name][] = $post;
					} else {
						$retval[$name] = array( $post );
					}
				}
			}

			return $retval;
		}

		/**
		 * Retrieves an array of years with associated posts.
		 * @author Jim Barnes
		 * @since 1.0.0
		 * @param $args Array | An array of WP_Query Arguments
		 * @return Array<int> => Array<WP_Post>
		 **/
		public static function group_by_year( $args=array() ) {
			$posts = self::all( $args );
			$retval = array();

			foreach( $posts as $post ) {
				$date = new DateTime( $post->metadata['ucf_meeting_date'] );
				$year = (string)$date->format( 'Y' );

				// If year exists in array, add this to it.
				if ( isset( $retval[$year] ) ) {
					$retval[$year][] = $post;
				} else {
					$retval[$year] = array( $post );
				}
			}

			return $retval;
		}
	}
}
