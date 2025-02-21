<?php
/*
Template Name: TOPページテンプレート
*/
?>
<?php get_header(); ?>
<?php if( is_user_logged_in() ) : //ログインしている時　?>
<?php //公開・非公開の変数
  global $current_user;
  $school_show_hidden =  $current_user->school_show_hidden;
  $position_show_hidden =  $current_user->position_show_hidden;
  $area_show_hidden =  $current_user->area_show_hidden;
?>

  <main>
    <div class="bg_g">
      <div class="main_section">
        <div class="main_width">
          <div class="account_block">
            <div class="account_box">
              <div class="flex">
                <div class="img">
                  <?php
                    $user_data = wp_get_current_user();
                    $user_idnm = $user_data->ID;
                  ?>
                  <?php if( $img = get_the_author_meta( 'user_prof_img', $user_idnm )): ?>
                    <?php	echo do_shortcode('[wpmem_field user_prof_img]'); ?>
                  <?php else: ?>
                    <img src="<?php echo esc_url(site_url('/')); ?>img/common/user-no-image.jpg" alt="Noimage">
                  <?php endif;?>
                </div>
                <!-- /.img -->
                <div class="info">
                  <b class="name">ようこそ <span><?php	echo do_shortcode('[wpmem_field user_nickname]'); ?></span>先生!!</b>
                  <div class="info_sub">

                    <?php if($school_show_hidden == '公開'):?>
                      <!-- ▼ fsta ikawa update -->
                      <?php /* <span class="school"><?php	echo do_shortcode('[wpmem_field user_school_name_select]'); ?></span> */ ?>
                      <span class="school"><?php	echo do_shortcode('[show_school_name]'); ?></span>
                      <!-- ▲ fsta ikawa update -->
                    <?php elseif($school_show_hidden == '非公開'):?>
                      <span class="school">学校名非公開</span>
                    <?php endif ?>
                    <!-- <span class="valid">2024年5月末まで有効</span> -->
                    <?php if($position_show_hidden == '公開'):?>
                      <span class="position"><?php	echo do_shortcode('[wpmem_field user_position]'); ?></span>
                    <?php elseif($position_show_hidden == '非公開'):?>
                      <span class="position">肩書き非公開</span>
                    <?php endif ?>
                    <?php if($area_show_hidden == '公開'):?>
                      <span class="position"><?php	echo do_shortcode('[wpmem_field user_area]'); ?></span>
                    <?php elseif($area_show_hidden == '非公開'):?>
                      <span class="position">担当領域非公開</span>
                    <?php endif ?>
                    <!-- ▼ fsta ikawa add -->
                    <span class="valid"><?php	echo do_shortcode('[wpmem_field user_expired_date]'); ?> まで有効</span>
                    <!-- ▲ fsta ikawa add -->


                  </div>
                  <div class="btn_box arrow_btn">
                    <a href="<?php echo esc_url(home_url('/')); ?>user_edit">会員情報・編集</a>
                  </div>
                  <!-- /.btn_box -->
                </div>
                <!-- /.info -->
              </div>
              <!-- /.flex -->
            </div>
            <!-- /.account_box -->
          </div>
          <!-- /.account_block -->
      <?php wp_reset_postdata(); ?>
      <?php
        $args = array(
            'post_type' => 'news',
            'posts_per_page' => 3,
            'post_status' => 'publish',
              'meta_query'    => array(
                    'relation'      => 'OR',
                    array(
                        'key'       => 'select_user',
                        // 'value'     => '',
                        'compare' => 'NOT EXISTS'
                    ),
                    array(
                        'key'       => 'select_user',
                        'value'     => NULL,
                        'compare' => 'IS'
                    ),
                    array(
                        'key'       => 'select_user',
                        // 'value'     => $current_user->ID,
                        'value' => '"' . $current_user->ID . '"',
                        'compare'   => 'LIKE',
                    ),
              ),
        );
        $the_query = new WP_Query( $args );
      ?>
      <?php if ( $the_query->have_posts() ) : ?>
          <section class="news_block">
            <div class="bg_w_shadow">
              <div class="block_header">
                <h2 class="ttl">お知らせ</h2>
                <div class="btn_box arrow_btn pc_only">
                  <a href="<?php echo esc_url(home_url('/')); ?>news-list">お知らせ一覧へ</a>
                </div>
              </div>
              <!-- /.block_header -->
              <div class="news_list">

                <?php while ( $the_query->have_posts() ) : $the_query->the_post(); ?>

                  <a href="<?php echo get_permalink(); ?>">
                    <p class="date"><?php the_time('Y年n月j日'); ?></p>
                    <p class="news_ttl"><?php the_title(); ?></p>
                  </a>

                <?php endwhile; ?>
              </div>
              <!-- /.news_list -->
            <div class="btn_box border_arrow_btn sp_only">
              <a href="<?php echo esc_url(home_url('/')); ?>news-list">お知らせ一覧へ</a>
            </div>
            <!-- /.btn_box border_arrow_btn sp_only -->

            </div>
            <!-- /.bg_w_shadow -->
          </section>
          <!-- /.news_block -->
    <?php endif; ?>
    <?php wp_reset_postdata(); ?>

    <?php
      $args = array(
          'post_type' => 'post',
          'posts_per_page' => 9
      );
      $the_query = new WP_Query( $args );
    ?>
    <?php if ( $the_query->have_posts() ) : ?>
          <section class="theme_block">
            <div class="bg_w_shadow">

              <div class="block_header">
                <h2 class="ttl">新着テーマ</h2>
                <div class="btn_box arrow_btn pc_only">
                  <a href="<?php echo esc_url(home_url('/')); ?>thema">テーマ一覧へ</a>
                </div>
              </div>
              <!-- /.block_header -->

              <ul class="theme_list">
                <?php while ( $the_query->have_posts() ) : $the_query->the_post(); ?>
                  <?php
                    $cat = get_the_category();
                    $cat = $cat[0]->cat_name;
                  ?>
                  <li class="item">
                    <a href="<?php the_permalink(); ?>">
                      <div class="img left">
                        <?php if(has_post_thumbnail()): ?>
                          <?php the_post_thumbnail('thumbnail600400'); ?>
                        <?php else: ?>
                          <img src="<?php echo esc_url(site_url('/')); ?>img/common/no-image600400.jpg" alt="Noimage"/>
                        <?php endif; ?>
                        <span class="category pc_only"><?php echo esc_html($cat); ?></span>
                      </div>
                      <!-- /.img left -->
                      <div class="right">
                        <div class="theme_info">
                          <p class="time"><?php the_time('Y年n月j日'); ?></p>
                          <p class="category sp_only"><?php echo esc_html($cat); ?></p>
                        </div>
                        <p class="theme_ttl"><?php the_title(); ?></p>
                        <div class="author">
                          <?php
                          $user_idnm = new WP_User( get_the_author_meta( 'ID' ) );
                          $user_id = get_the_author_meta('ID');
                          ?>
                          <?php if( $img = get_the_author_meta( 'user_prof_img', $user_id )): ?>
                            <?php echo get_avatar(get_the_author_meta( 'ID' ), 150 ); ?>
                          <?php else: ?>
                            <img src="<?php echo esc_url(site_url('/')); ?>img/common/user-no-image.jpg" alt="Noimage">
                          <?php endif;?>
                          <p><?php the_author_meta("user_nickname"); ?>
                            <?php //  if( ! in_array( 'administrator', $user_idnm->roles ) ) : ?>
                            <?php if ($user_idnm->ID == '23' ):?>
                            <?php else: ?>
                              先生
                           <?php endif; ?>
                          </p>
                        </div>
                        <!-- /.author -->
                        <p class="position">
                          <?php	echo do_shortcode('[show_school_name_02]'); ?>
                          <?php //the_author_meta("user_school_name_select"); ?>
                          <?php the_author_meta("user_position"); ?>
                        </p>
                      </div>
                      <!-- /.right -->
                    </a>
                  </li>
                  <!-- /.item -->
              <?php endwhile; ?>


              </ul>
              <!-- /.theme_list -->

              <div class="btn_box border_arrow_btn sp_only">
                <a href="<?php echo esc_url(home_url('/')); ?>thema">テーマ一覧へ</a>
              </div>
              <!-- /.btn_box border_arrow_btn sp_only -->

            </div>
            <!-- /.bg_w_shadow -->
          </section>
          <!-- /.theme_block -->
    <?php endif; ?>
    <?php wp_reset_postdata(); ?>

    <section class="favorite_block">
    <div class="bg_w_shadow">
      <div class="block_header">
        <h2 class="ttl">お気に入りリスト</h2>
      </div>

    <?php
    $favorites = get_user_favorites();
      if ($favorites) :
        $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
        $favorites_query = new WP_Query(array(
          'post_type' => 'post',
          'posts_per_page' => -1,
          'ignore_sticky_posts' => true,
          'post__in' => $favorites,
          'paged' => $paged,
        ));
    ?>
    <?php
      $cat = get_the_category($favorites);
      $cat = $cat[0]->cat_name;
    ?>
    <?php if ($favorites_query->have_posts()) : ?>
      <ul class="favorite_list">
        <?php while ($favorites_query->have_posts()) : ?>
        <?php $favorites_query->the_post(); ?>
        <li>
          <div class="flex">
            <p class="cat">
              <?php echo esc_html($cat); ?>
            </p>
            <p class="ttl">
              <a href="<?php the_permalink(); ?>">
                <?php the_title(); ?>
              </a>
            </p>
            <p class="btn">
              <?php echo get_favorites_button(get_the_ID()); ?>
            </p>
          </div>
        </li>
    <?php endwhile ?>
    <?php endif;wp_reset_postdata(); ?>
      </ul>
      <!-- /.favorite_list -->

    <?php else : ?>
    <p class="no_favorite_list">まだお気に入りがありません。</p>
    <?php endif ?>


      <?php //echo do_shortcode('[user_favorites include_links="true" include_buttons="true" include_thumbnails="true" thumbnail_size="thumbnail600400" include_excerpt="false"]'); ?>

    </div>
    <!-- /.bg_w_shadow -->
    </section>
    <!-- /.favorite_block -->


    <section class="top_comment_block">
      <div class="flex">
        <div class="left">
          <div class="bg_w_shadow">
            <div class="block_header">
              <h2 class="ttl">あなたのコメント履歴</h2>
            </div>
            <ul class="comment_post_list">
