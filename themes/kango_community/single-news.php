<?php get_header(); ?>
<main>

<?php if( is_user_logged_in() ) : //ログインしている時　?>

  <div class="page_ttl_block">
    <div class="bread_box">
      <div class="main_width">
        <nav class="nav_list">
          <ol itemscope itemtype="http://schema.org/BreadcrumbList">
            <li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
              <a itemprop="item" href="<?php echo esc_url(home_url('/')); ?>"><span itemprop="name">TOP</span></a>
            <meta itemprop="position" content="1" />
            </li>
            <li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
              <a itemprop="item" href="<?php echo esc_url(home_url('/')); ?>news-list/"><span itemprop="name">お知らせ一覧</span></a>
            <meta itemprop="position" content="2" />
            </li>
            <li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
              <span itemprop="name"><?php the_title(); ?></span>
              <meta itemprop="position" content="3" />
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
        <div class="theme_detail_block detail_block">
          <div class="bg_w_shadow">

            <?php if (have_posts()) : ?>
            <?php while (have_posts()) : the_post(); ?>

              <div class="width_800">
                <div class="detail_ttl_box">
                  <div class="theme_info">
                    <p class="time"><?php the_time('Y年n月j日'); ?></p>
                  </div>
                  <h1 class="ttl"><?php the_title(); ?></h1>
                </div>
                <!-- /.detail_ttl_box -->

                <div class="the_content single_box">
                  <?php the_content(); ?>
                </div>
                <!-- /.the_content.single_box -->

              </div>
              <!-- /.width_800 -->

            <?php endwhile; ?>
            <?php else: ?>
              <h2>まだ記事がありません</h2>
            <?php endif; ?>

          </div>
          <!-- /.bg_w_shadow -->

          <?php
            $prev_post = get_previous_post( true, '', '');
            $next_post = get_next_post( true, '', '');
          ?>
          <?php if( $prev_post || $next_post ): //次の記事か前の記事かどちらかあれば ?>
            <div class="page_btn_box">
              <div class="flex" id="js_page_btn">
                <?php if ($prev_post): // 前の記事 ?>
                  <a href="<?php echo esc_url(get_permalink($prev_post->ID)); ?>" class="prev page_btn">
                    <p class="en">PREV</p>
                    <p class="page_ttl"><?php echo esc_html($prev_post->post_title); ?></p>
                  </a>
                <?php endif; ?>
                <?php if ($next_post): // 前の記事 ?>
                  <a href="<?php echo esc_url(get_permalink($next_post->ID)); ?>" class="next page_btn">
                    <p class="en">NEXT</p>
                    <p class="page_ttl"><?php echo esc_html($next_post->post_title); ?></p>
                  </a>
                <?php endif; ?>
              </div>
              <!-- /.flex -->
            </div>
            <!-- /.page_btn_box -->
          <?php endif; ?>
        </div>
        <!-- /.theme_detail_block detail_block -->
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