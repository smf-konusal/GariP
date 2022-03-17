<?php
/**
 * Simple Machines Forum (SMF)
 *
 * @package SMF
 * @author Simple Machines https://www.simplemachines.org
 * @copyright 2022 Simple Machines and individual contributors
 * @license https://www.simplemachines.org/about/smf/license.php BSD
 *
 * @version 2.1.0
 */
 /**
 * @package GariP
 * @version 1.0
 * @theme GariP
 * @author Snrj and cee山 - http://smf.konusal.com
 * Copyright 2022 GariP
 *
 */
/*	This template is, perhaps, the most important template in the theme. It
	contains the main template layer that displays the header and footer of
	the forum, namely with main_above and main_below. It also contains the
	menu sub template, which appropriately displays the menu; the init sub
	template, which is there to set the theme up; (init can be missing.) and
	the linktree sub template, which sorts out the link tree.

	The init sub template should load any data and set any hardcoded options.

	The main_above sub template is what is shown above the main content, and
	should contain anything that should be shown up there.

	The main_below sub template, conversely, is shown after the main content.
	It should probably contain the copyright statement and some other things.

	The linktree sub template should display the link tree, using the data
	in the $context['linktree'] variable.

	The menu sub template should display all the relevant buttons the user
	wants and or needs.

	For more information on the templating system, please see the site at:
	https://www.simplemachines.org/
*/

/**
 * Initialize the template... mainly little settings.
 */
function template_init()
{
	global $settings, $txt;

	/* $context, $options and $txt may be available for use, but may not be fully populated yet. */

	// The version this template/theme is for. This should probably be the version of SMF it was created for.
	$settings['theme_version'] = '2.1';

	// Set the following variable to true if this theme requires the optional theme strings file to be loaded.
	$settings['require_theme_strings'] = true;

	// Set the following variable to true if this theme wants to display the avatar of the user that posted the last and the first post on the message index and recent pages.
	$settings['avatars_on_indexes'] = false;

	// Set the following variable to true if this theme wants to display the avatar of the user that posted the last post on the board index.
	$settings['avatars_on_boardIndex'] = false;

	// Set the following variable to true if this theme wants to display the login and register buttons in the main forum menu.
	$settings['login_main_menu'] = false;

	// This defines the formatting for the page indexes used throughout the forum.
	$settings['page_index'] = array(
		'extra_before' => '<span class="pages">' . $txt['pages'] . '</span>',
		'previous_page' => '<span class="main_icons previous_page"></span>',
		'current_page' => '<span class="current_page">%1$d</span> ',
		'page' => '<a class="nav_page" href="{URL}">%2$s</a> ',
		'expand_pages' => '<span class="expand_pages" onclick="expandPages(this, {LINK}, {FIRST_PAGE}, {LAST_PAGE}, {PER_PAGE});"> ... </span>',
		'next_page' => '<span class="main_icons next_page"></span>',
		'extra_after' => '',
	);
	// Allow css/js files to be disabled for this specific theme.
	// Add the identifier as an array key. IE array('smf_script'); Some external files might not add identifiers, on those cases SMF uses its filename as reference.
	if (!isset($settings['disable_files']))
		$settings['disable_files'] = array();

		$settings['theme_variants'] = array('1','2','3','4','5', '6');
}

/**
 * The main sub template above the content.
 */
