<?php
/*
Template Name: お知らせ一覧テンプレート
*/
?>

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
              <?php
                $paged = get_query_var('paged') ? get_query_var('paged') : 1;
                $args = array(
                    'post_type' => 'news',
                    'posts_per_page' => 15,
                    'paged' => $paged,
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
              <?php while ( $the_query->have_posts() ) : $the_query->the_post(); ?>

                <a href="<?php the_permalink(); ?>">
                  <p class="date"><?php the_time('Y年n月j日'); ?></p>
                  <p class="news_ttl"><?php the_title(); ?></p>
                </a>

              <?php endwhile; ?>

            <?php endif; ?>
            <?php wp_reset_postdata(); ?>

            </div>
            <!-- /.news_list -->
          </div>
          <!-- /.bg_w_shadow -->
          <?php if ($the_query->max_num_pages > 1) : ?>
          <?php
            $limitnum = 999999999;
            echo paginate_links(array(
              'base'         => str_replace($limitnum, '%#%', esc_url(get_pagenum_link($limitnum))),
              'format'       => '',
              'current'      => max(1, get_query_var('paged')),
              'total'        => $the_query->max_num_pages,
              'prev_next'    => true,
              'type'         => 'list',
              'end_size'     => 3,
              'mid_size'     => 3,
              'prev_text' => '<img src="'.esc_url(site_url()).'/img/common/arrow-l-btn.svg" alt="前へ">',
              'next_text' => '<img src="'.esc_url(site_url()).'/img/common/arrow-r-btn.svg" alt="前へ">'

            ));
          ?>
          <?php endif; ?>

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
