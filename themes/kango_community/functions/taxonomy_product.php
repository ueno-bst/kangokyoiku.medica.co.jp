<?php

add_action('init', function () {
    register_taxonomy('product', '採用商品', [
        'labels' => [
            'name' => '採用商品',
        ],
        'public' => false,
        'publicly_queryable' => true,
        'hierarchical' => true,
        'show_ui' => true,
        'show_in_nav_menus' => true,
        'show_in_rest' => false,
        'show_in_quick_edit' => true,
        'show_admin_column' => true,
        'sort' => false,
    ]);

}, 10);