<?php

//カスタム投稿-news
function news_custom_post_type()
{
  $labels = array(
    'name' => _x('お知らせ', 'post type general name'),
    'singular_name' => _x('お知らせ一覧', 'post type singular name'),
    'add_new' => _x('お知らせを追加', 'news'),
    'add_new_item' => __('新しいお知らせを追加'),
    'edit_item' => __('お知らせを編集'),
    'new_item' => __('新しいお知らせ'),
    'view_item' => __('お知らせを編集'),
    'search_items' => __('お知らせを探す'),
    'not_found' => __('お知らせはありません'),
    'not_found_in_trash' => __('ゴミ箱にお知らせはありません'),
    'parent_item_colon' => ''
  );
  $args = array(
    'labels' => $labels,
    'public' => true,
    'publicly_queryable' => true,
    'show_ui' => true,
    'query_var' => true,
    'rewrite' => true,
    'capability_type' => 'post',
    'hierarchical' => false,
    'menu_position' => 5,
    'has_archive' => true,
    'supports' => array('title','editor','thumbnail'),
    'show_in_rest' => true,
  );
  flush_rewrite_rules( false );
  register_post_type('news',$args);
// カスタムタクソノミーを作成
//カテゴリータイプ
  // $args = array(
  //   'label' => 'お知らせカテゴリー',
  //   'public' => true,
  //   'show_ui' => true,
  //   'hierarchical' => true
  // );
  // register_taxonomy('news_category','news',$args);
}
add_action('init', 'news_custom_post_type');


//記事のページごとの表示件数の制御
function my_pre_get_posts( $query ) {
  if(is_admin() || ! $query -> is_main_query()) return;

//  if($query -> is_front_page()) { //フロントページ
//    $query -> set('posts_per_page',10); //10件
//  }
 if($query->is_home()){ // トップページ
   $query->set( 'posts_per_page',30);
 }
  if($query->is_month()){ // 月別アーカイブ
    $query->set('posts_per_page',30);
  }
if($query->is_year()){ //年別アーカイブ
  $query->set('posts_per_page',30);
}
if($query->is_author()){ // 作成者アーカイブ
  $query->set('posts_per_page',30);
}
if($query->is_category()){ // カテゴリーアーカイブ
  $query->set('posts_per_page',30);
}
  //productsというカスタム投稿タイプのアーカイブページ
  if($query -> is_post_type_archive('news')){
    $query->set('posts_per_page', 30);
  }
  //newscatというタクソノミーの一覧ページ
  if ($query -> is_tax('news_category')) {
    $query->set('posts_per_page', 30); //10件
  //    $query -> set('order', 'DESC'); //降順
  //    $query -> set('orderby', 'date'); //日
  }
}
add_action('pre_get_posts','my_pre_get_posts');



//タームのリストを表示(HTMLカスタマイズ) ※スラッグ名付き
function terms_label() {
    $terms = get_the_terms($post->ID, 'news_category');
      echo '<ul class="category_list">';
        foreach  ((array)$terms as $term ) {
          echo '<li><a href="'.get_term_link($term->term_id).'" ';
          echo 'class="'.esc_attr($term->slug).'">';
          echo esc_html($term->name);
          echo '</a></li>';
        }
      echo '</ul>';
}


//ターム名表示（リンクなし）
function terms_name() {
    $terms = get_the_terms($post->ID, 'news_category');
    foreach  ((array)$terms as $term ) {
        echo '<p class="ca_band beautiful-skin">';
        echo esc_html($term->name);
        echo '</p>';
    }
}


//タクソノミー未選択時に特定のタームを選択させる※構築時に設定
// function add_defaultcategory_automatically($post_ID) {
//   global $wpdb;
//   $curTerm = wp_get_object_terms($post_ID, 'タクソノミー名');
//   if (0 == count($curTerm)) {
//     $defaultTerm= array(5);//選択させたいタームID
//     wp_set_object_terms($post_ID, $defaultTerm, 'タクソノミー名');
//   }
// }
// add_action('publish_カスタム投稿名', 'add_defaultcategory_automatically');