function template_html_above()
{
	global $context, $scripturl, $txt, $modSettings;

	// Show right to left, the language code, and the character set for ease of translating.
	echo '<!DOCTYPE html>
<html', $context['right_to_left'] ? ' dir="rtl"' : '', !empty($txt['lang_locale']) ? ' lang="' . str_replace("_", "-", substr($txt['lang_locale'], 0, strcspn($txt['lang_locale'], "."))) . '"' : '', '>
<head>
	<meta charset="', $context['character_set'], '">';
	loadCSSFile('bootstrap.min.css', array('minimize' => true));
	loadJavascriptFile('bootstrap.min.js', array('minimize' => true));
	/*
		You don't need to manually load index.css, this will be set up for you.
		Note that RTL will also be loaded for you.
		To load other CSS and JS files you should use the functions
		loadCSSFile() and loadJavaScriptFile() respectively.
		This approach will let you take advantage of SMF's automatic CSS
		minimization and other benefits. You can, of course, manually add any
		other files you want after template_css() has been run.

	*	Short example:
			- CSS: loadCSSFile('filename.css', array('minimize' => true));
			- JS:  loadJavaScriptFile('filename.js', array('minimize' => true));
			You can also read more detailed usages of the parameters for these
			functions on the SMF wiki.

	*	Themes:
			The most efficient way of writing multi themes is to use a master
			index.css plus variant.css files. If you've set them up properly
			(through $settings['theme_variants']), the variant files will be loaded
			for you automatically.
			Additionally, tweaking the CSS for the editor requires you to include
			a custom 'jquery.sceditor.theme.css' file in the css folder if you need it.

	*	MODs:
			If you want to load CSS or JS files in here, the best way is to use the
			'integrate_load_theme' hook for adding multiple files, or using
			'integrate_pre_css_output', 'integrate_pre_javascript_output' for a single file.
	*/

	// load in any css from mods or themes so they can overwrite if wanted
	template_css();

	// load in any javascript files from mods and themes
	template_javascript();

	echo '
	<title>', $context['page_title_html_safe'], '</title>
	<meta name="viewport" content="width=device-width, initial-scale=1">';

	// Content related meta tags, like description, keywords, Open Graph stuff, etc...
	foreach ($context['meta_tags'] as $meta_tag)
	{
		echo '
	<meta';

		foreach ($meta_tag as $meta_key => $meta_value)
			echo ' ', $meta_key, '="', $meta_value, '"';

		echo '>';
	}

	/*	What is your Lollipop's color?
		Theme Authors, you can change the color here to make sure your theme's main color gets visible on tab */
	echo '
	<meta name="theme-color" content="#557EA0">';

	// Please don't index these Mr Robot.
	if (!empty($context['robot_no_index']))
		echo '
	<meta name="robots" content="noindex">';

	// Present a canonical url for search engines to prevent duplicate content in their indices.
	if (!empty($context['canonical_url']))
		echo '
	<link rel="canonical" href="', $context['canonical_url'], '">';

	// Show all the relative links, such as help, search, contents, and the like.
	echo '
	<link rel="help" href="', $scripturl, '?action=help">
	<link rel="contents" href="', $scripturl, '">', ($context['allow_search'] ? '
	<link rel="search" href="' . $scripturl . '?action=search">' : '');

	// If RSS feeds are enabled, advertise the presence of one.
	if (!empty($modSettings['xmlnews_enable']) && (!empty($modSettings['allow_guestAccess']) || $context['user']['is_logged']))
		echo '
	<link rel="alternate" type="application/rss+xml" title="', $context['forum_name_html_safe'], ' - ', $txt['rss'], '" href="', $scripturl, '?action=.xml;type=rss2', !empty($context['current_board']) ? ';board=' . $context['current_board'] : '', '">
	<link rel="alternate" type="application/atom+xml" title="', $context['forum_name_html_safe'], ' - ', $txt['atom'], '" href="', $scripturl, '?action=.xml;type=atom', !empty($context['current_board']) ? ';board=' . $context['current_board'] : '', '">';

	// If we're viewing a topic, these should be the previous and next topics, respectively.
	if (!empty($context['links']['next']))
		echo '
	<link rel="next" href="', $context['links']['next'], '">';

	if (!empty($context['links']['prev']))
		echo '
	<link rel="prev" href="', $context['links']['prev'], '">';

	// If we're in a board, or a topic for that matter, the index will be the board's index.
	if (!empty($context['current_board']))
		echo '
	<link rel="index" href="', $scripturl, '?board=', $context['current_board'], '.0">';

	// Output any remaining HTML headers. (from mods, maybe?)
	echo $context['html_headers'];

	echo '
</head>
<body id="', $context['browser_body_id'], '" class="action_', !empty($context['current_action']) ? $context['current_action'] : (!empty($context['current_board']) ?
		'messageindex' : (!empty($context['current_topic']) ? 'display' : 'home')), !empty($context['current_board']) ? ' board_' . $context['current_board'] : '', '">
<div id="footerfix">';
}

/**
 * The upper part of the main template layer. This is the stuff that shows above the main forum content.
 */
