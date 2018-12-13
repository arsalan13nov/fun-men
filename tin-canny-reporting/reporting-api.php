<?php

namespace uncanny_learndash_reporting;

if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Class Sample
 * @package uncanny_custom_toolkit
 */
class ReportingApi extends Config {

	private static $user_role = null;

	private static $group_leaders_group_ids = null;

	private static $isolated_group_id = 0;

	/**
	 * Class constructor
	 */
	public function __construct() {

		//register api class
		add_action( 'rest_api_init', array( __CLASS__, 'reporting_api' ) );

	}

	public static function reporting_api() {

		if ( isset( $_GET['group_id'] ) ) {
			self::$isolated_group_id = absint( $_GET['group_id'] );
		}

		// Call get all courses and general user data
		register_rest_route( 'uncanny_reporting/v1', '/courses_overview/', array(
			'methods'  => 'GET',
			'callback' => array( __CLASS__, 'get_courses_overview' )
		) );

		//
		register_rest_route( 'uncanny_reporting/v1', '/users_completed_courses/', array(
			'methods'  => 'GET',
			'callback' => array( __CLASS__, 'get_users_completed_courses' ),

		) );

		//
		register_rest_route( 'uncanny_reporting/v1', '/course_modules/', array(
			'methods'  => 'GET',
			'callback' => array( __CLASS__, 'get_course_modules' ),

		) );

		//
		register_rest_route( 'uncanny_reporting/v1', '/assignment_data/', array(
			'methods'  => 'GET',
			'callback' => array( __CLASS__, 'get_assignment_data' ),

		) );

		//
		register_rest_route( 'uncanny_reporting/v1', '/tincan_data/(?P<user_ID>\d+)', array(
			'methods'  => 'GET',
			'callback' => array( __CLASS__, 'get_tincan_data' ),

		) );

		// Update it the user see the Tin Can tables
		register_rest_route( 'uncanny_reporting/v1', '/show_tincan/(?P<show_tincan>\d+)', array(
			'methods'  => 'GET',
			'callback' => array( __CLASS__, 'show_tincan_tables' ),

		) );

		// Update it the user see the Tin Can tables
		register_rest_route( 'uncanny_reporting/v1', '/disable_mark_complete/(?P<disable_mark_complete>\d+)', array(
			'methods'  => 'GET',
			'callback' => array( __CLASS__, 'disable_mark_complete' ),

		) );

		// Update it the user see the Tin Can tables
		register_rest_route( 'uncanny_reporting/v1', '/nonce_protection/(?P<nonce_protection>\d+)', array(
			'methods'  => 'GET',
			'callback' => array( __CLASS__, 'nonce_protection' ),

		) );

		// Reset Tin Can Data
		register_rest_route( 'uncanny_reporting/v1', '/reset_tincan_data', array(
			'methods'  => 'GET',
			'callback' => array( __CLASS__, 'reset_tincan_data' ),

		) );

		register_rest_route( 'uncanny_reporting/v1', '/reset_bookmark_data', array(
			'methods'  => 'GET',
			'callback' => array( __CLASS__, 'reset_bookmark_data' ),

		) );


		//dashboard_data
		register_rest_route( 'uncanny_reporting/v1', '/dashboard_data/', array(
			'methods'  => 'GET',
			'callback' => array( __CLASS__, 'get_dashboard_data' ),

		) );

	}

	/**
	 * Collect general user course data and LearnDash Labels
	 *
	 * @return array
	 */
	public static function get_courses_overview() {

		error_reporting( 0 );

		$json_return            = [];
		$json_return['success'] = false;
		$json_return['message'] = __( 'You do not have permission to access this information', 'uncanny-learndash-reporting' );
		$json_return['data']    = [];

		$current_user_ID = get_current_user_id();

		// is the user logged in
		if ( ! $current_user_ID ) {
			$json_return['message'] = __( 'You must be logged in to view this data.', 'uncanny-learndash-reporting' );
			$json_return['success'] = false;

			return $json_return;
		}

		if ( current_user_can( 'tincanny_reporting' ) ) {

			// is it an administrator
			if ( 'group_leader' === self::get_user_role() ) {

				// Verify the group leader has groups assigned
				if ( ! count( self::get_administrators_group_ids() ) ) {

					$json_return['message'] = __( 'Group Leader has no groups assigned', 'uncanny-learndash-reporting' );
					$json_return['success'] = false;

					return $json_return;
				}

			}

			$json_return['message']         = '';
			$json_return['success']         = true;
			$json_return['data']            = self::course_progress_data();
			$json_return['learnDashLabels'] = self::get_labels();
			$json_return['links']           = self::get_links();
			$json_return['get']             = self::$isolated_group_id;

		}

		return $json_return;

	}

	/**
	 * Collect general course data
	 *
	 * @return array|bool
	 */
	private static function course_progress_data() {

		// Get all user data
		$user_list = self::get_all_users_data();

		// Get all list of course
		$course_list = self::get_course_list();

		// Get all users from groups
		$groups_list = self::get_groups_list();

		return array(
			'userList'   => $user_list,
			'courseList' => $course_list,
			'groupsList' => $groups_list,
			'success'    => true
		);

	}

	public static function get_users_completed_courses() {

		error_reporting( 0 );

		$json_return            = [];
		$json_return['success'] = false;
		$json_return['message'] = __( 'You do not have permission to access this information', 'uncanny-learndash-reporting' );
		$json_return['data']    = [];

		global $wpdb;

		// check current user if admin or group leader
		if ( current_user_can( 'tincanny_reporting' ) ) {

			// Modify custom query to restrict data to group leaders available data
			if ( 'group_leader' === self::get_user_role() && ! self::$isolated_group_id ) {

				// Verify the group leader has groups assigned
				if ( ! count( self::get_administrators_group_ids() ) ) {

					$json_return['message'] = __( 'Group Leader has no groups assigned', 'uncanny-learndash-reporting' );
					$json_return['success'] = false;

					return $json_return;
				}

				foreach ( self::get_administrators_group_ids() as $group_id ) {

					// restrict group leader to a single group it its set
					if ( self::$isolated_group_id && $group_id == self::$isolated_group_id ) {
						$meta_keys[] = "'learndash_group_users_" . $group_id . "'";
					} else {
						$meta_keys[] = "'learndash_group_users_" . $group_id . "'";
					}


				}
				$imploded_meta_keys             = implode( ',', $meta_keys );
				$restrict_group_leader_usermeta = "AND user_id IN (SELECT user_id FROM $wpdb->usermeta WHERE meta_key IN ($imploded_meta_keys) )";
			} elseif ( self::$isolated_group_id ) {
				$restrict_group_leader_usermeta = "AND user_id IN (SELECT user_id FROM $wpdb->usermeta WHERE meta_key = 'learndash_group_users_" . self::$isolated_group_id . "' )";
			} else {
				$restrict_group_leader_usermeta = '';
			}

			if ( is_multisite() ) {

				$blog_ID          = get_current_blog_id();

				$base_capabilities_key = $wpdb->base_prefix . 'capabilities';
				$site_capabilities_key = $wpdb->base_prefix . $blog_ID . '_capabilities';

				if ( 1 === $blog_ID ) {
					$key = $base_capabilities_key;
				} else {
					$key = $site_capabilities_key;
				}

				$restrict_to_blog = "AND  ID IN (SELECT user_id FROM $wpdb->usermeta WHERE meta_key = '{$key}')";

			} else {
				$restrict_to_blog = '';
			}

			// Get all user data
			// Users' Progress
			$sql_string        = "SELECT user_id, meta_key, meta_value FROM $wpdb->usermeta WHERE meta_key LIKE 'course_completed_%' $restrict_group_leader_usermeta $restrict_to_blog";

			$courses_completed = $wpdb->get_results( $sql_string );

			if ( is_array( $courses_completed ) ) {
				$json_return['message'] = '';
				$json_return['success'] = true;
				foreach ( $courses_completed as $course_completed ) {
					$user_id                           = (int) $course_completed->user_id;
					$course_id                         = explode( '_', $course_completed->meta_key );
					$course_id                         = (int) $course_id[2];
					$time_stamp                        = (int) $course_completed->meta_value;
					$date_format                       = 'Y-m-d';
					$json_return['data'][ $user_id ][] = array( $course_id, date( $date_format, $time_stamp ) );
				}
			}

		}

		return $json_return;

	}

