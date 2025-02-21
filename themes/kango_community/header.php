<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
<title><?php wp_title('|', true, 'right'); ?><?php bloginfo('name'); ?></title>
<meta name="description" content="<?php bloginfo('description'); ?>" />
<meta name="keywords" content=" " />
<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
<link rel="icon" href="<?php echo esc_url(site_url('/')); ?>img/ico/favicon.ico">
<link rel="apple-touch-icon" href="<?php echo esc_url(site_url('/')); ?>img/ico/apple-touch-icon.png" sizes="180x180">
<link rel="stylesheet" href="<?php echo esc_url( get_stylesheet_uri()); ?>">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.2/css/select2.min.css">
<link rel="stylesheet" href="<?php echo esc_url(site_url('/')); ?>css/the_content.css">
<link rel="stylesheet" href="<?php echo esc_url(site_url('/')); ?>css/component.css">
<link rel="stylesheet" href="<?php echo esc_url(site_url('/')); ?>css/style.css">
<link rel="stylesheet" href="<?php echo esc_url(site_url('/')); ?>css/page.css">
<link rel="stylesheet" href="<?php echo esc_url(site_url('/')); ?>css/animate.css">
<link rel="stylesheet" href="<?php echo esc_url(site_url('/')); ?>css/wp_member.css">
<link rel="stylesheet" href="<?php echo esc_url(site_url('/')); ?>css/popup.css">
<link rel="stylesheet" href="<?php echo esc_url(site_url('/')); ?>css/comment.css">

<?php wp_head(); ?>
<!-- Google Tag Manager -->
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','GTM-PQ42MQD6');</script>
<!-- End Google Tag Manager -->
</head>
<body <?php body_class(); ?>>
<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-PQ42MQD6"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->
  
