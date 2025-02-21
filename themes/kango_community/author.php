<?php get_header(); ?>

<main>

  <div class="page_ttl_block">
    <div class="ttl_box">
      <h1 class="ttl main_width">
        <?php the_author_meta("user_nickname"); ?><small>先生のテーマ一覧</small>
      </h1>
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
              <a itemprop="item" href="<?php echo esc_url(home_url('/')); ?>member"><span itemprop="name">ご執筆の先生方</span></a>
            <meta itemprop="position" content="2" />
            </li>
            <li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
              <span itemprop="name"><?php the_author_meta("user_nickname"); ?>先生のテーマ一覧</span>
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
        <div class="theme_block">
          <div class="bg_w_shadow">

            <?php get_template_part( 'template-parts/post_search_form' ); ?>


            <ul class="theme_list">

              <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
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
                    </div>
                    <!-- /.right -->
                  </a>
                </li>
                <!-- /.item -->

              <?php endwhile; ?>
              <?php else: ?>
                <h2>まだ記事がありません</h2>
              <?php endif; ?>

            </ul>
            <!-- /.theme_list -->

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
</main>

<?php get_footer(); ?>