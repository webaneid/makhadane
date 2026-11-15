<?php
/**
 * Admin Header/Topbar Styling.
 *
 * Custom styling untuk WordPress admin bar (top black bar).
 *
 * @package makhadane
 */

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals

/**
 * Admin bar styles now loaded from scss/_admin-header.scss
 * Compiled into css/admin.css
 *
 * All inline styles (395 lines with 142 !important declarations)
 * have been moved to external SCSS for better caching and performance.
 */

/**
 * Enqueue mobile header JavaScript via inline script.
 *
 * @param string $hook Current admin page hook.
 * @return void
 */
function ane_admin_header_scripts( $hook ) {
	unset( $hook );

	if ( ! is_admin() ) {
		return;
	}

	wp_register_script( 'ane-admin-header', false, array(), MAKHADANE_VERSION, true );
	wp_enqueue_script( 'ane-admin-header' );
	wp_add_inline_script(
		'ane-admin-header',
		"(function(){'use strict';if(window.innerWidth>782){return;}function initMobileMenu(){var hamburger=document.querySelector('#wp-admin-bar-site-name > .ab-item');var userAvatar=document.querySelector('#wp-admin-bar-my-account > .ab-item');if(userAvatar){userAvatar.removeAttribute('href');userAvatar.setAttribute('aria-disabled','true');userAvatar.style.cursor='pointer';}var body=document.body;if(!hamburger){return;}var menuPanel=document.createElement('div');menuPanel.className='ane-mobile-menu';var adminMenu=document.querySelector('#adminmenu');if(adminMenu){var menuClone=adminMenu.cloneNode(true);menuClone.id='ane-mobile-adminmenu';menuPanel.appendChild(menuClone);}body.appendChild(menuPanel);var userPanel=document.createElement('div');userPanel.className='ane-mobile-user';var userSubmenu=document.querySelector('#wp-admin-bar-my-account .ab-submenu');if(userSubmenu){var submenuClone=userSubmenu.cloneNode(true);var userInfoItem=submenuClone.querySelector('#wp-admin-bar-user-info');if(userInfoItem){var userInfoLink=userInfoItem.querySelector('a');if(userInfoLink){var userInfoDiv=document.createElement('div');userInfoDiv.className=userInfoLink.className;userInfoDiv.innerHTML=userInfoLink.innerHTML;userInfoLink.parentNode.replaceChild(userInfoDiv,userInfoLink);}}userPanel.appendChild(submenuClone);}body.appendChild(userPanel);var overlay=document.createElement('div');overlay.className='ane-mobile-overlay';body.appendChild(overlay);function openMenu(){body.classList.add('ane-menu-open');menuPanel.classList.add('is-open');overlay.classList.add('is-visible');}function closeMenu(){body.classList.remove('ane-menu-open');menuPanel.classList.remove('is-open');if(!body.classList.contains('ane-user-open')){overlay.classList.remove('is-visible');}}function openUserPanel(){body.classList.add('ane-user-open');userPanel.classList.add('is-open');overlay.classList.add('is-visible');}function closeUserPanel(){body.classList.remove('ane-user-open');userPanel.classList.remove('is-open');if(!body.classList.contains('ane-menu-open')){overlay.classList.remove('is-visible');}}hamburger.addEventListener('click',function(e){e.preventDefault();e.stopPropagation();closeUserPanel();if(body.classList.contains('ane-menu-open')){closeMenu();}else{openMenu();}});if(userAvatar){userAvatar.addEventListener('click',function(e){e.preventDefault();e.stopPropagation();e.stopImmediatePropagation();closeMenu();if(body.classList.contains('ane-user-open')){closeUserPanel();}else{openUserPanel();}},{capture:true});}overlay.addEventListener('click',function(){closeMenu();closeUserPanel();});var menuItems=menuPanel.querySelectorAll('li.wp-has-submenu');menuItems.forEach(function(item){var link=item.querySelector('a.menu-top');var submenu=item.querySelector('.wp-submenu');if(!link||!submenu){return;}var icon=document.createElement('span');icon.className='ane-accordion-icon';icon.textContent='+';link.appendChild(icon);link.addEventListener('click',function(e){e.preventDefault();var isOpen=item.classList.contains('ane-submenu-open');menuItems.forEach(function(otherItem){if(otherItem!==item){otherItem.classList.remove('ane-submenu-open');}});item.classList.toggle('ane-submenu-open',!isOpen);});});}if(document.readyState==='loading'){document.addEventListener('DOMContentLoaded',initMobileMenu);}else{initMobileMenu();}window.addEventListener('resize',function(){if(window.innerWidth<=782&&!document.querySelector('.ane-mobile-menu')){initMobileMenu();}});})();"
	);
}
add_action( 'admin_enqueue_scripts', 'ane_admin_header_scripts' );