function template_body_above()
{
	global $context, $settings, $scripturl, $txt, $modSettings, $maintenance;
	
	// Wrapper div now echoes permanently for better layout options. h1 a is now target for "Go up" links.
			if(!empty($settings['snrjmenu2'])){
			echo '<div id="content-wrapper" class="d-flex flex-column">
						<div id="main_menu" class="dikeymenu navbar-light bg-white sticky-top">
							<div id="mobile_user_menu" class="popup_container">
								<div class="popup_window description">
									<div class="popup_heading">', $txt['mobile_user_menu'], '
										<a href="javascript:void(0);" class="main_icons hide_popup"></a>
									</div>
									', template_menu2(), '
								</div>
							</div>
						</div>';
			}
			else{
			template_menu();
			echo '<div id="content-wrapper" class="d-flex flex-column">';
			}
			echo'
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
                   ',!empty($settings['snrjmenu2']) ? '<a class="mobile_user_menu smenu"><span class="main_icons bars"></span></a>' : ' <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3"><i class="main_icons bars"></i></button> ','';
				if ($context['allow_search'])
				{
					echo '
						<form id="search_form" class="d-none d-sm-inline-block form-inline mr-auto ml-md-3 my-2 my-md-0 mw-100 navbar-search" action="', $scripturl, '?action=search2" method="post" accept-charset="', $context['character_set'], '">
							<div class="input-group">
							<input class="form-control bg-light border-0 small" type="search" name="search" value="">&nbsp;';

					// Using the quick search dropdown?
					$selected = !empty($context['current_topic']) ? 'current_topic' : (!empty($context['current_board']) ? 'current_board' : 'all');

					echo '
							<select name="search_selection" class="custom-select custom-select-sm form-control form-control-sm" >
								<option value="all"', ($selected == 'all' ? ' selected' : ''), '>', $txt['search_entireforum'], ' </option>';

					// Can't limit it to a specific topic if we are not in one
					if (!empty($context['current_topic']))
						echo '
								<option value="topic"', ($selected == 'current_topic' ? ' selected' : ''), '>', $txt['search_thistopic'], '</option>';

					// Can't limit it to a specific board if we are not in one
					if (!empty($context['current_board']))
						echo '
								<option value="board"', ($selected == 'current_board' ? ' selected' : ''), '>', $txt['search_thisboard'], '</option>';

					// Can't search for members if we can't see the memberlist
					if (!empty($context['allow_memberlist']))
						echo '
								<option value="members"', ($selected == 'members' ? ' selected' : ''), '>', $txt['search_members'], ' </option>';

					echo '
							</select>';

					// Search within current topic?
					if (!empty($context['current_topic']))
						echo '
							<input type="hidden" name="sd_topic" value="', $context['current_topic'], '">';

					// If we're on a certain board, limit it to this board ;).
					elseif (!empty($context['current_board']))
						echo '
							<input type="hidden" name="sd_brd" value="', $context['current_board'], '">';

					echo '<div class="input-group-append">
							<button class="btn btn-primary" name="search2" type="submit">
									<i class="main_icons search fa-sm"></i>
							 </button>
						 </div>
						 </div>	
						</form>';
				}
					echo '           
						<ul class="navbar-nav ml-auto" id="top_info"> ';
						
				if ($context['user']['is_logged'])
				{
					// Thirdly, alerts
					echo '
							<li class="nav-item">
								<a href="', $scripturl, '?action=profile;area=showalerts;u=', $context['user']['id'], '"', !empty($context['self_alerts']) ? ' class="nav-link active"' : ' class="nav-link"', ' id="alerts_menu_top">
									<span class="main_icons bell"></span>
									', !empty($context['user']['alerts']) ? '
									<span class="badge badge-danger badge-counter">' . $context['user']['alerts'] . '</span>' : '', '
								</a>
								<div id="alerts_menu" class="top_menu scrollable"></div>
							</li>';

					// Secondly, PMs if we're doing them
					if ($context['allow_pm'])
						echo '
							<li class="nav-item">
								<a href="', $scripturl, '?action=pm"', !empty($context['self_pm']) ? ' class="nav-link active"' : ' class="nav-link"', ' id="pm_menu_top">
									<span class="main_icons envelope"></span>
									', !empty($context['user']['unread_messages']) ? '
									<span class="badge badge-danger badge-counter">' . $context['user']['unread_messages'] . '</span>' : '', '
								</a>
								<div id="pm_menu" class="top_menu scrollable"></div>
							</li>';
							
					// Firstly, the user's menu
					echo ' <li class="topbar-divider d-none d-sm-block"></li>
							<li class="nav-item">
								<a href="', $scripturl, '?action=profile"', !empty($context['self_profile']) ? ' class="nav-link active"' : ' class="nav-link"', ' id="profile_menu_top" onclick="return false;"><span class="mr-2 d-none d-lg-inline text-gray-600">', $context['user']['name'], '</span>';

					if (!empty($context['user']['avatar']))
						echo $context['user']['avatar']['image'];

					echo ' </a><div id="profile_menu" class="top_menu"></div>
							</li>';		
					// A logout button for people without JavaScript.
					if (empty($settings['login_main_menu']))
						echo '
							<li class="nav-item" id="nojs_logout">
								<a class="nav-link" href="', $scripturl, '?action=logout;', $context['session_var'], '=', $context['session_id'], '">', $txt['logout'], '</a>
								<script>document.getElementById("nojs_logout").style.display = "none";</script>
							</li>';		
				}
				// Otherwise they're a guest. Ask them to either register or login.
				elseif (empty($maintenance))
				{
					// Some people like to do things the old-fashioned way.
					if (!empty($settings['login_main_menu']))
					{
						echo '
							<li class="nav-item welcome">', sprintf($txt[$context['can_register'] ? 'welcome_guest_register' : 'welcome_guest'], $context['forum_name_html_safe'], $scripturl . '?action=login', 'return reqOverlayDiv(this.href, ' . JavaScriptEscape($txt['login']) . ', \'login\');', $scripturl . '?action=signup'), '</li>';
					}
					else
					{
						echo '
						
							<li class="button_login">
								<a href="', $scripturl, '?action=login" class="btn btn-success btn-icon-split ', $context['current_action'] == 'login' ? 'active' : 'open','" onclick="return reqOverlayDiv(this.href, ' . JavaScriptEscape($txt['login']) . ', \'login\');">
									<span class="main_icons login icon text-white-50"></span>
									<span class="textmenu">', $txt['login'], '</span>
								</a>
							</li>
							<li class="button_signup">
								<a href="', $scripturl, '?action=signup" class="btn btn-primary btn-icon-split ', $context['current_action'] == 'signup' ? 'active' : 'open','">
									<span class="main_icons regcenter icon text-white-50"></span>
									<span class="textmenu">', $txt['register'], '</span>
								</a>
							</li>';
					}
				}
				else
					// In maintenance mode, only login is allowed and don't show OverlayDiv
					echo '
							<li class="nav-item">', sprintf($txt['welcome_guest'], $context['forum_name_html_safe'], $scripturl . '?action=login', 'return true;'), '</li>';


				 echo'    
					</ul>
				</nav>';
				
	echo '
	<div id="header">
		<h1 class="forumtitle">
			<a id="top" href="', $scripturl, '">', empty($context['header_logo_url_html_safe']) ? $context['forum_name_html_safe'] : '<img src="' . $context['header_logo_url_html_safe'] . '" alt="' . $context['forum_name_html_safe'] . '">', '</a>
		</h1>';

	echo '
		', empty($settings['site_slogan']) ? '<img id="smflogo" src="' . $settings['images_url'] . '/logo.png" alt="Simple Machines Forum" title="Simple Machines Forum">' : '<div id="siteslogan">' . $settings['site_slogan'] . '</div>', '';

	echo '
	</div>
	<div id="wrapper">';

	// The main content should go here.
	echo '
		<div id="content_section">
			<div id="main_content_section">';
}

