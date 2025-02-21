<?php get_header(); ?>

<?php fit_breadcrumb(); ?>



  <h2 style="font-size:3.0rem;"><?php single_cat_title(''); ?><small>の記事一覧</small></h2>

<ul>
<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
<li>
  <?php if(has_post_thumbnail()): ?>
    <?php the_post_thumbnail('thumbnail300300'); ?>
  <?php else: ?>
    <img src="<?php echo esc_url(home_url('/')); ?>img/no-image300300.jpg" alt="Noimage"/>
  <?php endif; ?>


<h3><a href="<?php the_permalink() ?>"><?php the_title(); ?></a></h3>
<?php the_time('Y.m.d') ?>

</li>

<?php endwhile; ?>
<?php else: ?>
    <h2>まだ記事がありません</h2>
<?php endif; ?>



<?php pagination(); ?>




<?php get_sidebar(); ?>





<?php get_footer(); ?>
