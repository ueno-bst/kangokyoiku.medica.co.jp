<?php get_header(); ?>
<main>

<?php if( is_user_logged_in() ) : //ログインしている時　?>

  <div class="page_ttl_block">
    <div class="ttl_box">
      <h1 class="ttl main_width">お知らせ一覧</h1>
    </div>
    <!-- /.ttl_box -->
    <div class="bread_box">
      <div class="main_width">
        <nav class="nav_list">
          <ol itemscope itemtype="http://schema.org/BreadcrumbList">
            <li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
              <a itemprop="item" href="<?php echo esc_url(home_url('/')); ?>"><span itemprop="name">TOP</span></a>
            <meta itemprop="position" content="1" />
            </li>
            <li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
              <span itemprop="name">お知らせ一覧</span>
              <meta itemprop="position" content="2" />
            </li>
          </ol>
        </nav>
      </div>
      <!-- /.main_width -->
    </div>
    <!-- /.bread_box -->
  </div>
  <!-- /.page_ttl_block -->

  <div class="bg_g">
    <div class="main_section">
      <div class="main_width">
        <div class="news_block page_news_block">
          <div class="bg_w_shadow">
            <div class="news_list">
              <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
                <a href="<?php the_permalink(); ?>">
                  <p class="date"><?php the_time('Y年n月j日'); ?></p>
                  <p class="news_ttl"><?php the_title(); ?></p>
                </a>
              <?php endwhile; ?>
              <?php else: ?>
                <h2>まだ記事がありません</h2>
              <?php endif; ?>
            </div>
            <!-- /.news_list -->
          </div>
          <!-- /.bg_w_shadow -->
          <?php pagination(); ?>
        </div>
        <!-- /.news_block page_news_block -->
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
