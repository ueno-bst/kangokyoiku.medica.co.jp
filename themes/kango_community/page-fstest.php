<?php

// get_user_favorites_list($user_id, $site_id, $include_links, $filters, $include_button, $include_thumbnails = false, $thumbnail_size = 'thumbnail', $include_excerpt = false)

$favorite_post_ids = get_user_favorites();
var_dump($favorite_post_ids);

// wp_query()