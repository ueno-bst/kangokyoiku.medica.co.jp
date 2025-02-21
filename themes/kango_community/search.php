<?php get_header(); ?>
<main>

<?php if( is_user_logged_in() ) : //ログインしている時　?>

  <div class="page_ttl_block">
    <div class="ttl_box">
      <h1 class="ttl main_width">
        「<?php the_search_query(); ?>」<small>の検索結果</small>
      </h1>
    </div>
    <!-- /.ttl_box -->
    <?php fit_breadcrumb(); ?>
  </div>
  <!-- /.page_ttl_block -->


  <div class="bg_g">
    <div class="main_section">
      <div class="main_width">
        <div class="theme_block">
          <div class="bg_w_shadow">

            <ul class="theme_list">


<?php if (have_posts() && get_search_query()) : ?>

  <?php
    $cat = get_the_category();
    $cat = $cat[0]->cat_name;
  ?>

<?php while (have_posts()) :?>
<?php the_post();?>

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
          $user_idnm = $the_query->user->ID;;
        ?>
        <?php if( $img = get_the_author_meta( 'user_prof_img', $user_idnm )): ?>
          <?php	echo do_shortcode('[wpmem_field user_prof_img]'); ?>
        <?php else: ?>
          <img src="<?php echo esc_url(site_url('/')); ?>img/common/user-no-image.jpg" alt="Noimage">
        <?php endif;?>
        <p><?php the_author_meta("user_nickname"); ?>先生</p>
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

<?php else : ?>
            <p>検索キーワードに該当する記事がありませんでした。</p>
<?php endif; ?>


            </ul>
            <!-- /.theme_list -->

            <div class="bottom_search_box">
              <b class="center_ttl">再検索</b>
              <?php get_template_part( 'template-parts/post_search_form' ); ?>

            </div>
            <!-- /.bottom_search_box -->

          </div>
          <!-- /.bg_w_shadow -->

        <?php pagination(); ?>
        <!-- /.pagination -->
        </div>
        <!-- /.theme_block -->



      </div>
      <!-- /.main_width -->
    </div>
    <!-- /.main_section -->
  </div>
  <!-- /.bg_g -->



<?php else: //ログインしていない時 ?>
  <?php get_template_part( 'login_form' ); ?>
<?php endif; ?>

</main>

<?php get_footer(); ?>
