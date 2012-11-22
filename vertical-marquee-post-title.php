<?php

/*
Plugin Name: Vertical marquee post title
Description: This plug-in will create the vertical marquee effect in your website, if you want your post title to move vertically (scroll upward or downwards) in the screen use this plug-in.
Author: Gopi.R
Version: 1.1
Plugin URI: http://www.gopiplus.com/work/2012/09/02/vertical-marquee-post-title-wordpress-plugin/
Author URI: http://www.gopiplus.com/work/2012/09/02/vertical-marquee-post-title-wordpress-plugin/
Donate link: http://www.gopiplus.com/work/2012/09/02/vertical-marquee-post-title-wordpress-plugin/
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/

function vmptshow()
{
	$vmpt_setting = get_option('vmpt_setting');
	$array = array("setting" => $vmpt_setting);
	echo vmpt_shortcode($array);	
}

function vmpt_shortcode( $atts ) 
{
	
	global $wpdb;
	$vmpt_marquee = "";
	//[vmpt setting="1"]
	if ( ! is_array( $atts ) )	{ return ''; }
	$setting = $atts['setting'];
	switch ($setting) 
	{ 
		case 1: 
			$vmpt_setting = get_option('vmpt_setting1');
			break;
		case 2: 
			$vmpt_setting = get_option('vmpt_setting2');
			break;
		case 3: 
			$vmpt_setting = get_option('vmpt_setting3');
			break;
		case 4: 
			$vmpt_setting = get_option('vmpt_setting4');
			break;
		default:
			$vmpt_setting = get_option('vmpt_setting1');
	}
	
	@list($vmpt_scrollamount, $vmpt_scrolldelay, $vmpt_direction, $vmpt_style, $vmpt_noofpost, $vmpt_categories, $vmpt_orderbys, $vmpt_order, $vmpt_spliter) = explode("~~", @$vmpt_setting);
	
	if(!is_numeric($vmpt_scrollamount)){ $vmpt_scrollamount = 2; } 
	if(!is_numeric($vmpt_scrolldelay)){ $vmpt_scrolldelay = 5; } 
	if(!is_numeric($vmpt_noofpost)){ $vmpt_noofpost = 10; }
	
	$sSql = query_posts('cat='.$vmpt_categories.'&orderby='.$vmpt_orderbys.'&order='.$vmpt_order.'&showposts='.$vmpt_noofpost);
	
	if ( ! empty($sSql) ) 
	{
		@$count = 0;
		foreach ( $sSql as $sSql ) 
		{
			@$title = stripslashes($sSql->post_title);
			@$link = get_permalink($sSql->ID);
			if($count==0) 
			{  
				if($link != "") { @$vmpt = @$vmpt . "<a href='".@$link."'>"; } 
				$vmpt = $vmpt . @$title;
				if($link != "") { $vmpt = $vmpt . "</a>"; }
			}
			else
			{
				$vmpt = $vmpt . "   <br /><br />   ";
				if($link != "") { $vmpt = $vmpt . "<a href='".$link."'>"; } 
				$vmpt = $vmpt . @$title;
				if($link != "") { @$vsm = @$vsm . "</a>"; }
			}
			$count = $count + 1;
		}
	}
	wp_reset_query();
	$vmpt_marquee = $vmpt_marquee . "<div style='padding:3px;' class='vmpt_marquee'>";
	$vmpt_marquee = $vmpt_marquee . "<marquee style='$vmpt_style' scrollamount='$vmpt_scrollamount' scrolldelay='$vmpt_scrolldelay' direction='$vmpt_direction' onmouseover='this.stop()' onmouseout='this.start()'>";
	$vmpt_marquee = $vmpt_marquee . $vmpt;
	$vmpt_marquee = $vmpt_marquee . "</marquee>";
	$vmpt_marquee = $vmpt_marquee . "</div>";
	return $vmpt_marquee;	
}

function vmpt_install() 
{
	add_option('vmpt_title', "Marquee post title");
	add_option('vmpt_setting', "1");
	add_option('vmpt_setting1', "2~~5~~up~~height:100px;~~10~~~~ID~~DESC");
	add_option('vmpt_setting2', "2~~5~~up~~color:#FF0000;font:Arial;height:100px;~~10~~~~ID~~DESC");
	add_option('vmpt_setting3', "2~~5~~down~~color:#FF0000;font:Arial;height:120px;~~10~~~~title~~DESC");
	add_option('vmpt_setting4', "2~~5~~down~~color:#FF0000;font:Arial;height:140px;~~10~~~~rand~~DESC");
}

function vmpt_widget($args) 
{
	extract($args);
	if(get_option('vmpt_title') <> "")
	{
		echo $before_widget;
		echo $before_title;
		echo get_option('vmpt_title');
		echo $after_title;
	}
	vmptshow();
	if(get_option('vmpt_title') <> "")
	{
		echo $after_widget;
	}
}
	
function vmpt_control() 
{
	$vmpt_title = get_option('vmpt_title');
	$vmpt_setting = get_option('vmpt_setting');
	if (@$_POST['vmpt_submit']) 
	{
		$vmpt_title = $_POST['vmpt_title'];
		$vmpt_setting = $_POST['vmpt_setting'];
		update_option('vmpt_title', $vmpt_title );
		update_option('vmpt_setting', $vmpt_setting );
	}
	
	$setting1 = "";
	$setting2 = "";
	$setting3 = "";
	$setting4 = "";
	if($vmpt_setting == "1") { $setting1 = "selected"; }
	if($vmpt_setting == "2") { $setting2 = "selected"; }
	if($vmpt_setting == "3") { $setting3 = "selected"; }
	if($vmpt_setting == "4") { $setting4 = "selected"; }
	
	echo '<p>Widget Title:<br><input  style="width: 200px;" type="text" value="';
	echo $vmpt_title . '" name="vmpt_title" id="vmpt_title" /></p>';
	echo '<p>Rss Setting:<br><select name="vmpt_setting" id="vmpt_setting">';
	echo '<option value="1" '.$setting1.'>Setting 1</option>';
	echo '<option value="2" '.$setting2.'>Setting 2</option>';
	echo '<option value="3" '.$setting3.'>Setting 3</option>';
	echo '<option value="4" '.$setting4.'>Setting 4</option>';
	echo '</select>';
	echo '<input type="hidden" id="vmpt_submit" name="vmpt_submit" value="1" />';
}

function vmpt_widget_init()
{
	if(function_exists('wp_register_sidebar_widget')) 
	{
		wp_register_sidebar_widget('post-title-marquee-scroll', 'Vertical marquee post title', 'vmpt_widget');
	}
	
	if(function_exists('wp_register_widget_control')) 
	{
		wp_register_widget_control('post-title-marquee-scroll', array('Vertical marquee post title', 'widgets'), 'vmpt_control');
	} 
}

function vmpt_deactivation() 
{

}

function vmpt_option() 
{
	global $wpdb;
	echo '<h2>Vertical marquee post title</h2>';
	
	$vmpt_setting1 = get_option('vmpt_setting1');
	$vmpt_setting2 = get_option('vmpt_setting2');
	$vmpt_setting3 = get_option('vmpt_setting3');
	$vmpt_setting4 = get_option('vmpt_setting4');
	
	list($a1, $b1, $c1, $d1, $e1, $f1, $g1, $h1) = explode("~~", $vmpt_setting1);
	list($a2, $b2, $c2, $d2, $e2, $f2, $g2, $h2) = explode("~~", $vmpt_setting2);
	list($a3, $b3, $c3, $d3, $e3, $f3, $g3, $h3) = explode("~~", $vmpt_setting3);
	list($a4, $b4, $c4, $d4, $e4, $f4, $g4, $h4) = explode("~~", $vmpt_setting4);
	
	if (@$_POST['vmpt_submit']) 
	{	
		$a1 = stripslashes($_POST['vmpt_scrollamount1']);
		$b1 = stripslashes($_POST['vmpt_scrolldelay1']);
		$c1 = stripslashes($_POST['vmpt_direction1']);
		$d1 = stripslashes($_POST['vmpt_style1']);
		$e1 = stripslashes($_POST['vmpt_noofpost1']);
		$f1 = stripslashes($_POST['vmpt_categories1']);
		$g1 = stripslashes($_POST['vmpt_orderbys1']);
		$h1 = stripslashes($_POST['vmpt_order1']);
		
		$a2 = stripslashes($_POST['vmpt_scrollamount2']);
		$b2 = stripslashes($_POST['vmpt_scrolldelay2']);
		$c2 = stripslashes($_POST['vmpt_direction2']);
		$d2 = stripslashes($_POST['vmpt_style2']);
		$e2 = stripslashes($_POST['vmpt_noofpost2']);
		$f2 = stripslashes($_POST['vmpt_categories2']);
		$g2 = stripslashes($_POST['vmpt_orderbys2']);
		$h2 = stripslashes($_POST['vmpt_order2']);
		
		$a3 = stripslashes($_POST['vmpt_scrollamount3']);
		$b3 = stripslashes($_POST['vmpt_scrolldelay3']);
		$c3 = stripslashes($_POST['vmpt_direction3']);
		$d3 = stripslashes($_POST['vmpt_style3']);
		$e3 = stripslashes($_POST['vmpt_noofpost3']);
		$f3 = stripslashes($_POST['vmpt_categories3']);
		$g3 = stripslashes($_POST['vmpt_orderbys3']);
		$h3 = stripslashes($_POST['vmpt_order3']);
		
		$a4 = stripslashes($_POST['vmpt_scrollamount4']);
		$b4 = stripslashes($_POST['vmpt_scrolldelay4']);
		$c4 = stripslashes($_POST['vmpt_direction4']);
		$d4 = stripslashes($_POST['vmpt_style4']);
		$e4 = stripslashes($_POST['vmpt_noofpost4']);
		$f4 = stripslashes($_POST['vmpt_categories4']);
		$g4 = stripslashes($_POST['vmpt_orderbys4']);
		$h4 = stripslashes($_POST['vmpt_order4']);	
		
		update_option('vmpt_title', @$vmpt_title );
		update_option('vmpt_setting1', @$a1 . "~~" . @$b1 . "~~" . @$c1 . "~~" . @$d1 . "~~" . @$e1 . "~~" . @$f1 . "~~" . @$g1 . "~~" . @$h1 . "~~" . @$i1 );
		update_option('vmpt_setting2', @$a2 . "~~" . @$b2 . "~~" . @$c2 . "~~" . @$d2 . "~~" . @$e2 . "~~" . @$f2 . "~~" . @$g2 . "~~" . @$h2 . "~~" . @$i2 );
		update_option('vmpt_setting3', @$a3 . "~~" . @$b3 . "~~" . @$c3 . "~~" . @$d3 . "~~" . @$e3 . "~~" . @$f3 . "~~" . @$g3 . "~~" . @$h3 . "~~" . @$i3 );
		update_option('vmpt_setting4', @$a4 . "~~" . @$b4 . "~~" . @$c4 . "~~" . @$d4 . "~~" . @$e4 . "~~" . @$f4 . "~~" . @$g4 . "~~" . @$h4 . "~~" . @$i4 );
		
	}
	
	echo '<form name="vmpt_form" method="post" action="">';
	?>
	<table width="800" border="0" cellspacing="0" cellpadding="0">
	  <tr>
		<td>
		<?php
		echo '<h2>Setting 1</h2>';
		
		echo '<p>Scroll amount :<br><input  style="width: 100px;" type="text" value="';
		echo $a1 . '" name="vmpt_scrollamount1" id="vmpt_scrollamount1" /></p>';
		
		echo '<p>Scroll delay :<br><input  style="width: 100px;" type="text" value="';
		echo $b1 . '" name="vmpt_scrolldelay1" id="vmpt_scrolldelay1" /></p>';
		
		echo '<p>Scroll direction :<br><input  style="width: 100px;" type="text" value="';
		echo $c1 . '" name="vmpt_direction1" id="vmpt_direction1" /> (Up/Down)</p>';
		
		echo '<p>Scroll style :<br><input  style="width: 250px;" type="text" value="';
		echo $d1 . '" name="vmpt_style1" id="vmpt_style1" /></p>';
	
		echo '<p>Number of post :<br><input  style="width: 100px;" type="text" value="';
		echo $e1 . '" name="vmpt_noofpost1" id="vmpt_noofpost1" /></p>';
		
		echo '<p>Post categories :<br><input  style="width: 200px;" type="text" value="';
		echo $f1 . '" name="vmpt_categories1" id="vmpt_categories1" /> (Example: 1, 3, 4) <br> Category IDs, separated by commas.</p>';
		
		echo '<p>Post orderbys :<br><input  style="width: 200px;" type="text" value="';
		echo $g1 . '" name="vmpt_orderbys1" id="vmpt_orderbys1" /> (Any 1 from below list) <br> ID/author/title/rand/date/category/modified</p>';
		
		echo '<p>Post order : <br><input  style="width: 100px;" type="text" value="';
		echo $h1 . '" name="vmpt_order1" id="vmpt_order1" /> ASC/DESC </p>';
		?>
		</td>
		<td>
		<?php
		echo '<h2>Setting 2</h2>';
		
		echo '<p>Scroll amount :<br><input  style="width: 100px;" type="text" value="';
		echo $a2 . '" name="vmpt_scrollamount2" id="vmpt_scrollamount2" /></p>';
		
		echo '<p>Scroll delay :<br><input  style="width: 100px;" type="text" value="';
		echo $b2 . '" name="vmpt_scrolldelay2" id="vmpt_scrolldelay2" /></p>';
		
		echo '<p>Scroll direction :<br><input  style="width: 100px;" type="text" value="';
		echo $c2 . '" name="vmpt_direction2" id="vmpt_direction2" /> (Up/Down)</p>';
		
		echo '<p>Scroll style :<br><input  style="width: 250px;" type="text" value="';
		echo $d2 . '" name="vmpt_style2" id="vmpt_style2" /></p>';
	
		echo '<p>Number of post :<br><input  style="width: 100px;" type="text" value="';
		echo $e2 . '" name="vmpt_noofpost2" id="vmpt_noofpost2" /></p>';
		
		echo '<p>Post categories :<br><input  style="width: 200px;" type="text" value="';
		echo $f2 . '" name="vmpt_categories2" id="vmpt_categories2" /> (Example: 1, 3, 4) <br> Category IDs, separated by commas.</p>';
		
		echo '<p>Post orderbys :<br><input  style="width: 200px;" type="text" value="';
		echo $g2 . '" name="vmpt_orderbys2" id="vmpt_orderbys2" /> (Any 1 from below list) <br> ID/author/title/rand/date/category/modified</p>';
		
		echo '<p>Post order : <br><input  style="width: 100px;" type="text" value="';
		echo $h2 . '" name="vmpt_order2" id="vmpt_order2" /> ASC/DESC </p>';
		?>
		</td>
	  </tr>
	  <tr>
		<td>
		<?php
		echo '<h2>Setting 3</h2>';
		
		echo '<p>Scroll amount :<br><input  style="width: 100px;" type="text" value="';
		echo $a3 . '" name="vmpt_scrollamount3" id="vmpt_scrollamount3" /></p>';
		
		echo '<p>Scroll delay :<br><input  style="width: 100px;" type="text" value="';
		echo $b3 . '" name="vmpt_scrolldelay3" id="vmpt_scrolldelay3" /></p>';
		
		echo '<p>Scroll direction :<br><input  style="width: 100px;" type="text" value="';
		echo $c3 . '" name="vmpt_direction3" id="vmpt_direction3" /> (Up/Down)</p>';
		
		echo '<p>Scroll style :<br><input  style="width: 250px;" type="text" value="';
		echo $d3 . '" name="vmpt_style3" id="vmpt_style3" /></p>';
		
		echo '<p>Number of post :<br><input  style="width: 100px;" type="text" value="';
		echo $e3 . '" name="vmpt_noofpost3" id="vmpt_noofpost3" /></p>';
		
		echo '<p>Post categories :<br><input  style="width: 200px;" type="text" value="';
		echo $f3 . '" name="vmpt_categories3" id="vmpt_categories3" /> (Example: 1, 3, 4) <br> Category IDs, separated by commas.</p>';
		
		echo '<p>Post orderbys :<br><input  style="width: 200px;" type="text" value="';
		echo $g3 . '" name="vmpt_orderbys3" id="vmpt_orderbys3" /> (Any 1 from below list) <br> ID/author/title/rand/date/category/modified</p>';
		
		echo '<p>Post order : <br><input  style="width: 100px;" type="text" value="';
		echo $h3 . '" name="vmpt_order3" id="vmpt_order3" /> ASC/DESC </p>';
		?>
		</td>
		<td>
		<?php
		echo '<h2>Setting 4</h2>';
		
		echo '<p>Scroll amount :<br><input  style="width: 100px;" type="text" value="';
		echo $a4 . '" name="vmpt_scrollamount4" id="vmpt_scrollamount4" /></p>';
		
		echo '<p>Scroll delay :<br><input  style="width: 100px;" type="text" value="';
		echo $b4 . '" name="vmpt_scrolldelay4" id="vmpt_scrolldelay4" /></p>';
		
		echo '<p>Scroll direction :<br><input  style="width: 100px;" type="text" value="';
		echo $c4 . '" name="vmpt_direction4" id="vmpt_direction4" /> (Up/Down)</p>';
		
		echo '<p>Scroll style :<br><input  style="width: 250px;" type="text" value="';
		echo $d4 . '" name="vmpt_style4" id="vmpt_style4" /></p>';
		
		echo '<p>Number of post :<br><input  style="width: 100px;" type="text" value="';
		echo $e4 . '" name="vmpt_noofpost4" id="vmpt_noofpost4" /></p>';
		
		echo '<p>Post categories :<br><input  style="width: 200px;" type="text" value="';
		echo $f4 . '" name="vmpt_categories4" id="vmpt_categories4" /> (Example: 1, 3, 4) <br> Category IDs, separated by commas.</p>';
		
		echo '<p>Post orderbys :<br><input  style="width: 200px;" type="text" value="';
		echo $g4 . '" name="vmpt_orderbys4" id="vmpt_orderbys4" /> (Any 1 from below list) <br> ID/author/title/rand/date/category/modified</p>';
		
		echo '<p>Post order : <br><input  style="width: 100px;" type="text" value="';
		echo $h4 . '" name="vmpt_order4" id="vmpt_order4" /> ASC/DESC </p>';
		?>
		</td>
	  </tr>
	</table>
	<?php
		
	echo '<input name="vmpt_submit" id="vmpt_submit" lang="publish" class="button-primary" value="Update all 4 settings" type="Submit" />';
	echo '</form>';
	?>
    <h2>Plugin configuration help</h2>
    <ul>
    	<li>Drag and drop the widget</a></li>
        <li>Short code for posts and pages</a></li>
        <li>Add directly in the theme</li>
    </ul>
    <h2>Check official website</h2>
    <ul>
    	<li>Check official website for more information <a href="http://www.gopiplus.com/work/2012/09/02/vertical-marquee-post-title-wordpress-plugin/" target="_blank">Click here</a></li>
    </ul>
    <?php
}

function vmpt_add_to_menu() 
{
	add_options_page('Vertical marquee post title', 'Vertical marquee post title', 'manage_options', __FILE__, 'vmpt_option' );
}

if (is_admin()) 
{
	add_action('admin_menu', 'vmpt_add_to_menu');
}

add_action("plugins_loaded", "vmpt_widget_init");
register_activation_hook(__FILE__, 'vmpt_install');
register_deactivation_hook(__FILE__, 'vmpt_deactivation');
add_action('init', 'vmpt_widget_init');
add_shortcode( 'vmpt', 'vmpt_shortcode' );
?>