<?php
    $user_data = wp_get_current_user();
    $user_id = $user_data->ID;
?>
    <?php
      $args= array(
      	'status' => 'all',
      	'type' => 'comment',
      	'post_status' => 'publish',
        'number'  => 7,
        'user_id'  => $user_id,

      );
    ?>
    <?php
      $comments_query= new WP_Comment_Query;
      $comments = $comments_query->query( $args );
    ?>
    <?php if ( $comments ) :?>
      <?php	foreach ( $comments as $comment ) :?>
        <?php
    		  $id		= absint( $comment->comment_post_ID );
    		  $post_data= get_post( $id );
          $c_id	= $comment->comment_ID;
        ?>
        <li>
          <div class="txt_box">

          <p class="date"><?php comment_date('Y年m月d日', $c_id) ?></p>
            <p class="ttl">
              <?php if(get_field('comment_delete_check', 'comment_'.$c_id)) :?>
                コメントが取り下げられました
                <span class="reason_txt">
                  <?php the_field('comment_delete_reason','comment_'.$c_id);?>
                </span>
            <?php elseif($comment->comment_approved == 0) :?>
              コメントを受け付けました
            <?php elseif($comment->comment_approved == 1) :?>
              <?php if ($comment->comment_parent) : ?>
                <a href="<?php echo esc_url( get_permalink( $id ) );?>?cmtid=<?php echo $comment->comment_parent; ?>#comment-<?php echo $comment->comment_parent;?>">コメントが公開されました。</a>
              <?php else : ?>
                <a href="<?php echo esc_url( get_permalink( $id ) );?>?cmtid=<?php echo $c_id; ?>#comment-<?php echo $c_id;?>">コメントが公開されました。</a>
              <?php endif; ?>
            <?php endif;?>
            </p>
          </div>
        </li>

  <?php endforeach; ?>
  <?php endif; ?>


    </ul>
    <!-- /.comment_post_list -->
  </div>
  <!-- /.bg_w_shadow -->


