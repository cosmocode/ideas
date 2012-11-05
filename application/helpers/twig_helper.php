<?php

function pagination($uri, $limit, $total) {
    $ci =& get_instance();
    $ci->load->library('pagination');

    $config = array(
        'base_url'        => $uri,
        'total_rows'      => $total,
        'per_page'        => $limit,
        'uri_segment'     => $ci->uri->total_segments(),

        'next_tag_open'   => '<li>',
        'next_tag_close'  => '</li>',
        'prev_tag_open'   => '<li>',
        'prev_tag_close'  => '</li>',
        'cur_tag_open'    => '<li class="disabled"><a href="#">',
        'cur_tag_close'   => '</a></li>',
        'num_tag_open'    => '<li>',
        'num_tag_close'   => '</li>',

    );

    $ci->pagination->initialize($config);
    $links = $ci->pagination->create_links();

    return "<div class=\"pagination\"><ul>$links</ul></div>";
}

/**
 * Shorten a given text to the wanted length in chars
 *
 * Also registered as Twig filter
 *
 * @param string    $text
 * @param int       $length
 * @return string
 */
function shorten($text, $length = 80) {
    $len = mb_strlen($text, 'UTF-8');
    if($len < $length) return $text;

    $text = mb_substr($text, 0, $length, 'UTF-8');

    return $text.'…';
}

/**
 * Make plain text more nice
 *
 * Also registered as Twig filter
 *
 * @link http://daringfireball.net/2009/11/liberal_regex_for_matching_urls
 * @param string $text
 * @return mixed|string
 */
function prettytext($text) {
    // automatically recognize links
    $pattern = '#\b(([\w-]+://?|www[.])[^\s()<>]+(?:\([\w\d]+\)|([^[:punct:]\s]|/)))#';
    $text    = preg_replace_callback($pattern, 'auto_link_callback', $text);

    // newlines
    $text = nl2br($text);

    return $text;
}

/**
 * Replace links in text with html links
 *
 * @param $matches
 * @return string
 */
function auto_link_callback($matches) {
    $url       = array_shift($matches);
    $url_parts = parse_url($url);
    $text      = $url_parts['host'];
    if(isset($url_parts['path'])) $text .= $url_parts['path'];
    $text = preg_replace("/^www./", "", $text);

    $last = -(strlen(strrchr($text, "/"))) + 1;
    if($last < 0) {
        $text = substr($text, 0, $last)."&hellip;";
    }

    return sprintf('<a href="%s" target="_blank">%s</a>', $url, $text);
}