/**
 * The stuff shown immediately below the main content, including the footer
 */
function template_body_below()
{
	global $context, $txt, $scripturl, $modSettings,$settings;
	echo '
			</div><!-- #main_content_section -->
		</div><!-- #content_section -->
	</div><!-- #wrapper -->
</div></div><!-- #footerfix -->';

	// Show the footer with copyright, terms and help links.
	echo '
	<div id="footer">
		<div class="inner_wrap">';
	if (!empty($modSettings['userLanguage']) && !empty($context['languages']) && count($context['languages']) > 1)
	{
		echo '
			<form id="languages_form" method="get" class="floatright">
				<select class="custom-select custom-select-sm form-control form-control-sm" id="language_select" name="language" onchange="this.form.submit()">';

		foreach ($context['languages'] as $language)
			echo '
					<option value="', $language['filename'], '"', isset($context['user']['language']) && $context['user']['language'] == $language['filename'] ? ' selected="selected"' : '', '>', str_replace('-utf8', '', $language['name']), '</option>';

		echo '
				</select>
				<noscript>
					<input type="submit" value="', $txt['quick_mod_go'], '">
				</noscript>
			</form>';
	}
	// There is now a global "Go to top" link at the right.
	echo '
		<ul>
			<li class="floatright"><a href="', $scripturl, '?action=help">', $txt['help'], '</a> ', (!empty($modSettings['requireAgreement'])) ? '| <a href="' . $scripturl . '?action=agreement">' . $txt['terms_and_rules'] . '</a>' : '', ' | <a href="#search_form">', $txt['go_up'], ' &#9650;</a></li>
			<li class="copyright">', theme_copyright(), '</li>
			<li class="copyright">'.$txt['themecop']. '</li>
		</ul>';

	// Show the load time?
	if ($context['show_load_time'])
		echo '
		<p>', sprintf($txt['page_created_full'], $context['load_time'], $context['load_queries']), '</p>';
	if(!empty($settings['snrjfooter']))
		{
		echo'<br class="clear"/>
		<div class="row">
			 <div class="col-md-3">
				<h5 class="footbaslik">'.(!empty($settings['snrjhakkindabaslik'])? ''.$settings['snrjhakkindabaslik'].'':''.$txt['snrjfootertext1'].'') .'</h5>
				<p>'.(!empty($settings['snrjhakkindaicerik'])? ''.$settings['snrjhakkindaicerik'].'':''.$txt['snrjfootertext2'].'') .'</p>
			 </div>
			 <div class="col-md-3">
				<h5 class="footbaslik">'.(!empty($settings['snrjlinkbaslik'])? ''.$settings['snrjlinkbaslik'].'':''.$txt['snrjfootertext3'].'') .'</h5>
				<ul>
					<li>'.(!empty($settings['snrjhizlilink'])? '<a href="'.$settings['snrjhizlilink'].'" title="'.$settings['snrjhizlibaslik'].'">'.$settings['snrjhizlibaslik'].'</a>':''.$txt['snrjfootertext4'].'' ).'</li>
					<li>'.(!empty($settings['snrjhizlilink1'])? '<a href="'.$settings['snrjhizlilink1'].'" title="'.$settings['snrjhizlibaslik1'].'">'.$settings['snrjhizlibaslik1'].'</a>':''.$txt['snrjfootertext4'].'' ).'</li>
					<li>'.(!empty($settings['snrjhizlilink2'])? '<a href="'.$settings['snrjhizlilink2'].'" title="'.$settings['snrjhizlibaslik2'].'">'.$settings['snrjhizlibaslik2'].'</a>':''.$txt['snrjfootertext4'].'' ).'</li>
					<li>'.(!empty($settings['snrjhizlilink3'])? '<a href="'.$settings['snrjhizlilink3'].'" title="'.$settings['snrjhizlibaslik3'].'">'.$settings['snrjhizlibaslik3'].'</a>':''.$txt['snrjfootertext4'].'' ).'</li>
				</ul>
			 </div>
			 <div class="col-md-3">
				<h5 class="footbaslik">'.(!empty($settings['snrjlinkbaslik1'])? ''.$settings['snrjlinkbaslik1'].'':''.$txt['snrjfootertext3'].'' ).'</h5>
				<ul>
					<li>'.(!empty($settings['snrjhizlilink4'])? '<a href="'.$settings['snrjhizlilink4'].'" title="'.$settings['snrjhizlibaslik4'].'">'.$settings['snrjhizlibaslik4'].'</a>':''.$txt['snrjfootertext4'].'' ).'</li>
					<li>'.(!empty($settings['snrjhizlilink5'])? '<a href="'.$settings['snrjhizlilink5'].'" title="'.$settings['snrjhizlibaslik5'].'">'.$settings['snrjhizlibaslik5'].'</a>':''.$txt['snrjfootertext4'].'' ).'</li>
					<li>'.(!empty($settings['snrjhizlilink6'])? '<a href="'.$settings['snrjhizlilink6'].'" title="'.$settings['snrjhizlibaslik6'].'">'.$settings['snrjhizlibaslik6'].'</a>':''.$txt['snrjfootertext4'].'' ).'</li>
					<li>'.(!empty($settings['snrjhizlilink7'])? '<a href="'.$settings['snrjhizlilink7'].'" title="'.$settings['snrjhizlibaslik7'].'">'.$settings['snrjhizlibaslik7'].'</a>':''.$txt['snrjfootertext4'].'' ).'</li>
				</ul>
			 </div>
			 <div class="col-md-3">
				<h5 class="footbaslik">'.(!empty($settings['snrjbilgibaslik'])? ''.$settings['snrjbilgibaslik'].'':''.$txt['snrjfootertext5'].'') .'</h5>
				<p>'.(!empty($settings['snrjbilgiicerik'])? ''.$settings['snrjbilgiicerik'].'':''.$txt['snrjfootertext6'].'') .'</p>
			 </div> 
		</div>';
		}
	echo '
		</div>
	</div><!-- #footer -->';
}

