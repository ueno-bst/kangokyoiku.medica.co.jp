<?php
// common
require_once("functions/common.php");
// カスタムタイプ処理
require_once("functions/custom_post.php");
// フォーム関連処理
require_once("functions/mw_wp_form.php");
// フロントの表示処理
require_once("functions/get_front_posts.php");
// ブロックエディター処理
require_once("functions/block_editor.php");
// Wp-Member処理
require_once("functions/wp-member.php");
require_once 'functions/post_type_content.php';
require_once 'functions/taxonomy_product.php';
//バージョン情報を非表示
remove_action('wp_head', 'wp_generator');
// EditURIを非表示にする
remove_action('wp_head', 'rsd_link');
// wlwmanifestを非表示にする
remove_action('wp_head', 'wlwmanifest_link');
//絵文字のスクリプトを停止
remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
remove_action( 'wp_print_styles', 'print_emoji_styles' );


/* アイキャッチ画像を表示 */
function add_thumbnail_size() {
    add_theme_support( 'post-thumbnails' );
    add_image_size( 'thumbnail', 150, 150, true );
    add_image_size( 'thumbnail600400', 600, 400, true );
    // add_image_size( 'product_case', 500, 320, true );
    // add_image_size( 'works_thumbimg_size', 366, 234, true );
    // add_image_size( 'works_archveimg_size', 716, 456, true );
    // add_image_size( 'top_images', 475, 392, true );
}
add_action( 'after_setup_theme', 'add_thumbnail_size' );

//固定ページで使うショートコード
function shortcode_url() {
 return get_bloginfo('url');
}
add_shortcode('site_url', 'shortcode_url');

//画像のみpタグで囲わない
function remove_p_on_images($content){
    return preg_replace('/<p>(\s*)(<img .* \/>)(\s*)<\/p>/iU', '\2', $content);
}
add_filter('the_content', 'remove_p_on_images');

//自動生成するpタグやbrタグを固定ページだけ取り除く
remove_filter('the_content','wpautop');
add_filter('the_content','custom_content');
function custom_content($content){
if(get_post_type()=='page')
    return $content; //
else
 return wpautop($content);
}

//ログイン画面の画像変更
function login_logo_image() {
    echo '<style type="text/css">
            #login h1 a {
                background: url(' . site_url() . '/img/common/logo.svg) no-repeat !important; width:285px; height:59px;
            }
    </style>';
}
add_action('login_head', 'login_logo_image');

//現在のURLを取得する
function get_current_link() {
	return (is_ssl() ? 'https' : 'http') . '://' . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"];
}


//固定ページでビジュアルエディタを非表示にする
function disable_visual_editor_in_page() {
        global $typenow;
        if( in_array( $typenow, array( 'page' ,'mw-wp-form' ) ) ){
                add_filter('user_can_richedit', 'disable_visual_editor_filter');
        }
}
function disable_visual_editor_filter(){
        return false;
}
add_action('load-post.php', 'disable_visual_editor_in_page');
add_action('load-post-new.php', 'disable_visual_editor_in_page');


//categoryのリストを表示(HTMLカスタマイズ) ※スラッグ名付き
function categories_label() {
    $cats = get_the_category();
    foreach ((array)$cats as $cat){
        echo '<li><a href="'.get_category_link($cat->term_id).'" ';
        echo 'class="'.esc_attr($cat->slug).'">';
        echo esc_html($cat->name);
        echo '</a></li>';
    }
}


//固定ページだけグーテンバーグじゃなくする
add_filter( 'use_block_editor_for_post_type', 'hide_block_editor', 10, 10 );
function hide_block_editor( $use_block_editor, $post_type ) {
  if ( $post_type === 'page' ) return false;
  return $use_block_editor;
}

//「wp-embed-template.min.css」と「embed-content.php」を編集可能に
function my_embed_style() {
    wp_enqueue_style('wp-embed-template-org', get_stylesheet_directory_uri() . '/wp-embed-template.min.css');
}
add_filter('embed_head', 'my_embed_style');


