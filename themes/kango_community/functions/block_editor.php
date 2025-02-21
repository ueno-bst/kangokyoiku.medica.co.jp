<?php
//フォントサイズを制御
add_theme_support( 'editor-font-sizes', array(
  array(
    'name' => '極小',
    'size' => 10,
    'slug' => 'x-small'
  ),
  array(
    'name' => '小',
    'size' => 13,
    'slug' => 'small'
  ),
  array(
    'name' => '標準',
    'size' => 15,
    'slug' => 'regular'
  ),
  array(
    'name' => '大',
    'size' => 24,
    'slug' => 'large'
  ),
  array(
    'name' => '特大',
    'size' => 36,
    'slug' => 'x-large'
  ),
  array(
    'name' => '超特大',
    'size' => 50,
    'slug' => 'huge'
  )
) );

add_theme_support('editor-color-palette', array(
  array(
    'name' => '黒',
    'slug' => 'black',
    'color' => '#000',
  ) ,
  array(
    'name' => 'シアンブルーグレー',
    'slug' => 'cyan-bluish-gray',
    'color' => '#abb8c3',
  ) ,
  array(
    'name' => '白',
    'slug' => 'white',
    'color' => '#fff',
  ) ,
  array(
    'name' => '淡いピンク',
    'slug' => 'pale-pink',
    'color' => '#f78da7',
  ) ,
  array(
    'name' => '鮮やかな赤',
    'slug' => 'vivid-red',
    'color' => '#cf2e2e',
  ) ,
  array(
    'name' => '明るく鮮やかなオレンジ',
    'slug' => 'luminous-vivid-orange',
    'color' => '#ff6900',
  ) ,
  array(
    'name' => '明るく鮮やかな琥珀',
    'slug' => 'luminous-vivid-amber',
    'color' => '#fcb900',
  ) ,
  array(
    'name' => '薄いグリーンシアン',
    'slug' => 'light-green-cyan',
    'color' => '#7bdcb5',
  ) ,
  array(
    'name' => '鮮やかなグリーンシアン',
    'slug' => 'vivid-green-cyan',
    'color' => '#00d084',
  ) ,
  array(
    'name' => '淡いシアンブルー',
    'slug' => 'pale-cyan-blue',
    'color' => '#8ed1fc',
  ) ,
  array(
    'name' => '鮮やかなシアンブルー',
    'slug' => 'vivid-cyan-blue',
    'color' => '#0693e3',
  ) ,
  array(
    'name' => '鮮やかなパープル',
    'slug' => 'vivid-purple',
    'color' => '#9b51e0',
  ) ,
  array(
    'name' => 'テーマカラー青',
    'slug' => 'original-blue',
    'color' => '#3F559F',
  ) ,
  array(
    'name' => 'テーマカラーピンク',
    'slug' => 'original-pink',
    'color' => '#DF8390',
  )
) );

//iframeのレスポンシブ対応
function wrap_iframe_in_div($the_content) {
  if ( is_singular() ) {
    $the_content = preg_replace('/<iframe/i', '<div class="content_iframe"><iframe', $the_content);
    $the_content = preg_replace('/<\/iframe>/i', '</iframe></div>', $the_content);
  }
  return $the_content;
}
add_filter('the_content','wrap_iframe_in_div');

//Gutenberg 管理画面css
function gutenberg_mysettings() {
  $editor_style_url = esc_url(site_url('/css/gutenberg_editor.css'));
  wp_enqueue_style( 'theme-editor-style', $editor_style_url );
}
add_action( 'enqueue_block_editor_assets', 'gutenberg_mysettings' );
