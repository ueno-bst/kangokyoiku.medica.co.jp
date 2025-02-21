<?php
/*
Template Name: 特殊ページテンプレート
*/
?>
<?php get_header(); ?>

<?php if( is_user_logged_in() ) : //ログインしている時　?>

<?php
   $page = get_post( get_the_ID() );
   $slug = $page->post_name;
 ?>

<?php if (have_posts()) : ?>
  <?php while (have_posts()) : the_post(); ?>

  <div class="page_ttl_block">
    <?php fit_breadcrumb(); ?>
  </div>
  <!-- /.page_ttl_block -->

      <?php remove_filter ('the_content', 'wpautop'); ?>
      <?php remove_filter( 'the_excerpt', 'wpautop' ); ?>

      <?php the_content();?>

<?php endwhile; ?>
<?php else : ?>

<h2>ページが見つかりません</h2>
<p>ページが存在しない場合があります。お手数ですが、<a href="<?php echo esc_url(home_url() ); ?>">【HOME】</a>に戻っていただくようお願いします。</p>
<?php endif; ?>



<?php else: //ログインしていない時 ?>
  <?php get_template_part( 'login_form' ); ?>
<?php endif; ?>
</main>
<?php get_footer(); ?>