</div>
<!-- /.left -->
<div class="right">


  <div class="bg_w_shadow">
    <div class="block_header">
      <h2 class="ttl">お気に入り登録したテーマのコメント一覧</h2>
    </div>
<?php if($favorites):?>
    <ul class="comment_post_list">
    <?php
      $args= array(
      	'status' => 'approve',
      	'type' => 'comment',
      	'post_status' => 'publish',
        'number'  => 14,
        'post__in'  => $favorites,

      );
    ?>
    <?php
      $comments_query= new WP_Comment_Query;
      $comments = $comments_query->query( $args );
    ?>
    <?php if ( $comments ) :?>
      <?php	foreach ( $comments as $comment ) :?>
        <?php
    		  $id		= absint( $comment->comment_post_ID );
    		  $post_data= get_post( $id );
          $c_id	= $comment->comment_ID;
        ?>
        <li>
          <div class="txt_box">
            <p class="date"><?php comment_date('Y年m月d日', $c_id) ?></p>
            <p class="ttl">
              <?php if (!get_comment_meta($c_id, 'read')) : ?>
                <span class="ico_no_read">未読</span>
              <?php endif; ?>
              <a href="<?php echo esc_url( get_permalink( $id ));?>?cmtid=<?php echo $c_id; ?>#comment-<?php echo $c_id;?>">
                <?php echo wp_html_excerpt( $post_data->post_title , 25, '...' ); ?>
              </a>
            </p>
          </div>
        </li>
  <?php endforeach; ?>
  <?php endif; ?>
    </ul>
    <!-- /.comment_post_list -->
    <?php else:?>
        <p>お気に入りに登録してる記事がまだありません。</p>
      <?php endif;?>

      </div>
      <!-- /.bg_w_shadow -->
    </div>
    <!-- /.right -->
  </div>
  <!-- /.flex -->
</section>
<!-- /.top_comment_block -->

        </div>
        <!-- /.main_width -->

      </div>
      <!-- /.main_section -->

    </div>
    <!-- /.bg_g -->

  </main>





<?php else: //ログインしていない時 ?>
  <?php get_template_part( 'login_form' ); ?>
<?php endif; ?>
<?php get_footer(); ?>