	private static function get_all_users_data() {

		global $wpdb;

		// Modify custom query to restrict data to group leaders available data
		if ( 'group_leader' === self::get_user_role() && ! self::$isolated_group_id ) {

			foreach ( self::get_administrators_group_ids() as $group_id ) {

				// restrict group leader to a single group it its set
				if ( self::$isolated_group_id && $group_id == self::$isolated_group_id ) {
					$meta_keys[] = "'learndash_group_users_" . $group_id . "'";
				} else {
					$meta_keys[] = "'learndash_group_users_" . $group_id . "'";
				}

			}
			$imploded_meta_keys             = implode( ',', $meta_keys );
			$restrict_group_leader_users    = "WHERE ID IN (SELECT user_id FROM $wpdb->usermeta WHERE meta_key IN ($imploded_meta_keys) )";
			$restrict_group_leader_usermeta = "AND user_id IN (SELECT user_id FROM $wpdb->usermeta WHERE meta_key IN ($imploded_meta_keys) )";
		} elseif ( self::$isolated_group_id ) {
			$restrict_group_leader_users    = "WHERE ID IN (SELECT user_id FROM $wpdb->usermeta WHERE meta_key = 'learndash_group_users_" . self::$isolated_group_id . "' )";
			$restrict_group_leader_usermeta = "AND user_id IN (SELECT user_id FROM $wpdb->usermeta WHERE meta_key = 'learndash_group_users_" . self::$isolated_group_id . "' )";
		} else {
			$restrict_group_leader_users    = '';
			$restrict_group_leader_usermeta = '';
		}

		if ( is_multisite() ) {
			$blog_ID = get_current_blog_id();

			$base_capabilities_key = $wpdb->base_prefix . 'capabilities';
			$site_capabilities_key = $wpdb->base_prefix . $blog_ID . '_capabilities';

			if ( 1 === $blog_ID ) {
				$key = $base_capabilities_key;
			} else {
				$key = $site_capabilities_key;
			}


			if ( '' === $restrict_group_leader_users ) {
				$restrict_to_blog = "WHERE ID IN (SELECT user_id FROM $wpdb->usermeta WHERE meta_key = '{$key}')";
			} else {
				$restrict_to_blog = "AND  ID IN (SELECT user_id FROM $wpdb->usermeta WHERE meta_key = '{$key}')";
			}

		} else {
			$restrict_to_blog = '';
		}


		// Users
		$sql_string = "SELECT ID, user_email, display_name, user_registered as user_group FROM $wpdb->users $restrict_group_leader_users $restrict_to_blog"; // ADDED user_registered as user_group
		$users      = $wpdb->get_results( $sql_string );

		// CUSTOM MODIFICATION IN REPORTING TO ADD GROUPS COLUMN
		foreach( $users as $key => $user ) {
			
			// Get user groups	
			$groups_array = learndash_get_users_group_ids( $user->ID );
			
			// If enrolled in groups
			if( !empty( $groups_array ) ) {
				
				$grp_names = array();

				// Loop through all group ids to get the group title
				foreach( $groups_array as $grp_id ) {
					$grp_names[] = get_the_title( $grp_id );
					
				}

				// If group(s) found, add them in new col
				if( is_array( $grp_names ) ) 
					$users[$key]->user_group = implode( ", ", $grp_names );
			}
		}
		// END - CUSTOM MODIFICATION IN REPORTING TO ADD GROUPS COLUMN

		if ( empty( $users ) ) {
			return array(
				'message' => __( 'No users found.', 'uncanny-learndash-reporting' )
			);
		}

		$rearranged_users = [];

		foreach ( $users as $user ) {
			$user_id                      = (int) $user->ID;
			$rearranged_users[ $user_id ] = $user;
		}

		if ( is_multisite() ) {
			$blog_ID = get_current_blog_id();

			$base_capabilities_key = $wpdb->base_prefix . 'capabilities';
			$site_capabilities_key = $wpdb->base_prefix . $blog_ID . '_capabilities';

			if ( 1 === $blog_ID ) {
				$key = $base_capabilities_key;
			} else {
				$key = $site_capabilities_key;
			}

			$restrict_to_blog = "AND  ID IN (SELECT user_id FROM $wpdb->usermeta WHERE meta_key = '{$key}')";

		} else {
			$restrict_to_blog = '';
		}

		// Users' Progress
		$sql_string = "SELECT user_id, meta_value FROM $wpdb->usermeta WHERE meta_key = '_sfwd-course_progress' $restrict_group_leader_usermeta $restrict_to_blog";
		$progress   = $wpdb->get_results( $sql_string );

		//Users' Completed Timers
		$sql_string       = "SELECT user_id, meta_key, meta_value FROM $wpdb->usermeta WHERE meta_key LIKE 'course_timer_completed_%' $restrict_group_leader_usermeta $restrict_to_blog";
		$timers           = $wpdb->get_results( $sql_string );
		$timer_rearranged = [];
		foreach ( $timers as $timer ) {
			$user_id   = (int) $timer->user_id;
			$timer_key = $timer->meta_key;
			$timer_key = explode( '_', $timer_key );
			$time      = $timer->meta_value;
			$seconds   = 0;
			if ( '' !== $time ) {
				$seconds = explode( ':', $time );
				$seconds = ( intval( $seconds[0] ) * 60 * 60 ) + ( intval( $seconds[1] ) * 60 ) + intval( $seconds[2] );
			}

			$timer_rearranged[ $user_id ][ (int) $timer_key[3] ] = $seconds;
		}

		//Users' Time Spent
		$sql_string             = "SELECT user_id, meta_key, meta_value FROM $wpdb->usermeta WHERE meta_key LIKE 'uo_timer_%' $restrict_group_leader_usermeta $restrict_to_blog";
		$times_spent            = $wpdb->get_results( $sql_string );
		$times_spent_rearranged = [];
		foreach ( $times_spent as $time_spent ) {

			$user_id        = (int) $time_spent->user_id;
			$time_spent_key = $time_spent->meta_key;
			$time_key       = explode( '_', $time_spent_key );
			$time           = $time_spent->meta_value;
			$seconds        = (int) $time;

			if ( isset( $times_spent_rearranged[ $user_id ][ (int) $time_key[2] ] ) ) {
				$times_spent_rearranged[ $user_id ][ (int) $time_key[2] ] = $times_spent_rearranged[ $user_id ][ (int) $time_key[2] ] + $seconds;
			} else {
				$times_spent_rearranged[ $user_id ][ (int) $time_key[2] ] = $seconds;
			}
			$rearranged_users = apply_filters( 'rearranged_users_reporting', $rearranged_users, $time, $user_id, $time_key );
		}

		// Users' Quizzes
		$sql_string             = "SELECT user_id, meta_value FROM $wpdb->usermeta WHERE meta_key = '_sfwd-quizzes' $restrict_group_leader_usermeta $restrict_to_blog";
		$all_users_quizzes_data = $wpdb->get_results( $sql_string );
		$quizzes_rearranged     = [];

		foreach ( $all_users_quizzes_data as $user_quiz_data ) {
			$user_id           = (int) $user_quiz_data->user_id;
			$user_quizzes_data = maybe_unserialize( $user_quiz_data->meta_value );

			if ( ! is_array( $user_quizzes_data ) ) {
				continue;
			}

			foreach ( $user_quizzes_data as $quiz_data ) {

				$quiz_id   = (int) $quiz_data['quiz'];
				$course_id = (int) $quiz_data['course_id'];

				if ( ! isset( $quizzes_rearranged[ $user_id ]->quizzes[ $course_id ][ $quiz_id ]['type'] ) ) {
					$quizzes_rearranged[ $user_id ]->quizzes[ $course_id ][ $quiz_id ]['type']     = 'quiz';
					$quizzes_rearranged[ $user_id ]->quizzes[ $course_id ][ $quiz_id ]['attempts'] = 0;
				}

				$quizzes_rearranged[ $user_id ]->quizzes[ $course_id ][ $quiz_id ]['attempts'] ++;
				$quizzes_rearranged[ $user_id ]->quizzes[ $course_id ][ $quiz_id ][] = $quiz_data;
			}


		}


		return self::join_users_data_with_meta_data( $progress, $timer_rearranged, $quizzes_rearranged, $rearranged_users, $times_spent_rearranged );

	}