/**
 * Customize admin bar menu items.
 *
 * Add custom New Post & New Page buttons, remove unnecessary items.
 *
 * @param WP_Admin_Bar $wp_admin_bar Admin bar instance.
 */
function ane_customize_admin_bar( $wp_admin_bar ) {
	// Remove WordPress logo.
	$wp_admin_bar->remove_node( 'wp-logo' );

	// Remove comments.
	$wp_admin_bar->remove_node( 'comments' );

	// Remove new content menu.
	$wp_admin_bar->remove_node( 'new-content' );

	// Remove updates.
	$wp_admin_bar->remove_node( 'updates' );

	// Add New Post button (solid) - direct to top-secondary.
	$wp_admin_bar->add_node(
		array(
			'id'     => 'ane-new-post',
			'title'  => '<span class="ane-btn-icon ane-btn-icon-post"></span>' . __( 'New Post', 'makhadane' ),
			'href'   => admin_url( 'post-new.php' ),
			'parent' => 'top-secondary',
			'meta'   => array(
				'class' => 'ane-action-btn',
			),
		)
	);

	// Add New Page button (outline) - direct to top-secondary.
	$wp_admin_bar->add_node(
		array(
			'id'     => 'ane-new-page',
			'title'  => '<span class="ane-btn-icon ane-btn-icon-page"></span>' . __( 'New Page', 'makhadane' ),
			'href'   => admin_url( 'post-new.php?post_type=page' ),
			'parent' => 'top-secondary',
			'meta'   => array(
				'class' => 'ane-action-btn',
			),
		)
	);

	// Add Dashboard link to site-name submenu.
	$wp_admin_bar->add_node(
		array(
			'id'     => 'ane-dashboard',
			'title'  => __( 'Dashboard', 'makhadane' ),
			'href'   => admin_url( 'admin.php?page=ane-setup' ),
			'parent' => 'site-name',
			'meta'   => array(
				'class' => 'ane-dashboard-link',
			),
		)
	);
}
add_action( 'admin_bar_menu', 'ane_customize_admin_bar', 999 );

/**
 * Hide admin bar for non-admin users on frontend.
 *
 * Clean frontend experience untuk subscribers/customers.
 */
function ane_hide_admin_bar_frontend() {
	if ( ! current_user_can( 'manage_options' ) && ! is_admin() ) {
		show_admin_bar( false );
	}
}
add_action( 'after_setup_theme', 'ane_hide_admin_bar_frontend' );

/**
 * Add custom CSS class to admin bar.
 *
 * @param string $classes Current class.
 * @return string Modified class.
 */
function ane_admin_bar_class( $classes ) {
	$classes .= ' ane-admin-bar';

	// Add role-based class.
	$user = wp_get_current_user();
	if ( ! empty( $user->roles ) ) {
		$classes .= ' user-role-' . $user->roles[0];
	}

	return $classes;
}
add_filter( 'admin_bar_class', 'ane_admin_bar_class' );

/**
 * Customize "Howdy" text in admin bar.
 *
 * @param WP_Admin_Bar $wp_admin_bar Admin bar instance.
 */
function ane_replace_howdy( $wp_admin_bar ) {
	$account = $wp_admin_bar->get_node( 'my-account' );

	if ( ! $account ) {
		return;
	}

	// Replace "Howdy" with "Selamat datang".
	$account->title = str_replace( 'Howdy,', __( 'Selamat datang,', 'makhadane' ), $account->title );

	$wp_admin_bar->add_node( $account );
}
add_action( 'admin_bar_menu', 'ane_replace_howdy', 25 );