// カスタム投稿の編集画面に『投稿者』を表示
add_action('admin_menu', 'myplugin_add_custom_box_news');
function myplugin_add_custom_box_news()
{
  if (function_exists('add_meta_box')) {
      add_meta_box('myplugin_sectionid', __('投稿者', 'myplugin_textdomain'), 'post_author_meta_box', 'news', 'advanced');
  }
}
function manage_news_columns ($columns) {
  $columns['author'] = '投稿者';
  return $columns;
}
function add_news_column ($column, $post_id) {
  if ('author' == $column) {
      $value = get_the_term_list($post_id, 'author');
      echo attribute_escape($value);
  }
}
add_filter('manage_posts_columns', 'manage_news_columns');
add_action('manage_posts_custom_column', 'add_news_column', 10, 2);


///記事一覧にアイキャッチ画像を表示させるコード
// カラムタイトルにフック
add_filter( 'manage_posts_columns', 'add_thumb_columns' );
// 各カラム行にフック
add_action( 'manage_posts_custom_column', 'add_thumb_column', 10, 2 );

/**
 * 投稿一覧の行タイトルに thumb を配列キーとした dashicon を追加
 *
 * @param $columns
 *
 * @return mixed
 */
function add_thumb_columns( $columns ) {
	// サムネイル用のスタイル
	echo '<style>.column-thumb{width:100px;}</style>';

	// サムネイルをカラム先頭に追加するため array_reverse で挟み込んで追加
	$columns          = array_reverse( $columns, true );
	$columns['thumb'] = '<div class="dashicons dashicons-format-image"></div>';
	$columns          = array_reverse( $columns, true );

	return $columns;
}

/* 投稿一覧の各行にサムネイル出力
 *
 * @param $column
 * @param $post_id
 */
function add_thumb_column( $column, $post_id ) {
	switch ( $column ) {
		// 行のキーが thumb なら アイキャッチ を出力
		case 'thumb':
			// アイキャッチがある場合
			if ( $thumb = get_the_post_thumbnail( $post_id, array( 100, 100 ) ) ) {
				// 編集権限、ゴミ箱内かどうかの判別用変数
				$user_can_edit = current_user_can( 'edit_post', $post_id );
				$is_trash      = isset( $_REQUEST['status'] ) && 'trash' == $_REQUEST['status'];
				// 編集権限があり、ゴミ箱でないなら画像をリンクつきに
				if ( ! $is_trash || $user_can_edit ) {
					$thumb = sprintf( '<a href="%s" title="%s">%s</a>',
						get_edit_post_link( $post_id, true ),
						esc_attr( sprintf( __( 'Edit &#8220;%s&#8221;', 'default' ), _draft_or_post_title() ) ),
						$thumb );
				}
				// 出力
				echo $thumb;
			}
			break;
		default:
			break;
	}
}


//投稿の表示をテーマに
function change_post_menu_label() {
  global $menu;
  global $submenu;
    $menu[5][0] = 'テーマ';
    $submenu['edit.php'][5][0] = 'テーマ';
    $submenu['edit.php'][10][0] = 'テーマを追加';
    $submenu['edit.php'][16][0] = 'テーマ用タグ';
}
function change_post_object_label() {
  global $wp_post_types;
    $labels = &$wp_post_types['post']->labels;
    $labels->name = 'テーマ';
    $labels->singular_name = 'テーマ';
    $labels->add_new = _x('テーマを追加', 'テーマ');
    $labels->add_new_item = 'テーマの新規追加';
    $labels->edit_item = 'テーマの編集';
    $labels->new_item = '新規のテーマ';
    $labels->view_item = 'テーマを表示';
    $labels->search_items = 'テーマを検索';
    $labels->not_found = '記事が見つかりませんでした';
    $labels->not_found_in_trash = 'ゴミ箱に記事は見つかりませんでした';
}
add_action( 'init', 'change_post_object_label' );
add_action( 'admin_menu', 'change_post_menu_label' );

//投稿のタグを非表示
function hide_tag_from_menu() {
    global $wp_taxonomies;
        foreach ( $wp_taxonomies['post_tag']->object_type as $i => $object_type ) {
            if ( $object_type == 'post' ) {
                unset( $wp_taxonomies['post_tag']->object_type[$i] );
            }
    }
    return true;
}
add_action( 'init', 'hide_tag_from_menu' );