	private static function join_users_data_with_meta_data( $progress, $timers, $quizzes_rearranged, $rearranged_users, $times_spent ) {

		foreach ( $progress as $user_progress ) {

			$progress_user_id = (int) $user_progress->user_id;

			$user_courses_progress = maybe_unserialize( $user_progress->meta_value );


			if ( is_array( $user_courses_progress ) ) {

				foreach ( $user_courses_progress as $course_id => $user_course_progress ) {

					if ( isset( $timers[ $progress_user_id ][ $course_id ] ) ) {
						$user_course_progress['completed_time'] = $timers[ $progress_user_id ][ $course_id ];
					}

					if ( isset( $times_spent[ $progress_user_id ][ $course_id ] ) ) {
						$user_course_progress['time_spent'] = $times_spent[ $progress_user_id ][ $course_id ];
					}

					$rearranged_users[ $progress_user_id ]->$course_id = $user_course_progress;

				}
			}

		}

		foreach ( $quizzes_rearranged as $user_id => $user_quizzes_data ) {
			foreach ( $user_quizzes_data as $quiz_id => $quiz_data ) {
				$rearranged_users[ $user_id ]->$quiz_id = $quiz_data;
			}


		}

		return $rearranged_users;

	}

	public static function get_course_list() {

		global $wpdb;

		$which = 0;
		// Modify custom query to restrict data to group leaders available data
		if ( 'group_leader' === self::get_user_role() && ! self::$isolated_group_id ) {

			foreach ( self::get_administrators_group_ids() as $group_id ) {

				// restrict group leader to a single group it its set
				if ( self::$isolated_group_id && $group_id == self::$isolated_group_id ) {
					$meta_keys[] = "'learndash_group_enrolled_" . $group_id . "'";
				} else {
					$meta_keys[] = "'learndash_group_enrolled_" . $group_id . "'";
				}

			}
			$imploded_meta_keys                     = implode( ',', $meta_keys );
			$restrict_group_leader_post             = "AND ID IN (SELECT post_id FROM $wpdb->postmeta WHERE meta_key IN ($imploded_meta_keys) )";
			$restrict_group_leader_postmeta         = "AND post_id IN (SELECT post_id FROM $wpdb->postmeta WHERE meta_key IN ($imploded_meta_keys) )";
			$restrict_group_leader_associated_posts =
				"AND meta_value IN (
				    SELECT post_id
				    FROM $wpdb->postmeta
				    WHERE meta_key = '_sfwd-courses'
				    AND post_id IN (
				        SELECT post_id
				        FROM $wpdb->postmeta
				        WHERE meta_key IN ($imploded_meta_keys)
				    )
				)";
		} elseif ( self::$isolated_group_id ) {

			$restrict_group_leader_post             = "AND ID IN (SELECT post_id FROM $wpdb->postmeta WHERE meta_key = 'learndash_group_enrolled_" . self::$isolated_group_id . "' )";
			$restrict_group_leader_postmeta         = "AND post_id IN (SELECT post_id FROM $wpdb->postmeta WHERE meta_key = 'learndash_group_enrolled_" . self::$isolated_group_id . "' )";
			$restrict_group_leader_associated_posts =
				"AND meta_value IN (
				    SELECT post_id
				    FROM $wpdb->postmeta
				    WHERE meta_key = '_sfwd-courses'
				    AND post_id IN (
				        SELECT post_id
				        FROM $wpdb->postmeta
				        WHERE meta_key = 'learndash_group_enrolled_" . self::$isolated_group_id . "'
				    )
				)";
			$which                                  = 2;
		} else {
			$restrict_group_leader_post             = '';
			$restrict_group_leader_postmeta         = '';
			$restrict_group_leader_associated_posts = '';
			$which                                  = 3;
		}

		// courses
		$sql_string        = "SELECT ID, post_title FROM $wpdb->posts WHERE post_status = 'publish' AND post_type = 'sfwd-courses' $restrict_group_leader_post";
		$sql_string_course = "SELECT ID, post_title FROM $wpdb->posts WHERE post_status = 'publish' AND post_type = 'sfwd-courses' $restrict_group_leader_post";
		$course_list       = $wpdb->get_results( $sql_string );


		$rearranged_course_list = [];
		foreach ( $course_list as $course ) {
			$course_id                            = (int) $course->ID;
			$rearranged_course_list[ $course_id ] = $course;
		}

		// Course settings
		$sql_string      = "SELECT post_id, meta_value FROM $wpdb->postmeta WHERE meta_key = '_sfwd-courses' $restrict_group_leader_postmeta";
		$course_settings = $wpdb->get_results( $sql_string );

		foreach ( $course_settings as $course_setting ) {

			$course_id = (int) $course_setting->post_id;

			if ( ! array_key_exists( $course_id, $rearranged_course_list ) ) {
				continue;
			}

			$courses_settings_values = maybe_unserialize( $course_setting->meta_value );

			if ( is_array( $courses_settings_values ) ) {

				foreach ( $courses_settings_values as $key => $value ) {
					if ( 'sfwd-courses_course_price_type' === $key ) {
						$js_key_converted                                        = 'course_price_type';
						$rearranged_course_list[ $course_id ]->$js_key_converted = $value;
					}
					if ( 'sfwd-courses_course_access_list' === $key ) {
						$js_key_converted = 'course_user_access_list';
						if ( '' === $value ) {
							$rearranged_course_list[ $course_id ]->$js_key_converted = [];
						} elseif ( 'group_leader' === self::get_user_role() ) {
							$rearranged_course_list[ $course_id ]->$js_key_converted = [];
						} else {
							$rearranged_course_list[ $course_id ]->$js_key_converted = array_map( 'intval', explode( ',', $value ) );
						}

					}

				}
			}

		}

		// Course associated LearnDash Posts
		// Modify custom query to restrict data to group leaders available data
		$sql_string    = "SELECT post_id, meta_value FROM $wpdb->postmeta WHERE meta_key = 'course_id' OR meta_key LIKE 'ld_course_%' $restrict_group_leader_associated_posts";
		$courses_posts = $wpdb->get_results( $sql_string );


		foreach ( $courses_posts as $course_post ) {

			$sub_post_id       = (int) $course_post->post_id;
			$associated_course = (int) $course_post->meta_value;

			if ( ! array_key_exists( $associated_course, $rearranged_course_list ) ) {
				continue;
			}

			// make sure that there is an associate course
			if ( 0 == $associated_course ) {
				continue;
			}
			if ( ! isset( $rearranged_course_list[ $associated_course ]->associatedPosts ) ) {
				$rearranged_course_list[ $associated_course ]->associatedPosts = [];
			}

			array_push( $rearranged_course_list[ $associated_course ]->associatedPosts, $sub_post_id );

		}

		return $rearranged_course_list;

	}

	public static function get_groups_list() {

		global $wpdb;

		// Modify custom query to restrict data to group leaders available data
		if ( 'group_leader' === self::get_user_role() && ! self::$isolated_group_id ) {

			foreach ( self::get_administrators_group_ids() as $group_id ) {

				// restrict group leader to a single group it its set
				if ( self::$isolated_group_id && $group_id == self::$isolated_group_id ) {
					$post_keys[]      = "'learndash_group_users_" . $group_id . "'";
					$post_meta_keys[] = "'learndash_group_enrolled_" . $group_id . "'";
				} else {
					$post_keys[]      = "'learndash_group_users_" . $group_id . "'";
					$post_meta_keys[] = "'learndash_group_enrolled_" . $group_id . "'";
				}


			}
			$imploded_post_keys      = implode( ',', $post_keys );
			$imploded_post_meta_keys = implode( ',', $post_meta_keys );

			$restrict_group_leader_post      = "AND postmeta.meta_key IN ($imploded_post_keys)";
			$restrict_group_leader_user_meta = "WHERE meta_key IN ($imploded_post_keys)";
			$restrict_group_leader_postmeta  = "where meta_key IN ($imploded_post_meta_keys)";
		} elseif ( self::$isolated_group_id ) {
			$restrict_group_leader_post      = "AND postmeta.meta_key = 'learndash_group_users_" . self::$isolated_group_id . "'";
			$restrict_group_leader_user_meta = "WHERE meta_key = 'learndash_group_users_" . self::$isolated_group_id . "'";
			$restrict_group_leader_postmeta  = "where meta_key = 'learndash_group_enrolled_" . self::$isolated_group_id . "'";
		} else {
			$restrict_group_leader_post      = "AND postmeta.meta_key LIKE 'learndash_group_users_%'";
			$restrict_group_leader_user_meta = "WHERE meta_key LIKE 'learndash_group_users_%'";
			$restrict_group_leader_postmeta  = "WHERE meta_key LIKE 'learndash_group_enrolled_%'";
		}

		$sql_string = "SELECT post.ID, post.post_title, postmeta.meta_value FROM $wpdb->posts post JOIN $wpdb->postmeta postmeta ON post.ID = postmeta.post_id WHERE post.post_status = 'publish' AND post.post_type = 'groups' $restrict_group_leader_post";
		$group_list = $wpdb->get_results( $sql_string );

		$sql_string             = "SELECT post_id, meta_key FROM $wpdb->postmeta $restrict_group_leader_postmeta";
		$course_groups_enrolled = $wpdb->get_results( $sql_string );


		if ( is_multisite() ) {
			$blog_ID          = get_current_blog_id();

			$base_capabilities_key = $wpdb->base_prefix . 'capabilities';
			$site_capabilities_key = $wpdb->base_prefix . $blog_ID . '_capabilities';

			if ( 1 === $blog_ID ) {
				$key = $base_capabilities_key;
			} else {
				$key = $site_capabilities_key;
			}

			$restrict_to_blog = "AND  ID IN (SELECT user_id FROM $wpdb->usermeta WHERE meta_key = '{$key}')";
		} else {
			$restrict_to_blog = '';
		}
		$sql_string           = "SELECT user_id, meta_key, meta_value FROM $wpdb->usermeta $restrict_group_leader_user_meta AND meta_value != '' $restrict_to_blog";
		$user_groups_enrolled = $wpdb->get_results( $sql_string );

		$rearrange_group_list = [];

		$rearrange_course_groups_enrolled = [];

		foreach ( $course_groups_enrolled as $course_group_relationship ) {

			$group_id  = (int) str_replace( 'learndash_group_enrolled_', '', $course_group_relationship->meta_key );
			$course_id = (int) $course_group_relationship->post_id;

			if ( ! isset( $rearrange_course_groups_enrolled[ $group_id ] ) ) {
				$rearrange_course_groups_enrolled[ $group_id ] = [];
			}

			array_push( $rearrange_course_groups_enrolled[ $group_id ], $course_id );

			if ( ! isset( $rearrange_group_list[ $course_id ] ) ) {
				$rearrange_group_list[ $course_id ] = [];
			}
			array_push( $rearrange_group_list[ $course_id ], $group_id );

		}

		if ( ! empty( $rearrange_course_groups_enrolled ) ) {
			foreach ( $group_list as $group ) {


				$group_id    = (int) $group->ID;
				$group_title = $group->post_title;

				if ( ! isset( $rearrange_course_groups_enrolled[ $group_id ] ) ) {
					continue;
				}

				$groups_user = array_map( 'intval', unserialize( $group->meta_value ) );

				$rearrange_group_list[ $group_id ]['ID']                   = $group_id;
				$rearrange_group_list[ $group_id ]['post_title']           = $group_title;
				$rearrange_group_list[ $group_id ]['groups_user']          = $groups_user;
				$rearrange_group_list[ $group_id ]['groups_course_access'] = $rearrange_course_groups_enrolled[ $group_id ];

			}
		}

		$rearrange_group_list['$user_groups_enrolled'] = $user_groups_enrolled;

		if ( ! empty( $user_groups_enrolled ) ) {
			foreach ( $user_groups_enrolled as $user_group_enrolled ) {

				$group_id = (int) str_replace( 'learndash_group_users_', '', $user_group_enrolled->meta_key );

				if ( ! isset( $rearrange_course_groups_enrolled[ $group_id ] ) ) {
					continue;
				}

				if ( in_array( $user_group_enrolled->user_id, $rearrange_group_list[ $group_id ]['groups_user'] ) ) {
					continue;
				}

				array_push( $rearrange_group_list[ $group_id ]['groups_user'], $user_group_enrolled->user_id );

			}
		}


		return $rearrange_group_list;
	}

	public static function get_course_modules() {

		error_reporting( 0 );

		$course_modules = [];

		if ( current_user_can( 'tincanny_reporting' ) ) {

			$course_modules['lessonList'] = self::get_lesson_list();
			$course_modules['topicList']  = self::get_topic_list();
			$course_modules['quizList']   = self::get_quiz_list();

		}

		return $course_modules;
	}

	private static function get_lesson_list() {

		global $wpdb;

		// Modify custom query to restrict data to group leaders available data
		if ( 'group_leader' === self::get_user_role() && ! self::$isolated_group_id ) {

			foreach ( self::get_administrators_group_ids() as $group_id ) {
				if ( self::$isolated_group_id && $group_id == self::$isolated_group_id ) {
					$meta_keys[] = "'learndash_group_enrolled_" . $group_id . "'";
				} else {
					$meta_keys[] = "'learndash_group_enrolled_" . $group_id . "'";
				}
			}
			$imploded_meta_keys         = implode( ',', $meta_keys );
			$restrict_group_leader_post =
				"AND ID IN (
				SELECT post_id
				FROM $wpdb->postmeta
				WHERE meta_key = 'course_id'
				OR meta_key LIKE 'ld_course_%'
				AND meta_value IN (
				SELECT post_id
				FROM $wpdb->postmeta
				WHERE meta_key IN ($imploded_meta_keys)
				)
				)";
		} elseif ( self::$isolated_group_id ) {
			$restrict_group_leader_post =
				"AND ID IN (
				SELECT post_id
				FROM $wpdb->postmeta
				WHERE meta_key = 'course_id'
				OR meta_key LIKE 'ld_course_%'
				AND meta_value IN (
				SELECT post_id
				FROM $wpdb->postmeta
				WHERE meta_key = 'learndash_group_enrolled_" . self::$isolated_group_id . "'
				)
				)";
		} else {
			$restrict_group_leader_post = '';
		}

		$rearranged_lesson_list = array();


		$sql_string  = "SELECT ID, post_title FROM $wpdb->posts WHERE post_status = 'publish' AND post_type = 'sfwd-lessons' $restrict_group_leader_post";
		$sql_string  = apply_filters( 'get_lesson_list_sql', $sql_string, $restrict_group_leader_post );
		$lesson_list = $wpdb->get_results( $sql_string );

		foreach ( $lesson_list as $lesson ) {
			$lesson_id                            = (int) $lesson->ID;
			$rearranged_lesson_list[ $lesson_id ] = $lesson;
		}

		$rearranged_lesson_list[1] = array();

		return $rearranged_lesson_list;
	}

	private static function get_topic_list() {

		global $wpdb;

		// Modify custom query to restrict data to group leaders available data
		if ( 'group_leader' === self::get_user_role() && ! self::$isolated_group_id ) {

			foreach ( self::get_administrators_group_ids() as $group_id ) {
				if ( self::$isolated_group_id && $group_id == self::$isolated_group_id ) {
					$meta_keys[] = "'learndash_group_enrolled_" . $group_id . "'";
				} else {
					$meta_keys[] = "'learndash_group_enrolled_" . $group_id . "'";
				}
			}

			$imploded_meta_keys         = implode( ',', $meta_keys );
			$restrict_group_leader_post =
				"AND ID IN (
				SELECT post_id
				FROM $wpdb->postmeta
				WHERE meta_key = 'course_id'
				OR meta_key LIKE 'ld_course_%'
				AND meta_value IN (
				SELECT post_id
				FROM $wpdb->postmeta
				WHERE meta_key IN ($imploded_meta_keys)
				)
				)";
		} elseif ( self::$isolated_group_id ) {
			$restrict_group_leader_post =
				"AND ID IN (
				SELECT post_id
				FROM $wpdb->postmeta
				WHERE meta_key = 'course_id'
				OR meta_key LIKE 'ld_course_%'
				AND meta_value IN (
				SELECT post_id
				FROM $wpdb->postmeta
				WHERE meta_key = 'learndash_group_enrolled_".self::$isolated_group_id."'
				)
				)";
		} else {
			$restrict_group_leader_post = '';
		}

		$sql_string = "SELECT ID, post_title FROM $wpdb->posts WHERE post_status = 'publish' AND post_type = 'sfwd-topic' $restrict_group_leader_post";
		$topic_list = $wpdb->get_results( $sql_string );


		$rearranged_topic_list = [];
		foreach ( $topic_list as $topic ) {
			$topic_id                           = (int) $topic->ID;
			$rearranged_topic_list[ $topic_id ] = $topic;
		}

		$rearranged_topic_list[1] = array();

		return $rearranged_topic_list;

	}

	private static function get_quiz_list() {

		global $wpdb;

		// Modify custom query to restrict data to group leaders available data
		if ( 'group_leader' === self::get_user_role() && ! self::$isolated_group_id ) {

			foreach ( self::get_administrators_group_ids() as $group_id ) {
				if ( self::$isolated_group_id && $group_id == self::$isolated_group_id ) {
					$meta_keys[] = "'learndash_group_enrolled_" . $group_id . "'";
				} else {
					$meta_keys[] = "'learndash_group_enrolled_" . $group_id . "'";
				}

			}
			$imploded_meta_keys         = implode( ',', $meta_keys );
			$restrict_group_leader_post =
				"AND ID IN (
				SELECT post_id
				FROM $wpdb->postmeta
				WHERE meta_key = 'course_id'
				OR meta_key LIKE 'ld_course_%'
				AND meta_value IN (
				SELECT post_id
				FROM $wpdb->postmeta
				WHERE meta_key IN ($imploded_meta_keys)
				)
				)";
		} elseif ( self::$isolated_group_id ) {
			$restrict_group_leader_post =
				"AND ID IN (
				SELECT post_id
				FROM $wpdb->postmeta
				WHERE meta_key = 'course_id'
				OR meta_key LIKE 'ld_course_%'
				AND meta_value IN (
				SELECT post_id
				FROM $wpdb->postmeta
				WHERE meta_key = 'learndash_group_enrolled_" . self::$isolated_group_id . "'
				)
				)";
		} else {
			$restrict_group_leader_post = '';
		}

		$sql_string = "SELECT ID, post_title FROM $wpdb->posts WHERE post_status = 'publish' AND post_type = 'sfwd-quiz'";
		$quiz_list  = $wpdb->get_results( $sql_string );


		$rearranged_quiz_list = [];
		foreach ( $quiz_list as $quiz ) {
			$quiz_id                          = (int) $quiz->ID;
			$rearranged_quiz_list[ $quiz_id ] = $quiz;
		}

		$rearranged_quiz_list[1] = array();

		return $rearranged_quiz_list;

	}

	public static function get_assignment_data() {

		error_reporting( 0 );

		global $wpdb;


		$rearranged_assignment_list      = [];
		$merged_approval_assignment_data = [];

		if ( current_user_can( 'tincanny_reporting' ) ) {

			// Modify custom query to restrict data to group leaders available data
			if ( 'group_leader' === self::get_user_role() && ! self::$isolated_group_id ) {

				foreach ( self::get_administrators_group_ids() as $group_id ) {
					if ( self::$isolated_group_id && $group_id == self::$isolated_group_id ) {
						$meta_keys[] = "'learndash_group_enrolled_" . $group_id . "'";
					} else {
						$meta_keys[] = "'learndash_group_enrolled_" . $group_id . "'";
					}

				}
				$imploded_meta_keys = implode( ',', $meta_keys );
				// TODO CHECK ASSIGNMENT ACTIVITIES
				$restrict_group_leader_post = "AND post.ID IN (SELECT post_id FROM $wpdb->postmeta WHERE meta_key IN ($imploded_meta_keys) )";
			} elseif ( self::$isolated_group_id ) {
				$restrict_group_leader_post = "AND post.ID IN (SELECT post_id FROM $wpdb->postmeta WHERE meta_key = 'learndash_group_enrolled_" . self::$isolated_group_id . "' )";
			} else {
				$restrict_group_leader_post = '';
			}

			$sql_string             = "SELECT post.ID, post.post_author, post.post_title, post.post_date, postmeta.meta_key, postmeta.meta_value FROM $wpdb->posts post JOIN $wpdb->postmeta postmeta ON post.ID = postmeta.post_id WHERE post.post_status = 'publish' AND post.post_type = 'sfwd-assignment' AND ( postmeta.meta_key = 'approval_status' OR postmeta.meta_key = 'course_id' OR postmeta.meta_key LIKE 'ld_course_%' ) $restrict_group_leader_post";
			$assignment_data_object = $wpdb->get_results( $sql_string );


			foreach ( $assignment_data_object as $assignment ) {

				// Assignment List
				$data               = [];
				$data['ID']         = $assignment->ID;
				$data['post_title'] = $assignment->post_title;

				$assignment_id                                = (int) $assignment->ID;
				$rearranged_assignment_list[ $assignment_id ] = $data;

				// User Assignment Data
				$assignment_id      = (int) $assignment->ID;
				$assignment_user_id = (int) $assignment->post_author;
				$meta_key           = $assignment->meta_key;
				$meta_value         = (int) $assignment->meta_value;

//				if ( isset( $rearranged_approval_data[ $assignment_id ] ) ) {
//					$merged_approval_assignment_data[ $assignment_user_id ][ $assignment_id ]['approval_status'] = $rearranged_approval_data[ $assignment_id ];
//				}

				// SQL Time '1970-01-17 05:54:21' exploded to get date only
				$date                                                                                     = explode( ' ', $assignment->post_date );
				$merged_approval_assignment_data[ $assignment_user_id ][ $assignment_id ]['completed_on'] = $date[0];

				$merged_approval_assignment_data[ $assignment_user_id ][ $assignment_id ]['ID'] = $assignment_id;

				$merged_approval_assignment_data[ $assignment_user_id ][ $assignment_id ][ $meta_key ] = $meta_value;

			}

			$rearranged_assignment_list[1] = [];

			$assignment_data['userAssignmentData'] = $merged_approval_assignment_data;
			$assignment_data['assignmentList']     = $rearranged_assignment_list;

		}

		return $assignment_data;


	}

	public static function get_labels() {

		$labels['course']  = \LearnDash_Custom_Label::get_label( 'course' );
		$labels['courses'] = \LearnDash_Custom_Label::get_label( 'courses' );

		$labels['lesson']  = \LearnDash_Custom_Label::get_label( 'lesson' );
		$labels['lessons'] = \LearnDash_Custom_Label::get_label( 'lessons' );

		$labels['topic']  = \LearnDash_Custom_Label::get_label( 'topic' );
		$labels['topics'] = \LearnDash_Custom_Label::get_label( 'topics' );

		$labels['quiz']    = \LearnDash_Custom_Label::get_label( 'quiz' );
		$labels['quizzes'] = \LearnDash_Custom_Label::get_label( 'quizzes' );


		return $labels;
	}

	public static function get_links() {

		$labels = [];

		$labels['profile']    = admin_url( 'user-edit.php', 'admin' );
		$labels['assignment'] = admin_url( 'post.php', 'admin' );;

		return $labels;
	}

	public static function get_tincan_data( $data ) {

		error_reporting( 0 );

		$return_object = [];

		if ( ! current_user_can( 'tincanny_reporting' ) ) {
			$return_object['message'] = __( 'Current User doesn\'t have permissions to Tin Can report data', 'uncanny-learndash-reporting' );
			$return_object['user_ID'] = get_current_user_id();
		}

		// validate inputs
		$user_ID = absint( $data['user_ID'] );

		// if any of the values are 0 then they didn't validate, storage is not possible
		if ( 0 === $user_ID ) {
			$return_object['message'] = 'invalid user id supplied';
			$return_object['user_ID'] = $data['user_ID'];
		}

		// Example of defining user_d and course, other variables are available
//		$db = null;
//		if ( class_exists( '\UCTINCAN\Database' ) ) {
//			$db = new \UCTINCAN\Database();
//			//$db->user_id = 1;
//			//$db->course = 33;
//			$return_this = $db->GetData( 0 );
//		}

		if ( 'group_leader' === self::get_user_role() ) {

			global $wpdb;

			foreach ( self::get_administrators_group_ids() as $group_id ) {
				if ( self::$isolated_group_id && $group_id == self::$isolated_group_id ) {
					$meta_keys[] = "'learndash_group_enrolled_" . $group_id . "'";
				} else {
					$meta_keys[] = "'learndash_group_enrolled_" . $group_id . "'";
				}

			}

			$imploded_meta_keys = implode( ',', $meta_keys );

			$restrict_group_leader_post = "AND ID IN (SELECT post_id FROM $wpdb->postmeta WHERE meta_key IN ($imploded_meta_keys) )";

			// courses
			$sql_string       = "SELECT ID FROM $wpdb->posts WHERE post_status = 'publish' AND post_type = 'sfwd-courses' $restrict_group_leader_post";
			$group_course_ids = $wpdb->get_col( $sql_string );
		}

		$tin_can_data = null;
		if ( class_exists( '\UCTINCAN\Database\Admin' ) ) {
			$database          = new \UCTINCAN\Database\Admin();
			$database->user_id = $user_ID;
			$tin_can_data      = $database->get_data();
		}

		if ( null !== $tin_can_data && ! empty( $tin_can_data ) ) {

			$data          = [];
			$sample        = [];
			$sample['All'] = $tin_can_data;
			foreach ( $tin_can_data as $user_single_tin_can_object ) {

				$course_ID = (int) $user_single_tin_can_object['course_id'];
				$lesson_ID = (int) $user_single_tin_can_object['lesson_id'];

				if ( 'group_leader' === self::get_user_role() ) {
					if ( ! in_array( $course_ID, $group_course_ids ) ) {
						continue;
					}
				}

				if ( $user_single_tin_can_object['lesson_id'] && $user_single_tin_can_object['course_id'] ) {

					if ( ! isset( $data[ $course_ID ] ) ) {
						$data[ $course_ID ] = [];
					}
					if ( ! isset( $data[ $course_ID ][ $lesson_ID ] ) ) {
						$data[ $course_ID ][ $lesson_ID ] = [];
					}
					$course_ID = (int) $user_single_tin_can_object['course_id'];
					$lesson_ID = (int) $user_single_tin_can_object['lesson_id'];
					array_push( $data[ $course_ID ][ $lesson_ID ], $user_single_tin_can_object );

				} else {
					continue;
				}

			}

			return [ 'user_ID' => $user_ID, 'tinCanStatements' => $data ];

		} else {
			return [];
		}

	}

	public static function show_tincan_tables( $data ) {

		error_reporting( 0 );

		$show_tincan_tables = absint( $data['show_tincan'] );

		if ( 1 == $show_tincan_tables ) {
			$value = 'yes';
		}
		if ( 0 == $show_tincan_tables ) {
			$value = 'no';
		}

		if ( current_user_can( 'manage_options' ) ) {
			$updated = update_option( 'show_tincan_reporting_tables', $value );

			return $value;
		} else {
			return 'no permissions';
		}


	}

	public static function disable_mark_complete( $data ) {

		error_reporting( 0 );

		$disable_mark_complete = absint( $data['disable_mark_complete'] );

		if ( 1 == $disable_mark_complete ) {
			$value = 'yes';
		}
		if ( 0 == $disable_mark_complete ) {
			$value = 'no';
		}

		if ( current_user_can( 'manage_options' ) ) {
			$updated = update_option( 'disable_mark_complete_for_tincan', $value );

			return $value;
		} else {
			return 'no permissions';
		}
	}

	public static function nonce_protection( $data ) {
		error_reporting( 0 );

		$nonce_protection = absint( $data['nonce_protection'] );

		if ( 1 == $nonce_protection ) {
			$value = 'yes';
		}
		if ( 0 == $nonce_protection ) {
			$value = 'no';
		}

		if ( current_user_can( 'manage_options' ) ) {
			$updated = update_option( 'tincanny_nonce_protection', $value );

			return $value;
		} else {
			return 'no permissions';
		}
	}

	public static function reset_tincan_data() {

		error_reporting( 0 );

		if ( current_user_can( 'manage_options' ) ) {

			if ( class_exists( '\UCTINCAN\Database\Admin' ) ) {
				$database = new \UCTINCAN\Database\Admin();
				$database->reset();

				return true;
			}

		}

		return false;
	}

	public static function reset_bookmark_data() {

		error_reporting( 0 );

		if ( current_user_can( 'manage_options' ) ) {

			if ( class_exists( '\UCTINCAN\Database\Admin' ) ) {
				$database = new \UCTINCAN\Database\Admin();
				$database->reset_bookmark_data();

				return true;
			}

		}

		return false;
	}

	public static function get_dashboard_data() {

		error_reporting( 0 );

		if ( current_user_can( 'tincanny_reporting' ) ) {

			// is it an administrator
			if ( 'group_leader' === self::get_user_role() ) {

				// Verify the group leader has groups assigned
				if ( ! count( self::get_administrators_group_ids() ) ) {

					$json_return['message'] = __( 'Group Leader has no groups assigned', 'uncanny-learndash-reporting' );
					$json_return['success'] = false;

					return $json_return;
				}

			}
		}

		global $wpdb;

		// Modify custom query to restrict data to group leaders available data
		if ( 'group_leader' === self::get_user_role() && ! self::$isolated_group_id ) {

			foreach ( self::get_administrators_group_ids() as $group_id ) {

				if ( self::$isolated_group_id && $group_id == self::$isolated_group_id ) {
					$user_meta_keys[] = "'learndash_group_users_" . $group_id . "'";
					$post_meta_keys[] = "'learndash_group_enrolled_" . $group_id . "'";
				} else {
					$user_meta_keys[] = "'learndash_group_users_" . $group_id . "'";
					$post_meta_keys[] = "'learndash_group_enrolled_" . $group_id . "'";
				}


			}

			$imploded_user_meta_keys = implode( ',', $user_meta_keys );
			$imploded_post_meta_keys = implode( ',', $post_meta_keys );

			$restrict_group_leader_users             = "WHERE ID IN (SELECT user_id FROM $wpdb->usermeta WHERE meta_key IN ($imploded_user_meta_keys) )";
			$restrict_group_leader_courses           = "AND ID IN (SELECT post_id FROM $wpdb->postmeta WHERE meta_key IN ($imploded_post_meta_keys) )";
			$restrict_group_leader_courses_completed = "AND user_id IN (SELECT user_id FROM $wpdb->usermeta WHERE meta_key IN ($imploded_user_meta_keys) )";
		} elseif ( self::$isolated_group_id ) {
			$restrict_group_leader_users             = "WHERE ID IN (SELECT user_id FROM $wpdb->usermeta WHERE meta_key = 'learndash_group_users_" . self::$isolated_group_id . "' )";
			$restrict_group_leader_courses           = "AND ID IN (SELECT post_id FROM $wpdb->postmeta WHERE meta_key = 'learndash_group_enrolled_" . self::$isolated_group_id . "' )";
			$restrict_group_leader_courses_completed = "AND user_id IN (SELECT user_id FROM $wpdb->usermeta WHERE meta_key = 'learndash_group_users_" . self::$isolated_group_id . "' )";
		} else {
			$restrict_group_leader_users             = '';
			$restrict_group_leader_courses           = '';
			$restrict_group_leader_courses_completed = '';
		}

		if ( is_multisite() ) {
			$blog_ID = get_current_blog_id();

			$base_capabilities_key = $wpdb->base_prefix . 'capabilities';
			$site_capabilities_key = $wpdb->base_prefix . $blog_ID . '_capabilities';

			if ( 1 === $blog_ID ) {
				$key = $base_capabilities_key;
			} else {
				$key = $site_capabilities_key;
			}

			if ( '' === $restrict_group_leader_users ) {
				$restrict_to_blog = "WHERE ID IN (SELECT user_id FROM $wpdb->usermeta WHERE meta_key = '{$key}')";
			} else {
				$restrict_to_blog = "AND  ID IN (SELECT user_id FROM $wpdb->usermeta WHERE meta_key = '{$key}')";
			}

		} else {
			$restrict_to_blog = '';
		}

		//
		$data_object                = [];
		$sql_string                 = "SELECT count(ID) FROM $wpdb->users $restrict_group_leader_users $restrict_to_blog";
		$data_object['total_users'] = $wpdb->get_var( $sql_string );

		$sql_string                   = "SELECT count(ID) FROM $wpdb->posts WHERE post_status = 'publish' AND post_type = 'sfwd-courses' $restrict_group_leader_courses";
		$data_object['total_courses'] = $wpdb->get_var( $sql_string );

		$sql_string        = "SELECT user_id, meta_key, meta_value FROM $wpdb->usermeta WHERE meta_key LIKE 'course_completed_%' $restrict_group_leader_courses_completed ";
		$courses_completed = $wpdb->get_results( $sql_string );

		$temp_array       = [];
		$user_completions = [];
		foreach ( $courses_completed as $completion ) {

			$course_id = explode( '_', $completion->meta_key )[2];
			$user_id   = $completion->user_id;

			if ( $user_id != 1 ) {
				if ( ! isset( $user_completions[ $course_id ] ) ) {
					$user_completions[ $course_id ] = 1;
				} else {
					$user_completions[ $course_id ] ++;
				}
			}

			$date = date( 'Y-m-d', (int) $completion->meta_value );

			if ( strtotime( $date ) <= strtotime( '-30 days' ) ) {
				continue;
			}
			if ( ! isset( $temp_array[ $date ] ) ) {
				$temp_array[ $date ] = 1;
			} else {
				$temp_array[ $date ] ++;
			}
		}

		$completions = [];
		// min max date
		foreach ( $temp_array as $date => $amount_completions ) {
			$object              = new \stdClass();
			$object->date        = $date;
			$object->completions = $amount_completions;
			if ( $amount_completions > 0 ) {
				array_push( $completions, $object );
			}

		}

		$table = $wpdb->prefix . 'uotincan_reporting';
		//sql min, max, date
		$sql_string        = "SELECT `stored` FROM $table WHERE `stored` >= NOW( ) - INTERVAL 1 MONTH";
		$tin_can_completed = $wpdb->get_results( $sql_string );

		$temp_array = [];
		foreach ( $tin_can_completed as $completion ) {
			$date = date( 'Y-m-d', strtotime( $completion->stored ) );
			if ( ! isset( $temp_array[ $date ] ) ) {
				$temp_array[ $date ] = 1;
			} else {
				$temp_array[ $date ] ++;
			}
		}

		$tin_can_stored = [];
		foreach ( $temp_array as $date => $amount_completions ) {
			$object         = new \stdClass();
			$object->date   = $date;
			$object->tinCan = $amount_completions;
			if ( $amount_completions > 0 ) {
				array_push( $tin_can_stored, $object );
			}

		}

		$data_object['courses_tincan_completed'] = array_merge( $tin_can_stored, $completions );

		usort( $data_object['courses_tincan_completed'], function ( $a, $b ) {
			return strtotime( $a->date ) - strtotime( $b->date );
		} );

		// TOPS
		$group_list  = self::get_groups_list();
		$course_list = self::get_course_list();
		$access      = [];
		foreach ( $course_list as $course_id => $course ) {

			$access[ $course_id ]['post_title']              = $course->post_title;
			$access[ $course_id ]['course_price_type']       = $course->course_price_type;
			$access[ $course_id ]['course_user_access_list'] = $course->course_user_access_list;

			if ( isset( $user_completions[ $course_id ] ) ) {
				$access[ $course_id ]['completions'] = $user_completions[ $course_id ];
			} else {
				$access[ $course_id ]['completions'] = 0;
			}

			foreach ( $group_list as $group ) {
				if ( isset( $group['groups_course_access'] ) ) {
					foreach ( $group['groups_course_access'] as $group_access_course_id ) {
						if ( $group_access_course_id == $course_id ) {
							$access[ $course_id ]['course_user_access_list'] = array_unique( array_merge( $access[ $course_id ]['course_user_access_list'], $group['groups_user'] ) );
						}
					}
				}
			}
		}

		$data_object['top_course_completions'] = $access;
		$data_object['learnDashLabels']        = self::get_labels();

		usort( $data_object['top_course_completions'], function ( $a, $b ) {
			return $b['completions'] - $a['completions'];
		} );

		$data_object['report_link'] = admin_url( 'admin.php?page=uncanny-learnDash-reporting' );

		$data_object['localizedStrings'] = array(
			'Loading Dashboard Report' => 'xLoading Dashboard Report',
			'Total Users'              => 'xTotal Users',
		);

		return $data_object;
	}

	private static function get_user_role() {

		if ( ! self::$user_role ) {

			// Default value
			self::$user_role = 'unknown';

			$current_user_ID = get_current_user_id();

			// is it an administrator
			if ( current_user_can( 'manage_options' ) ) {

				//Set user's role
				self::$user_role = 'administrator';
			} // Is it a group leader
			elseif ( is_group_leader( $current_user_ID ) ) {

				//Set user's role
				self::$user_role = 'group_leader';
			}

		}

		return self::$user_role;

	}

	private static function get_administrators_group_ids() {

		if ( ! self::$group_leaders_group_ids ) {

			$current_user_ID = get_current_user_id();

			if ( 'group_leader' === self::get_user_role() ) {
				self::$group_leaders_group_ids = learndash_get_administrators_group_ids( $current_user_ID );
			}

		}

		return self::$group_leaders_group_ids;

	}

}