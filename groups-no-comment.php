<?php
/**
* Plugin Name: Groups No Comment
* Plugin URI:
* Description: A little extension that prohibits members of the group "No Comment" to read or post comments. Requires the <a href=" https://www.itthinx.com/shop/groups-restrict-comments-pro/">Groups Restrict Comments Pro</a> extension.
* Version: 1.0.0
* Author: itthinx
* Author URI: http://www.itthinx.com
* Donate-Link: http://www.itthinx.com
* License: GPLv3
*/

if ( !defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Plugin main class, implements filters to override whether users can read or post comments based on their membership with the "No Comments" group.
 * Users who belong to that group will not be able to post or read comments.
 */
class Groups_No_Comment {

	/**
	 * Adds two filters that determine whether a user can read or post comments.
	 */
	public static function init() {
		if ( class_exists( 'Groups_Group' ) && class_exists( 'Groups_Restrict_Comments_Pro' ) ) {
			add_filter( 'groups_restrict_comments_pro_show_form', array( __CLASS__, 'groups_restrict_comments_pro_show_form' ), 10, 2 );
			add_filter( 'groups_restrict_comments_pro_user_can_read_comments', array( __CLASS__, 'groups_restrict_comments_pro_user_can_read_comments' ), 10, 3 );
		}
	}

	/**
	 * Override whether the user can post a comment, i.e. show the comment form (or not).
	 *
	 * @param boolean $show_form whether to show the form
	 * @param int $id ID of the post for which the form would be shown
	 *
	 * @return boolean
	 */
	public static function groups_restrict_comments_pro_show_form( $show_form, $id ) {
		if ( $user_id = get_current_user_id() ) {
			if ( $group_id = Groups_Group::read_by_name( 'No Comment' ) ) {
				$user = new Groups_User( $user_id );
				$user_groups = $user->group_ids_deep;
				if ( in_array( $group_id, $user_groups ) ) {
					$show_form = false;
				}
			}
		}
		return $show_form;
	}

	/**
	 * Override whether the user can read comments.
	 *
	 * @param boolean $user_can_read whether the user can read comments
	 * @param int $user_id the user's ID 
	 * @param int $post_id the post ID
	 *
	 * @return boolean
	 */
	public static function groups_restrict_comments_pro_user_can_read_comments( $user_can_read, $user_id, $post_id ) {
		if ( $user_id = get_current_user_id() ) {
			if ( $group_id = Groups_Group::read_by_name( 'No Comment' ) ) {
				$user = new Groups_User( $user_id );
				$user_groups = $user->group_ids_deep;
				if ( in_array( $group_id, $user_groups ) ) {
					$user_can_read = false;
				}
			}
		}
		return $user_can_read;
	}
}
add_action( 'init', array( 'Groups_No_Comment', 'init' ) );