//blogcardにclassを付与
function custom_blogcard_oembed($code){
  $url = esc_url(home_url('/'));

  if(strpos($code, $url) !== false || strpos($code, $url) !== false){
    $html = preg_replace("@src=(['\"])?([^'\">\s]*)@", "src=$1$2&showinfo=0&rel=0", $code);
    $html = preg_replace('/ width="\d+"/', '', $html);
    $html = preg_replace('/ height="\d+"/', '', $html);
    $html = '<div class="blogcard">' . $html . '</div>';

    return $html;
  }
  return $code;
}
add_filter('embed_handler_html', 'custom_blogcard_oembed');
add_filter('embed_oembed_html', 'custom_blogcard_oembed');


//ACFのプレビューが表示されない回避コード
//【注意】ACFをインストールして有効化しないと、エラーが出るので、使わない時は消す
function fix_post_id_on_preview($null, $post_id) {
    if (is_preview()) {
        return get_the_ID();
    }
    else {
        $acf_post_id = isset($post_id->ID) ? $post_id->ID : $post_id;

        if (!empty($acf_post_id)) {
            return $acf_post_id;
        }
        else {
            return $null;
        }
    }
}
add_filter( 'acf/pre_load_post_id', 'fix_post_id_on_preview', 10, 2 );


/*ショートコードを使う*/
/*[mycode file='xxxx']*/
function Include_my_php($params = array()) {
    extract(shortcode_atts(array(
        'file' => 'default'
    ), $params));
    ob_start();
    include(get_theme_root() . '/' . get_template() . "/template-parts/$file.php");
    return ob_get_clean();
}
add_shortcode('mycode', 'Include_my_php');


//検索結果＜全角スペース対応／カスタム投稿を含める・固定ページ除外＞
function SearchFilter($query) {
    if ( !is_admin() && $query->is_main_query() && $query->is_search() ) { //全角スペースでも検索可能にする
     $s = $query->get( 's' );
     $s = str_replace('　',' ', $s );
     $query->set( 's', $s );
    }
    if ( !is_admin() && $query->is_main_query() && $query->is_search() ) { //検索する投稿タイプ
     $query->set( 'post_type', array('post') );
    }
 }
 add_action( 'pre_get_posts','SearchFilter' );

 //検索対象＜カテゴリー・タグ・カスタムタクソノミー・カスタムフィールド・ユーザー表示名から＞
 function custom_search($search, $wp_query) {
     global $wpdb;

     if (!$wp_query->is_search)
             return $search;
     if (!isset($wp_query->query_vars))
             return $search;

     $search_words = explode(' ', isset($wp_query->query_vars['s']) ? $wp_query->query_vars['s'] : '');
     if ( count($search_words) > 0 ) {
             $search = '';
             /*$search .= "AND post_type = 'post'";*/
             foreach ( $search_words as $word ) {
                     if ( !empty($word) ) {
                             $search_word = '%' . esc_sql( $word ) . '%';
                             $search .= " AND (
                                  {$wpdb->posts}.post_title LIKE '{$search_word}'
                                 OR {$wpdb->posts}.post_content LIKE '{$search_word}'
            OR {$wpdb->posts}.ID IN (
              SELECT distinct r.object_id
              FROM {$wpdb->term_relationships} AS r
              INNER JOIN {$wpdb->term_taxonomy} AS tt ON r.term_taxonomy_id = tt.term_taxonomy_id
              INNER JOIN {$wpdb->terms} AS t ON tt.term_id = t.term_id
              WHERE t.name LIKE '{$search_word}'
            OR t.slug LIKE '{$search_word}'
            OR tt.description LIKE '{$search_word}'
            )
                                 OR {$wpdb->posts}.ID IN (
                                 SELECT distinct post_id
                                 FROM {$wpdb->postmeta}
                                 WHERE meta_value LIKE '{$search_word}'
                                 )
            OR {$wpdb->posts}.post_author IN (
              SELECT distinct ID
              FROM {$wpdb->users}
              WHERE display_name LIKE '{$search_word}'
              )
                             ) ";

                     }
             }
            //  $search = preg_replace('/\A  AND /', '', $search);
            //  $search = 'AND' . '(' . str_replace(')  AND ', ')  OR ', $search) . ')';
     }

     return $search;
 }
 add_filter('posts_search','custom_search', 10, 2);

