<?php
/*
Template Name: ご執筆の先生方テンプレート
*/
?>

<?php get_header(); ?>

<main>

  <div class="page_ttl_block">
    <div class="ttl_box">
      <h1 class="ttl main_width">ご執筆の先生方</h1>
    </div>
    <!-- /.ttl_box -->
    <?php fit_breadcrumb(); ?>

    <div class="bg_g">
      <div class="main_section">
        <div class="main_width">
          <div class="teachers_block">

<?php
  $users = get_users(array(
    'role'=> 'contributor', //寄稿者のみ
    'orderby' => 'ID',
    'order' => 'ASC',
  ));
?>
            <ul class="list">
  <?php foreach($users as $user) :?>
  <?php
    $uid = $user->ID;
  ?>

  <li class="item bg_w_shadow">
    <div class="author">
      <?php if( $img = get_the_author_meta( 'user_prof_img', $uid )): ?>
        <?php echo get_avatar($uid, 150); ?>
      <?php else: ?>
        <img src="<?php echo esc_url(site_url('/')); ?>img/common/user-no-image.jpg" alt="Noimage">
      <?php endif;?>
      <div class="info">
        <p class="name">
          <?php echo $user->user_nickname ; ?>先生
        </p>
        <p class="position">
          <!-- ▼ fsta ikawa update 20240130 -->
          <?php /* <?php echo $user->user_school_name_select ; ?> <?php echo $user->user_position ; ?> */ ?>
          <?php	echo do_shortcode('[show_school_name_by_shcool_id '.$user->user_school_name_select.']'); ?> <?php echo $user->user_position ; ?>
          <!-- ▲ fsta ikawa update 20240130 -->
        </p>
        <a href="<?php echo get_bloginfo("url") . '/?author=' . $uid ?>" class="theme_btn">
          <span>この先生のテーマ一覧</span>
        </a>
      </div>
      <!-- /.inner -->
    </div>
    <!-- /.author -->

    <?php
      $args = array(
          'post_type' => 'post',
          'author' => $uid, //　投稿者ID
          'posts_per_page' => 2
      );
      $the_query = new WP_Query( $args );
    ?>
    <?php if ( $the_query->have_posts() ) : ?>
    <div class="article_box">
      <p class="ttl">執筆記事</p>
      <div class="article_list">
        <?php while ( $the_query->have_posts() ) : $the_query->the_post(); ?>
          <?php
            $cat = get_the_category();
            $cat = $cat[0]->cat_name;
          ?>
          <a href="<?php the_permalink(); ?>">
          <p class="category"><?php echo esc_html($cat); ?></p>
          <p class="article_ttl"><?php the_title(); ?></p>
        </a>
      <?php endwhile; ?>
      </div>
      <!-- /.article_list -->
    </div>
    <!-- /.article_box -->
  <?php endif; ?>
  <?php wp_reset_postdata(); ?>


  </li>
<?php endforeach; ?>
</ul>
<!-- /.list -->



</div>
<!-- /.teachers_block -->

</div>
<!-- /.main_width -->

</div>
<!-- /.main_section -->

</div>
<!-- /.bg_g -->



</main>
<?php get_footer(); ?>
