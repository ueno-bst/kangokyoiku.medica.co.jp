<?php get_header(); ?>

<main>

    <div class="page_ttl_block">
        <div class="ttl_box">
            <h1 class="ttl main_width">
                <?php if($monthnum||$year||$cat): ?>
                    <?php if($cat): ?><?php single_cat_title(); ?><?php endif; ?>
                    <?php if($monthnum||$year): ?><?php echo $year.'年'; ?><?php endif; ?>
                    <?php if($monthnum): ?><?php echo $monthnum.'月'; ?><?php endif; ?>
                <?php endif; ?><small>の記事一覧</small>
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
