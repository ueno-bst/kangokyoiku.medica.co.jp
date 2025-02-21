<?php
/*
Template Name: ヘッダーとフッターなし用のページテンプレート
*/
?>

<?php get_header(); ?>


<?php
   $page = get_post( get_the_ID() );
   $slug = $page->post_name;
 ?>
<?php if (have_posts()) : ?>
  <?php while (have_posts()) : the_post(); ?>

    <div class="page_ttl_block <?php echo $slug; ?> ver_no_login_block">
      <div class="ttl_box">
<?php if( is_user_logged_in() ) : //ログインしている時　?>
          <h1 class="ttl main_width"><?php the_title(); ?></h1>
<?php else: //ログインしていない時 ?>
          <h1 class="ttl main_width center"><?php the_title(); ?></h1>
<?php endif; ?>
      </div>
      <!-- /.ttl_box -->
    </div>
    <!-- /.page_ttl_block -->


    <div class="main_section">
      <div class="main_width">

      <?php remove_filter ('the_content', 'wpautop'); ?>
      <?php remove_filter( 'the_excerpt', 'wpautop' ); ?>

      <?php the_content();?>

<?php endwhile; ?>
<?php else : ?>

<h2>ページが見つかりません</h2>
<p>ページが存在しない場合があります。お手数ですが、<a href="<?php echo esc_url(home_url() ); ?>">【HOME】</a>に戻っていただくようお願いします。</p>
<?php endif; ?>

</div>
<!-- /.main_width -->
</div>
<!-- /.main_section -->



</main>
<?php get_footer(); ?>
