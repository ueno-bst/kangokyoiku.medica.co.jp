<?php

// TOPページ お役立ち情報
function get_top_leaning() {
  global $post;

  $args = array(
    'post_type' => 'post',
    'category' => 2,
    'posts_per_page' => 3,
    'post_status'    => 'publish'
  );
  $newslist = get_posts($args);

  if ($newslist) {

    $display_html = '
  <div class="main_width">
    <h3 class="center_border_ttl">家作りを学ぶ</h3>
    <div class="column3_block">
      <div class="main_width">
        <h2 class="border_ttl">お役立ち情報</h2>
        <ul>
';

    foreach( $newslist as $post ) {
      setup_postdata( $post );
      // サムネイル
      if (has_post_thumbnail()) {
        $thumb = get_the_post_thumbnail(get_the_ID(), 'top_images');
      } else {
        $thumb = '<img src="'.esc_url(home_url()).'/img/common/noimg.jpg" />';
      }

      $display_html .= '
            <li>
              <a href="'.get_permalink().'">
                '.$thumb.'
                <p>'.esc_html(get_the_title()).'</p>
              </a>
            </li>
';
    }

    $display_html .= '
    </ul>
  </div>
</div>
<!-- /.column3_block -->
';
    }

    wp_reset_postdata();
    return $display_html;
}
add_shortcode('get_top_leaning', 'get_top_leaning');

// TOPページ イベント情報
function get_top_eventinfo() {
  global $post;

  $display_html = '
      <h3 class="border_ttl">イベント情報</h3>
';

  $args = array(
    'post_type' => 'post',
    'category_name' => 'event',
    'posts_per_page' => 1,
    'post_status'    => 'publish'
  );

  $posts = get_posts( $args );
  if( $posts ) {
    foreach( $posts as $post ) {
      setup_postdata( $post );

      // サムネイル
      if (has_post_thumbnail()) {
        $thumb = get_the_post_thumbnail(get_the_ID(), 'top_images');
      } else {
        $thumb = '<img src="'.esc_url(home_url()).'/img/common/noimg.jpg" />';
      }

      $display_html .= '
          <div class="left">
            '.$thumb.'
          </div>
          <!-- /.left  -->
          <div class="right">
            <span class="date">'.get_the_time('Y.n.j').'UP</span>
            <h3 class="event_ttl"><a href="'.get_permalink().'">'.esc_html(get_the_title()).'</a></h3>
            <p class="event_conts">
              '.esc_html(get_the_excerpt()).'
            </p>
            <div class="btnbox">
              <p class="read_more_btn"><a href="'.get_permalink().'">イベント詳細を見る</a></p>
            </div>
          </div>
          <!-- /.right -->
';
    }
  } else {
    $display_html .= '
          <div>
            <p>COMMING SOON</p>
          </div>
          <!-- /.right -->
';
  }
  wp_reset_postdata(); //クエリのリセット
  return $display_html;

}
add_shortcode('get_top_eventinfo', 'get_top_eventinfo');

// TOPページ お客様の声
function get_top_voice() {
  global $post;

  $display_html = '';

  $args = array(
    'post_type' => 'post',
    'category' => 7,
    'posts_per_page' => 3,
    'post_status'    => 'publish'
  );
  $voicelist = get_posts($args);

  if ($voicelist) {
    $display_html .= '
    <section class="column3_block top_voice_block">
      <div class="main_width">
        <h2 class="center_border_ttl">お客様の声</h2>
        <ul>
';
    foreach( $voicelist as $post ) {
      setup_postdata( $post );

      // サムネイル
      if (has_post_thumbnail()) {
        $thumb = get_the_post_thumbnail(get_the_ID(), 'top_images');
      } else {
        $thumb = '<img src="'.esc_url(home_url()).'/img/common/noimg.jpg" width="475" height="392" />';
      }

      $display_html .= '
        <li>
          <a href="'.get_permalink().'">
            '.$thumb.'
            <span>'.get_the_time('Y.n.j').' 　UP</span>
            <p>'.esc_html(get_the_title()).'</p>
          </a>
        </li>
';

    }
    $display_html .= '
      </ul>
    </div>
    <!-- /.main_width -->
  </section>
';
  }
  wp_reset_postdata();

  return $display_html;
}
add_shortcode('get_top_voice', 'get_top_voice');

// TOPページ 施工事例
function get_top_works() {
  global $post;

  $display_html = '';

  $args = array(
    'post_type' => 'works',
    'posts_per_page' => 5,
    'post_status'    => 'publish'
  );
  $workslist = get_posts($args);

  if ($workslist) {
    $display_html .= '
      <section class="column4_block top_works_block">
        <div class="main_width">
          <h2 class="border_ttl">施工事例</h2>
          <p class="jirei_text">
            一級建築士事務所、工事店として、新築・リフォーム・店舗・建物修繕など、幅広い工事実績があります。
          </p>

          <ul>
';
    foreach( $workslist as $post ) {
      setup_postdata( $post );


      if (has_post_thumbnail()) {
        $thumb = '
          <a href="'.get_permalink().'">
            '.get_the_post_thumbnail(get_the_ID(), 'works_img_size').'
          </a>';
      } else {
        $thum = '
          <a href="'.get_permalink().'">
            <img src="'.esc_url(home_url()).'/img/common/no-image-user390240.jpg" alt="">
          </a>';
      }

      //term
      if ($terms = get_the_terms($post->ID, 'works_category')) {
          foreach ( $terms as $term ) {
              $term_slug = $term->slug;
              $term_name = $term->name;
          }
      }

      $display_html .= '
          <li>
            <div class="img">
              '.$thumb.'
            </div>
            <span class="'.$term_slug.'">'.$term_name.'</span>
            <p class="title">'.get_the_title().'</p>
            <p class="read_more_text"><a href="'.get_permalink().'">READ MORE</a></p>
          </li>
';
    }

    $display_html .= '
        </ul>
      <p class="read_more_border_arrow">
        <a href="'.esc_url(home_url()).'/works/">ユーリカワークスの<br>施工事例をもっとを見る</a>
      </p>
    </div>
    <!-- /.main_width -->
  </section>
  <!-- /.top_works -->
';
  }
  wp_reset_postdata();

  return $display_html;
}
add_shortcode('get_top_works', 'get_top_works');