/**
 * This shows any deferred JavaScript and closes out the HTML
 */
function template_html_below()
{
	
	echo'<script>(function($) {
  "use strict"; // Start of use strict

  // Toggle the side navigation
  $("#sidebarToggle, #sidebarToggleTop").on(\'click\', function(e) {
    $("body").toggleClass("sidebar-toggled");
    $(".sidebar").toggleClass("toggled");
    $(".sidebar-haberler-yok-et").toggleClass("display_none");
    if ($(".sidebar").hasClass("toggled")) {
      $(\'.sidebar .collapse\').collapse(\'hide\');
    };
  });

  // Close any open menu accordions when window is resized below 768px
  $(window).resize(function() {
    if ($(window).width() < 768) {
      $(\'.sidebar .collapse\').collapse(\'hide\');
    };
    
    // Toggle the side navigation when window is resized below 480px
    if ($(window).width() < 480 && !$(".sidebar").hasClass("toggled")) {
      $("body").addClass("sidebar-toggled");
      $(".sidebar").addClass("toggled");
      $(\'.sidebar .collapse\').collapse(\'hide\');
    };
  });

  // Prevent the content wrapper from scrolling when the fixed side navigation hovered over
  $(\'body.fixed-nav .sidebar\').on(\'mousewheel DOMMouseScroll wheel\', function(e) {
    if ($(window).width() > 768) {
      var e0 = e.originalEvent,
        delta = e0.wheelDelta || -e0.detail;
      this.scrollTop += (delta < 0 ? 1 : -1) * 30;
      e.preventDefault();
    }
  });

  // Scroll to top button appear
  $(document).on(\'scroll\', function() {
    var scrollDistance = $(this).scrollTop();
    if (scrollDistance > 100) {
      $(\'.scroll-to-top\').fadeIn();
    } else {
      $(\'.scroll-to-top\').fadeOut();
    }
  });

  // Smooth scrolling using jQuery easing
  $(document).on(\'click\', \'a.scroll-to-top\', function(e) {
    var $anchor = $(this);
    $(\'html, body\').stop().animate({
      scrollTop: ($($anchor.attr(\'href\')).offset().top)
    }, 1000, \'easeInOutExpo\');
    e.preventDefault();
  });

})(jQuery); // End of use strict
</script><svg class="grovmenu">
<defs>
  <filter id="filt">
	<feGaussianBlur in="SourceGraphic" result="blur" stdDeviation="10" />
	<feColorMatrix in="blur" mode="matrix" values="1 0 0 0 0  0 1 0 0 0  0 0 1 0 0  0 0 0 18 -7" result="filt" />
	<feBlend in2="filt" in="SourceGraphic" result="mix" />
  </filter>
</defs>
</svg>
<div class="wrap">
<input type="checkbox" id="checking" style="display:none;" />
<a class="blob" href="?variant=1"><span class="main_icons current_theme"></span></a>
<a class="blob" href="?variant=2"><span class="main_icons current_theme"></span></a>
<a class="blob" href="?variant=3"><span class="main_icons current_theme"></span></a>
<a class="blob" href="?variant=4"><span class="main_icons current_theme"></span></a>
<a class="blob" href="?variant=5"><span class="main_icons current_theme"></span></a>
<label class="blob" for="checking"> 
  <span class="bar"></span> 
  <span class="bar"></span> 
  <span class="bar"></span> 
</label>
</div>';
	// Load in any javascipt that could be deferred to the end of the page
	template_javascript(true);
	echo '
</body>
</html>';
}

/**
 * Show a linktree. This is that thing that shows "My Community | General Category | General Discussion"..
 *
 * @param bool $force_show Whether to force showing it even if settings say otherwise
 */
function theme_linktree($force_show = false)
{
	global $context, $shown_linktree, $scripturl, $txt;

	// If linktree is empty, just return - also allow an override.
	if (empty($context['linktree']) || (!empty($context['dont_default_linktree']) && !$force_show))
		return;

	echo '
				<div class="navigate_section">
					<ul>';

	// Each tree item has a URL and name. Some may have extra_before and extra_after.
	foreach ($context['linktree'] as $link_num => $tree)
	{
		echo '
						<li', ($link_num == count($context['linktree']) - 1) ? ' class="last"' : '', '>';

		// Don't show a separator for the first one.
		// Better here. Always points to the next level when the linktree breaks to a second line.
		// Picked a better looking HTML entity, and added support for RTL plus a span for styling.
		if ($link_num != 0)
			echo '
							<span class="dividers">', $context['right_to_left'] ? ' &#9668; ' : ' &#9658; ', '</span>';

		// Show something before the link?
		if (isset($tree['extra_before']))
			echo $tree['extra_before'], ' ';

		// Show the link, including a URL if it should have one.
		if (isset($tree['url']))
			echo '
							<a href="' . $tree['url'] . '"><span>' . $tree['name'] . '</span></a>';
		else
			echo '
							<span>' . $tree['name'] . '</span>';

		// Show something after the link...?
		if (isset($tree['extra_after']))
			echo ' ', $tree['extra_after'];

		echo '
						</li>';
	}

	echo '
					</ul>
				</div><!-- .navigate_section -->';

	$shown_linktree = true;
}

/**
 * Show the menu up top. Something like [home] [help] [profile] [logout]...
 */
function template_menu()
{
	global $context,$settings,$txt,$scripturl;

	echo '<ul class="navbar-nav renklendir sidebar sidebar-dark accordion" id="accordionSidebar">
			<li>
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="',$scripturl,'">
                <div class="sidebar-brand-icon rotate-n-15">
                    <i class="main_icons laugh-wink"></i>
                </div>
                <div class="sidebar-brand-text mx-3">'.(!empty($settings['menubaslik'])? ''.$settings['menubaslik'].'':''.$txt['menubaslik'].'' ).'</div>
            </a>
			</li>';

	// Note: Menu markup has been cleaned up to remove unnecessary spans and classes.
	foreach ($context['menu_buttons'] as $act => $button)
	{
		echo '
						<li class="nav-item button_', $act, '">
								<a class="nav-link '.($button['active_button'] ? ' active ' : ' '.(empty($button['sub_buttons']) ? ' ' : ' collapsed').'').'" 
								'.(empty($button['sub_buttons']) ? ' ' : ' data-toggle="collapse" data-target="#' . $act . '" aria-expanded="true" aria-controls="' .$act . '"').'
								href="', $button['href'], '"', isset($button['target']) ? ' target="' . $button['target'] . '"' : '', isset($button['onclick']) ? ' onclick="' . $button['onclick'] . '"' : '', '>', $button['icon'], '<span class="textmenu">', $button['title'], !empty($button['amt']) ? ' <span class="amt">' . $button['amt'] . '</span>' : '', '</span>
								</a>';

		// 2nd level menus
		if (!empty($button['sub_buttons']))
		{
			echo '<div id="' . $act . '" class="collapse" data-parent="#accordionSidebar">
							<ul class="text-gray-100 py-2 collapse-inner rounded">';

			foreach ($button['sub_buttons'] as $ac => $childbutton)
			{
				echo '
								<li', !empty($childbutton['sub_buttons']) ? ' class="subsections"' : '', '>
									<a href="', $childbutton['href'], '"', isset($childbutton['target']) ? ' target="' . $childbutton['target'] . '"' : '', isset($childbutton['onclick']) ? ' onclick="' . $childbutton['onclick'] . '"' : '', '>
										<span class="main_icons ', $ac,'"></span>', $childbutton['title'], !empty($childbutton['amt']) ? ' <span class="amt">' . $childbutton['amt'] . '</span>' : '', '
									</a>';
				// 3rd level menus :)
				if (!empty($childbutton['sub_buttons']))
				{
					echo '
									<ul>';

					foreach ($childbutton['sub_buttons'] as $grandchildbutton)
						echo '
										<li>
											<a href="', $grandchildbutton['href'], '"', isset($grandchildbutton['target']) ? ' target="' . $grandchildbutton['target'] . '"' : '', isset($grandchildbutton['onclick']) ? ' onclick="' . $grandchildbutton['onclick'] . '"' : '', '>
												', $grandchildbutton['title'], !empty($grandchildbutton['amt']) ? ' <span class="amt">' . $grandchildbutton['amt'] . '</span>' : '', '
											</a>
										</li>';

					echo '
									</ul>';
				}

				echo '
								</li>';
			}
			echo '
							</ul> </div>';
		}
		echo '
						</li>';
	}

	if ($context['user']['is_logged'])
		echo '
							<li class="nav-item"><hr class="sidebar-divider">
								<a class="nav-link" href="', $scripturl, '?action=unread" title="', $txt['unread_since_visit'], '">', $txt['view_unread_category'], '</a>
							</li>
							<li class="nav-item">
								<a class="nav-link" href="', $scripturl, '?action=unreadreplies" title="', $txt['show_unread_replies'], '">', $txt['unread_replies'], '</a>
							</li>';

	if (!empty($settings['enable_news']) && !empty($context['random_news_line']))
		echo ' 
			<li class="sidebar-haberler-yok-et"><hr class="sidebar-divider">
				<div class="sidebar-heading">', $txt['news'], '</div>
					<div class="sidebar-card d-none d-lg-flex">
						<pc lass="text-center mb-2">', $context['random_news_line'], '</p>
					</div>
			</li>';

	echo '<li class="text-center d-none d-md-inline">
                <button class="rounded-circle border-0" id="sidebarToggle"></button>
            </li>
        </ul>';
}
function template_menu2()
{
global $context;

	echo '
					<ul class="dropmenu menu_nav konusal">';

	// Note: Menu markup has been cleaned up to remove unnecessary spans and classes.
	foreach ($context['menu_buttons'] as $act => $button)
	{
		echo '
						<li class="button_', $act, '', !empty($button['sub_buttons']) ? ' subsections"' : '"', '>
							<a', $button['active_button'] ? ' class="active"' : '', ' href="', $button['href'], '"', isset($button['target']) ? ' target="' . $button['target'] . '"' : '', isset($button['onclick']) ? ' onclick="' . $button['onclick'] . '"' : '', '>
								', $button['icon'], '<span class="textmenu">', $button['title'], !empty($button['amt']) ? ' <span class="amt">' . $button['amt'] . '</span>' : '', '</span>
							</a>';

		// 2nd level menus
		if (!empty($button['sub_buttons']))
		{
			echo '
							<ul>';

			foreach ($button['sub_buttons'] as $ac => $childbutton)
			{
				echo '
								<li', !empty($childbutton['sub_buttons']) ? ' class="subsections"' : '', '>
									<a href="', $childbutton['href'], '"', isset($childbutton['target']) ? ' target="' . $childbutton['target'] . '"' : '', isset($childbutton['onclick']) ? ' onclick="' . $childbutton['onclick'] . '"' : '', '>
										<span class="main_icons ', $ac,'"></span>', $childbutton['title'], !empty($childbutton['amt']) ? ' <span class="amt">' . $childbutton['amt'] . '</span>' : '', '
									</a>';
				// 3rd level menus :)
				if (!empty($childbutton['sub_buttons']))
				{
					echo '
									<ul>';

					foreach ($childbutton['sub_buttons'] as $grandchildbutton)
						echo '
										<li>
											<a href="', $grandchildbutton['href'], '"', isset($grandchildbutton['target']) ? ' target="' . $grandchildbutton['target'] . '"' : '', isset($grandchildbutton['onclick']) ? ' onclick="' . $grandchildbutton['onclick'] . '"' : '', '>
												', $grandchildbutton['title'], !empty($grandchildbutton['amt']) ? ' <span class="amt">' . $grandchildbutton['amt'] . '</span>' : '', '
											</a>
										</li>';

					echo '
									</ul>';
				}

				echo '
								</li>';
			}
			echo '
							</ul>';
		}
		echo '
						</li>';
	}

	echo '
					</ul><!-- .menu_nav -->';

}
/**
 * Generate a strip of buttons.
 *
 * @param array $button_strip An array with info for displaying the strip
 * @param string $direction The direction
 * @param array $strip_options Options for the button strip
 */
function template_button_strip($button_strip, $direction = '', $strip_options = array())
{
	global $context, $txt;

	if (!is_array($strip_options))
		$strip_options = array();

	// Create the buttons...
	$buttons = array();
	foreach ($button_strip as $key => $value)
	{
		// As of 2.1, the 'test' for each button happens while the array is being generated. The extra 'test' check here is deprecated but kept for backward compatibility (update your mods, folks!)
		if (!isset($value['test']) || !empty($context[$value['test']]))
		{
			if (!isset($value['id']))
				$value['id'] = $key;

			$button = '
				<a class="button button_strip_' . $key . (!empty($value['active']) ? ' active' : '') . (isset($value['class']) ? ' ' . $value['class'] : '') . '" ' . (!empty($value['url']) ? 'href="' . $value['url'] . '"' : '') . ' ' . (isset($value['custom']) ? ' ' . $value['custom'] : '') . '>'.(!empty($value['icon']) ? '<span class="main_icons '.$value['icon'].'"></span>' : '').'' . $txt[$value['text']] . '</a>';

			if (!empty($value['sub_buttons']))
			{
				$button .= '
					<div class="top_menu dropmenu ' . $key . '_dropdown">
						<div class="viewport">
							<div class="overview">';
				foreach ($value['sub_buttons'] as $element)
				{
					if (isset($element['test']) && empty($context[$element['test']]))
						continue;

					$button .= '
								<a href="' . $element['url'] . '"><strong>' . $txt[$element['text']] . '</strong>';
					if (isset($txt[$element['text'] . '_desc']))
						$button .= '<br><span>' . $txt[$element['text'] . '_desc'] . '</span>';
					$button .= '</a>';
				}
				$button .= '
							</div><!-- .overview -->
						</div><!-- .viewport -->
					</div><!-- .top_menu -->';
			}

			$buttons[] = $button;
		}
	}

	// No buttons? No button strip either.
	if (empty($buttons))
		return;

	echo '
		<div class="buttonlist', !empty($direction) ? ' float' . $direction : '', '"', (empty($buttons) ? ' style="display: none;"' : ''), (!empty($strip_options['id']) ? ' id="' . $strip_options['id'] . '"' : ''), '>
			', implode('', $buttons), '
		</div>';
}

/**
 * Generate a list of quickbuttons.
 *
 * @param array $list_items An array with info for displaying the strip
 * @param string $list_class Used for integration hooks and as a class name
 * @param string $output_method The output method. If 'echo', simply displays the buttons, otherwise returns the HTML for them
 * @return void|string Returns nothing unless output_method is something other than 'echo'
 */
function template_quickbuttons($list_items, $list_class = null, $output_method = 'echo')
{
	global $txt;

	// Enable manipulation with hooks
	if (!empty($list_class))
		call_integration_hook('integrate_' . $list_class . '_quickbuttons', array(&$list_items));

	// Make sure the list has at least one shown item
	foreach ($list_items as $key => $li)
	{
		// Is there a sublist, and does it have any shown items
		if ($key == 'more')
		{
			foreach ($li as $subkey => $subli)
				if (isset($subli['show']) && !$subli['show'])
					unset($list_items[$key][$subkey]);

			if (empty($list_items[$key]))
				unset($list_items[$key]);
		}
		// A normal list item
		elseif (isset($li['show']) && !$li['show'])
			unset($list_items[$key]);
	}

	// Now check if there are any items left
	if (empty($list_items))
		return;

	// Print the quickbuttons
	$output = '
		<ul class="quickbuttons' . (!empty($list_class) ? ' quickbuttons_' . $list_class : '') . '">';

	// This is used for a list item or a sublist item
	$list_item_format = function($li)
	{
		$html = '
			<li' . (!empty($li['class']) ? ' class="' . $li['class'] . '"' : '') . (!empty($li['id']) ? ' id="' . $li['id'] . '"' : '') . (!empty($li['custom']) ? ' ' . $li['custom'] : '') . '>';

		if (isset($li['content']))
			$html .= $li['content'];
		else
			$html .= '
				<a href="' . (!empty($li['href']) ? $li['href'] : 'javascript:void(0);') . '"' . (!empty($li['javascript']) ? ' ' . $li['javascript'] : '') . '>
					' . (!empty($li['icon']) ? '<span class="main_icons ' . $li['icon'] . '"></span>' : '') . (!empty($li['label']) ? $li['label'] : '') . '
				</a>';

		$html .= '
			</li>';

		return $html;
	};

	foreach ($list_items as $key => $li)
	{
		// Handle the sublist
		if ($key == 'more')
		{
			$output .= '
			<li class="post_options">
				<a href="javascript:void(0);">' . $txt['post_options'] . '</a>
				<ul>';

			foreach ($li as $subli)
				$output .= $list_item_format($subli);

			$output .= '
				</ul>
			</li>';
		}
		// Ordinary list item
		else
			$output .= $list_item_format($li);
	}

	$output .= '
		</ul><!-- .quickbuttons -->';

	// There are a few spots where the result needs to be returned
	if ($output_method == 'echo')
		echo $output;
	else
		return $output;
}

/**
 * The upper part of the maintenance warning box
 */
function template_maint_warning_above()
{
	global $txt, $context, $scripturl;

	echo '
	<div class="errorbox" id="errors">
		<dl>
			<dt>
				<strong id="error_serious">', $txt['forum_in_maintenance'], '</strong>
			</dt>
			<dd class="error" id="error_list">
				', sprintf($txt['maintenance_page'], $scripturl . '?action=admin;area=serversettings;' . $context['session_var'] . '=' . $context['session_id']), '
			</dd>
		</dl>
	</div>';
}

/**
 * The lower part of the maintenance warning box.
 */
function template_maint_warning_below()
{

}

?>