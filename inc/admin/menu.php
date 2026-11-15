<?php
/**
 * Admin Menu Styling & Customization.
 *
 * Modern sidebar menu dengan dynamic brand colors dan smooth animations.
 *
 * @package makhadane
 */

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals

/**
 * Admin menu styles now loaded from scss/_admin-menu.scss
 * Compiled into css/admin.css
 *
 * All inline styles (467 lines with 109 !important declarations)
 * have been moved to external SCSS for better caching and performance.
 */

/**
 * Enqueue admin menu JavaScript via inline script.
 *
 * @return void
 */
function ane_admin_menu_scripts() {
	wp_register_script( 'ane-admin-menu', false, array(), MAKHADANE_VERSION, true );
	wp_enqueue_script( 'ane-admin-menu' );
	wp_add_inline_script(
		'ane-admin-menu',
		"(function(){'use strict';function init(){var menuItems=document.querySelectorAll('#adminmenu li.menu-top');menuItems.forEach(function(item){var link=item.querySelector('a');var menuName=item.querySelector('.wp-menu-name');if(link&&menuName){var tooltip=document.createElement('span');tooltip.className='ane-tooltip';tooltip.textContent=menuName.textContent.trim();var arrow=document.createElement('span');arrow.className='ane-tooltip-arrow';document.body.appendChild(tooltip);document.body.appendChild(arrow);link.addEventListener('mouseenter',function(){if(!item.classList.contains('opensub')&&!item.classList.contains('current')){var rect=link.getBoundingClientRect();var topPos=rect.top+rect.height/2+'px';tooltip.style.position='fixed';tooltip.style.left='70px';tooltip.style.top=topPos;tooltip.style.transform='translateY(-50%)';arrow.style.position='fixed';arrow.style.left='62px';arrow.style.top=topPos;arrow.style.transform='translateY(-50%)';tooltip.classList.add('ane-tooltip-show');arrow.classList.add('ane-tooltip-show');}});link.addEventListener('mouseleave',function(){tooltip.classList.remove('ane-tooltip-show');arrow.classList.remove('ane-tooltip-show');});}});var subItems=document.querySelectorAll('#adminmenu li.menu-top.wp-has-submenu');subItems.forEach(function(item){var link=item.querySelector('a');if(!link){return;}link.addEventListener('click',function(e){e.preventDefault();var isOpen=item.classList.contains('opensub');if(isOpen){item.classList.remove('opensub');item.setAttribute('data-ane-locked','false');document.body.classList.remove('has-open-submenu');}else{subItems.forEach(function(otherItem){otherItem.classList.remove('opensub');otherItem.setAttribute('data-ane-locked','false');});item.classList.add('opensub');item.setAttribute('data-ane-locked','true');document.body.classList.add('has-open-submenu');}});var observer=new MutationObserver(function(mutations){mutations.forEach(function(mutation){if(mutation.type==='attributes'&&mutation.attributeName==='class'&&item.getAttribute('data-ane-locked')==='true'&&!item.classList.contains('opensub')){item.classList.add('opensub');}});});observer.observe(item,{attributes:true,attributeFilter:['class']});});}if(document.readyState==='loading'){document.addEventListener('DOMContentLoaded',init);}else{init();}})();"
	);
}
add_action( 'admin_enqueue_scripts', 'ane_admin_menu_scripts' );

/**
 * Add custom CSS classes to admin body.
 *
 * Allows targeting specific conditions dengan CSS.
 *
 * @param string $classes Current body classes.
 * @return string Modified body classes.
 */
function ane_admin_body_class( $classes ) {
	// Add custom admin class.
	$classes .= ' sarika-admin';

	// Add role-based class.
	$user = wp_get_current_user();
	if ( ! empty( $user->roles ) ) {
		$classes .= ' user-role-' . $user->roles[0];
	}

	// Add mobile class.
	if ( wp_is_mobile() ) {
		$classes .= ' sarika-mobile-admin';
	}

	return $classes;
}
add_filter( 'admin_body_class', 'ane_admin_body_class' );
