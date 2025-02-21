<?php

add_action('init', function () {
    $label = 'コンテンツ';

    register_post_type('article', [
        'label' => $label,
        'labels' => [
            'name' => $label,
            'singular_name' => "{$label}一覧",
            'add_new' => "{$label}を追加",
            'add_new_item' => "新しい{$label}を追加",
            'edit_item' => "{$label}を編集",
            'new_item' => "新しい{$label}",
            'view_item' => "{$label}を編集",
            'search_items' => "{$label}を探す",
            'not_found' => "{$label}はありません",
            'not_found_in_trash' => "ゴミ箱に{$label}はありません",
            'parent_item_colon' => ""
        ],
        'public' => true,
        'public_queryable' => true,
        'show_ui' => true,
        'query_var' => 'artcl',
        'rewrite' => true,
        'capability_type' => 'post',
        'hierarchical' => false,
        'menu_position' => 4,
        'has_archive' => true,
        'supports' => ['title', 'editor', 'thumbnail'],
        'show_in_rest' => true,
        'taxonomies' => ['product'],
    ]);
}, 15);