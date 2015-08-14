<?php // CUSTOM SHORTCODES


// Video embed shortcodes
add_shortcode('youtube', 'custom_video_embed');
add_shortcode('vimeo', 'custom_video_embed');
function custom_video_embed($atts, $content = null, $tag){

    extract(shortcode_atts(array(
        'id'     => '',
        'width'  => '100%',
        'height' => '400px'
    ), $atts, 'video_embed'));

    if($id != ''){

        $attributes = array("frameborder=\"0\"");
        if(!empty($width))  $attributes[] = "width=\"{$width}\"";
        if(!empty($height)) $attributes[] = "height=\"{$height}\"";

        switch($tag){
            case "youtube":
                $src = "http://www.youtube-nocookie.com/embed/{$id}";
                $attributes[] = "allowfullscreen";
                break;
            case "vimeo":
                $src = "http://player.vimeo.com/video/{$id}?title=0&byline=0&portrait=0";
                $attributes[] = "webkitAllowFullScreen";
                $attributes[] = "mozallowfullscreen";
                $attributes[] = "allowFullScreen";
                break;
        }

        return "<div class=\"embed-wrapper\">\n" .
            "<iframe " . implode(" ", $attributes) . " src=\"{$src}\"></iframe>\n" .
        "</div>\n";
    }
    return;
}