<?php if( is_user_logged_in()) : ?>
  <header class="header">
    <div class="inner">
      <div class="bg_w">
        <div class="main_width header_01">
          <?php if ( is_front_page() ) : ?>
            <h1 class="logo left">
              <a href="<?php echo esc_url(home_url('/')); ?>">
                <img src="<?php echo esc_url(site_url('/')); ?>img/common/logo.svg" alt="看護教育力UP↑コミュニティ">
              </a>
            </h1>
            <!-- /.logo left -->
          <?php else: ?>
            <div class="logo left">
              <a href="<?php echo esc_url(home_url('/')); ?>">
                <img src="<?php echo esc_url(site_url('/')); ?>img/common/logo.svg" alt="看護教育力UP↑コミュニティ">
              </a>
            </div>
            <!-- /.logo left -->
          <?php endif; ?>
          <div class="right">
            <ul class="nav_list">
              <li class="about">
                <a href="<?php echo esc_url(home_url('/')); ?>guide/">看護教育力UP↑コミュニティとは</a>
              </li>
              <!-- <li>
                <a href="<?php echo esc_url(home_url('/')); ?>">よくあるご質問</a>
              </li>
              <li>
                <a href="<?php echo esc_url(home_url('/')); ?>">利用規約</a>
              </li> -->
              <li class="contact">
                <a href="https://business.form-mailer.jp/fms/fa8b45c0209306" target="_blank" rel="noopener">お問い合わせ</a>
              </li>
            </ul>
            <!-- /.nav_list-->
  <?php if( is_user_logged_in() ) : ?>
  <?php //公開・非公開の変数
    global $current_user;
    $school_show_hidden =  $current_user->school_show_hidden;
    $position_show_hidden =  $current_user->position_show_hidden;
    $area_show_hidden =  $current_user->area_show_hidden;
  ?>
            <div class="header_account">
              <div id="js_account_show_btn">
                <?php
                  $user_data = wp_get_current_user();
                  $user_idnm = $user_data->ID;
                ?>
                <?php if( $img = get_the_author_meta( 'user_prof_img', $user_idnm )): ?>
                  <?php	echo do_shortcode('[wpmem_field user_prof_img]'); ?>
                <?php else: ?>
                  <img src="<?php echo esc_url(site_url('/')); ?>img/common/user-no-image.jpg" alt="Noimage">
                <?php endif;?>
              </div>

              <div id="js_popup" class="popup">
                <div class="bg_w_shadow account_box">
                  <div class="flex">
                    <div class="img">
                      <?php if( $img = get_the_author_meta( 'user_prof_img', $user_idnm )): ?>
                        <?php	echo do_shortcode('[wpmem_field user_prof_img]'); ?>
                      <?php else: ?>
                        <img src="<?php echo esc_url(site_url('/')); ?>img/common/user-no-image.jpg" alt="Noimage">
                      <?php endif;?>
                    </div>
                    <!-- /.img -->
                    <div class="info">
                      <b class="name">
                        <?php	echo do_shortcode('[wpmem_field user_nickname]'); ?>先生
                      </b>
                      <div class="info_sub">
                        <?php if($school_show_hidden == '公開'):?>
                        <!-- ▼ fsta ikawa update -->
                        <?php /* <span class="school"><?php	echo do_shortcode('[wpmem_field user_school_name_select]'); ?></span> */ ?>
                        <span class="school"><?php	echo do_shortcode('[show_school_name]'); ?></span>
                        <!-- ▲ fsta ikawa update -->
                      <?php elseif($school_show_hidden == '非公開'):?>
                        <span class="school">学校名非公開</span>
                      <?php endif ?>
                      <?php if($position_show_hidden == '公開'):?>
                        <span class="position"><?php	echo do_shortcode('[wpmem_field user_position]'); ?></span>
                      <?php elseif($position_show_hidden == '非公開'):?>
                        <span class="position">肩書き非公開</span>
                      <?php endif ?>
                      <?php if($area_show_hidden == '公開'):?>
                        <span class="position"><?php	echo do_shortcode('[wpmem_field user_area]'); ?></span>
                      <?php elseif($area_show_hidden == '非公開'):?>
                        <span class="position">担当領域非公開</span>
                      <?php endif ?>
                      <!-- ▼ fsta ikawa add -->
                      <span class="valid"><?php	echo do_shortcode('[wpmem_field user_expired_date]'); ?> まで有効</span>
                      <!-- ▲ fsta ikawa add -->
                        <!-- <span class="valid">2024年5月末まで有効</span> -->
                      </div>
                    </div>
                    <!-- /.info -->
                  </div>
                  <!-- /.flex -->
                  <div class="btn_box btn_box_01 border_arrow_btn">
                    <a href="<?php echo esc_url(home_url('/')); ?>user_edit">会員情報・編集</a>
                  </div>
                  <!-- /.btn_box -->
                  <div class="btn_box btn_box_02 logout">
                    <?php	echo do_shortcode('[wpmem_logout]ログアウト[/wpmem_logout]'); ?>
                  </div>
                  <!-- /.btn_box -->
                </div>
                <!-- /.bg_w_shadow account_box -->
              </div>
              <!-- /.popup #js_popup -->

            </div>
            <!-- /.header_account -->
    <?php endif; ?>


          </div>
          <!-- /.right -->

          <div class="nav_btn sp_only">
            <span></span>
            <span></span>
            <span></span>
          </div>
          <!-- /.nav_btn sp_only -->

        </div>
        <!-- /.main_width -->
      </div>
      <!-- /.bg_w header_01 -->

      <div class="bg_b">
        <div class="main_width header_02">
          <ul class="nav_list">
            <li>
              <a href="<?php echo esc_url(home_url('/')); ?>">マイページTOP</a>
            </li>
            <li>
              <a href="<?php echo esc_url(home_url('/')); ?>news-list/">お知らせ一覧</a>
            </li>
            <li>
              <a href="<?php echo esc_url(home_url('/')); ?>thema/">テーマ一覧</a>
            </li>
            <li>
              <a href="<?php echo esc_url(home_url('/')); ?>member/">執筆者から探す</a>
            </li>
          </ul>
        </div>
        <!-- /.main_width -->
      </div>
      <!-- /.bg_b header_02 -->

    </div>
    <!-- /.inner -->

    <div class="nav_box_sp bg_g sp_only">
      <div class="nav_scroll">

        <div class="account_box bg_w">
          <div class="main_width">
            <div class="flex">
              <div class="img">
                <?php if( $img = get_the_author_meta( 'user_prof_img', $user_idnm )): ?>
                  <?php	echo do_shortcode('[wpmem_field user_prof_img]'); ?>
                <?php else: ?>
                  <img src="<?php echo esc_url(site_url('/')); ?>img/common/user-no-image.jpg" alt="Noimage">
                <?php endif;?>
              </div>
              <!-- /.img -->
              <div class="info">
                <b class="name">
                  <?php	echo do_shortcode('[wpmem_field user_nickname]'); ?>先生
                </b>
                <div class="info_sub">
                  <!-- ▼ fsta ikawa update -->
                  <?php /* <span class="school"><?php	echo do_shortcode('[wpmem_field user_school_name_select]'); ?>(非公開)</span> */ ?>
                  <span class="school"><?php	echo do_shortcode('[show_school_name]'); ?></span>
                  <!-- ▲ fsta ikawa update -->
                  <!-- ▼ fsta ikawa add -->
                  <!-- FSTODO レイアウトが崩れるので直してほしい -->
                  <span class="valid"><?php	echo do_shortcode('[wpmem_field user_expired_date]'); ?> まで有効</span>
                  <!-- ▲ fsta ikawa add -->
                  <!-- <span class="valid">2024年5月末まで有効</span> -->
                </div>
              </div>
              <!-- /.info -->
            </div>
            <!-- /.flex -->
            <div class="btn_box border_arrow_btn">
              <a href="<?php echo esc_url(home_url('/')); ?>user_edit">会員情報・編集</a>
            </div>
            <!-- /.btn_box -->
          </div>
          <!-- /.main_width -->
        </div>
        <!-- /.account_box bg_w -->

        <div class="bg_w nav_list nav_01">
          <div class="main_width">
            <ul>
              <li>
                <a href="<?php echo esc_url(home_url('/')); ?>">マイページTOP</a>
              </li>
              <li>
                <a href="<?php echo esc_url(home_url('/')); ?>news-list/">お知らせ一覧</a>
              </li>
              <li>
                <a href="<?php echo esc_url(home_url('/')); ?>thema/">テーマ一覧</a>
              </li>
              <li>
                <a href="<?php echo esc_url(home_url('/')); ?>member/">執筆者から探す</a>
              </li>
              <li>
                <a href="<?php echo esc_url(home_url('/')); ?>guide/">看護教育力UP↑コミュニティとは</a>
              </li>
            </ul>
            <!-- /.nav_list nav_01 -->
          </div>
          <!-- /.main_width -->
        </div>
        <!-- /.bg_w -->
        <div class="bg_w nav_list nav_02">
          <div class="main_width">
            <ul>
              <li>
                <a href="<?php echo esc_url(home_url('/')); ?>">ご利用規約</a>
              </li>
              <!-- <li>
                <a href="<?php echo esc_url(home_url('/')); ?>">よくあるご質問</a>
              </li> -->
              <li>
                <a href="https://business.form-mailer.jp/fms/fa8b45c0209306" target="_blank" rel="noopener">お問い合わせ</a>
              </li>
            </ul>
            <!-- /.nav_list nav_02 -->
          </div>
          <!-- /.main_width -->
        </div>
        <!-- /.bg_w -->
        <div class="bg_w nav_list nav_03 logout">
          <div class="main_width">
            <ul>
              <li>
                <a href="<?php echo esc_url(home_url('/')); ?>" class="logout_btn">ログアウト</a>
              </li>
            </ul>
            <!-- /.nav_list nav_03 -->
          </div>
          <!-- /.main_width -->
        </div>
        <!-- /.bg_w -->
      </div>
      <!-- /.nav_scroll -->

    </div>
    <!-- /.nav_box_sp -->

  </header>
<?php else: ?>
  <header class="header_no_login">
    <div class="logo">
      <img src="<?php echo esc_url(site_url('/')); ?>img/common/logo.svg" alt="看護教育力UP↑コミュニティ">
    </div>
    <!-- /.logo left -->
  </header>
  <!-- /.ver_no_login_box -->
<?php endif; ?>
