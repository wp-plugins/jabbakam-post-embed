<?php 
/*
Plugin Name: Jabbakam embedder for Wordpress
Plugin URI: http://wordpress.org/extend/plugins/jabbakam-post-embed/
Description: Converts any Jabbakam shared video URLs into embedded Flowplayer instances.
Version: 1.0
Author: Jabbakam
Author URI: http://www.jabbakam.com
License: GPLv3

Copyright (c) 2010 Jabbakam Limited

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see http://www.gnu.org/licenses/gpl.html.
*/

//apply filter function to post content before it is shown to the browser
add_filter( "the_content", "jabbakam_parse" );

//defines and returns embed code based on shared url given
function jabbakam_write_embed($instance,$jabbakam_shared_url)
{
	//get description, title, and dimensions from meta headers at shared url
	$headers = get_meta_tags($jabbakam_shared_url);
	$headers['video_width'] = $headers['video_width'] * 0.75;
	$headers['video_height'] = $headers['video_height'] * 0.75;
	
	//define flowplayer embed code
	$jabbakam_embed = 	'<div class="jabbakam_container">';
	$jabbakam_embed .=	'<object width="'.$headers['video_width'].'" height="'.$headers['video_height'].'" ';
	$jabbakam_embed .=	'id="jabbakam_player_'.$instance.'" name="jabbakam_player_'.$instance.'" ';
	$jabbakam_embed .=	'data="'.$headers['flowplayer_location'].'" type="application/x-shockwave-flash"> ';
	$jabbakam_embed .=	'<param name="movie" value="'.$headers['flowplayer_location'].'" /> ';
	$jabbakam_embed .=	'<param name="allowfullscreen" value="true" /><param name="allowscriptaccess" value="always" /> ';
	$jabbakam_embed .=	'<param name="flashvars" value="config='.$headers['flowplayer_config'].'" /></object>';
	$jabbakam_embed .= 	'<p><strong>'.$headers['description'].'</strong></p>';
	$jabbakam_embed .= 	'</div>';
	
	//return code
	return $jabbakam_embed;	
}



//searches in content and triggers the writing of embed code where jabbakam links found
function jabbakam_parse($content) 
{	
	//Look for jabbakam standalone player links in post content, (containing 'http://www.jabbakam.com/play/')
	$needle = "http://www.jabbakam.com/s/";		
				
	//only process html if jabbakam links found			
	if (strstr($content, $needle)){		
				
		//explode content string into array of words and tags
		$parts = jabbakam_explode_tags('', $content);
		
		$html = ""; 
		
		foreach ($parts as $key=>$value)
		{
			$parts_inner = explode(" ", $value); 	
			
			foreach ($parts_inner as $key_inner=>$value_inner)
			{
				if (strstr($value_inner, $needle))
				{
					$parts_inner[$key_inner] = jabbakam_write_embed($key_inner,$value_inner);
				} 			
			}	
			
			$parts_inner = implode(" ", $parts_inner);
			
			$html .= $parts_inner;
		}
		
		$content = $html;	
	} 
	
	return $content;
	
}



//explodes html into tags and their contents
function jabbakam_explode_tags($chr, $str) 
{
	for ($i=0, $j=0; $i < strlen($str); $i++) 
	{
		if ($str{$i} == $chr) 
		{
		   while ($str{$i+1} == $chr)
		   $i++;
		   $j++;
		   continue;
		}
		if ($str{$i} == "<") 
		{
		   if (strlen($res[$j]) > 0)
		   $j++;
		   $pos = strpos($str, ">", $i);
		   $res[$j] .= substr($str, $i, $pos - $i+1);
		   $i += ($pos - $i);
		   $j++;
		   continue;
		}
		if ((($str{$i} == "\n") || ($str{$i} == "\r")) && (strlen($res[$j]) == 0))
		   continue;
		$res[$j] .= $str{$i};
	}
	return $res;
}

?>