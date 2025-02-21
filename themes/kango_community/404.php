<?php get_header(); ?>

<main>

  <div class="page_ttl_block">
    <div class="ttl_box">
      <h1 class="ttl main_width center">
        -404-<br><small>ページが見つかりませんでした。</small>
      </h1>
    </div>
    <!-- /.ttl_box -->
    <?php fit_breadcrumb(); ?>
  </div>
  <!-- /.page_ttl_block -->

  <div class="bg_g">
    <div class="main_section ver_404">
      <div class="main_width">
        <div class="txt_base">
          <p>
            お探しのページが見つかりませんでした。<br>
            お手数ですが、下記のボタンよりTOPページにお戻り下さい。
          </p>
        </div>
        <!-- /.txt_base -->
        <p class="border_arrow_btn">
          <a href="<?php echo esc_url(home_url('/')); ?>">TOPページに戻る</a>
        </p>


      </div>
      <!-- /.main_width -->
  </div>
  <!-- /.main_section -->
</div>
<!-- /.bg_g -->

<?php get_footer(); ?>
