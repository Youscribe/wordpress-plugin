<?php
/*
    Plugin Name: YouScribe
    Description: Embed YouScribe publications inside a blog post
    Version: 1.0.0
    Author: YouScribe
*/

/*  Copyright 2013 YouScribe  (email : contact@youscribe.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

function youscribe_parse( $content )
{
	// Parse post and check for youscribe embed
	$content = preg_replace_callback("/\[youscribe ([^]]*)\]/i", "youscribe_render", $content);

	return $content;
}

function youscribe_render( $tags )
{
	// Parsing attributes
	if ( !preg_match_all('/([^= ]+)=([\S]*)/i', $tags[1], $matches) ) return '';

	$attributes = array();

	$count = count($matches[1]);

	for ( $i = 0 ; $i < $count ; $i++ )
	{
		$attributes[$matches[1][$i]] = $matches[2][$i];
	}

	// Validation
	if ( empty($attributes['id']) ) return '';
	if ( empty($attributes['did']) ) return '';
	if ( empty($attributes['mode']) ) $attributes['mode'] = 'embed';
	if ( empty($attributes['page']) ) $attributes['page'] = 1;
	if ( empty($attributes['width']) ) $attributes['width'] = '100%';
	
	// Language
	$language = preg_match('/$([a-z]+)/i', get_bloginfo('language'));

	if (empty($language) || !['fr', 'es', 'en'].contains($language))
	{
		$language = 'en';
	}

	// Create links
	$base_url = 'http://' + ($language == 'fr' ? 'www' : $language) + '.youscribe.com';
	$home_url = $base_url;
	$user_url = $base_url + '/' + $attributes['user_name'];

	// Iframe querystring
	$iqs  = 'productId=' . $attributes['id'];
	$iqs  = '&amp;documentId=' . $attributes['did'];
	$iqs .= '&amp;width=' . $attributes['width'];
	$iqs .= '&amp;height=' . $attributes['height'];
	$iqs .= '&amp;startPage=' . $attributes['page'];
	$iqs .= '&amp;displayMode=' . $attributes['mode'];
	if (!empty($attributes['token']))
		$iqs .= '&amp;token=' . $attributes['token'];
	$iqs .= '&amp;fullscreen=0';

	$publish_by = 'publié par';
	if ($language == 'en')
		$publish_by = 'publish by';
	else if ($language == 'es')
		$publish_by = 'publish by';

	// Generate HTML code
	$html = '<div style="overflow: hidden;position: relative;">'
	$html .= '<iframe src="http://' . ($language == 'fr' ? 'www' : $language) . '.youscribe.com/BookReader/IframeEmbed?' . $iqs .  '" allowfullscreen  webkitallowfullscreen mozallowfullscreen frameborder="0" scrolling="no"  '
		$html .= ' width="' . $attributes['width'] . '" height="' . $attributes['height'] . '" marginwidth="0" marginheight="0" style="overflow:hidden;border: solid 1px #BCBDBC;"></iframe>'
	$html .= '</div><div style="margin-bottom:5px">'
	$html .= '<a href="' . $attributes["url"] . '" title="' + $attributes["title"] + '" target="_blank">' . $attributes["title"] . '</a>'
	$html .= ' ' . $publish_by  ' <a href="' . $user_url . '" target="_blank">' . $attributes['user_name'] . '</a></div>'

	return $html;
}

add_filter('the_content', 'youscribe_parse');

?>