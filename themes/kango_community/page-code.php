<?php
/*
  Template Name: 認証コード確認画面
 */
?>
<?php get_header(); ?>

<?php
$page = get_post(get_the_ID());
$slug = $page->post_name;
?>

<style type="text/css">
    .main_section {
        width: 100%;
        max-width: 600px;
        margin: 0 auto;
        /*    padding: 20px;
            border: 1px solid #ddd;*/
    }

    .main_section form {
        padding: 20px;
        border: 1px solid #ddd;
    }

    .main_section form p {
        margin-bottom: 15px;
    }

    .main_section label {
        display: block;
        margin-bottom: 5px;
    }

    .main_section input[type="text"],
    .main_section input[type="tel"] {
        width: 100%;
        padding: 10px;
        border: 1px solid #ccc;
        border-radius: 4px;
    }

    .main_section input[type="submit"] {
        padding: 0.7em 2em;
        background-color: #3F559F;
        color: #fff;
        border: none;
        border-radius: 0;
        cursor: pointer;
        font-size: 1.4rem;
    }

    .main_section input[type="submit"]:hover {
        background-color: #31417a;
    }
    .error-message p{
        color: #ff2525;
    }
    .txt_box {
        /*padding-top: 20px;*/
        padding-bottom: 20px;
    }
    .txt_box.ver_center {
      margin-bottom: 20px;
      text-align: center;
    }
    .txt_box.ver_contact {
      text-align: center;
      margin-top: 10px;
    }
    .txt_box a {
      color: #DF8390;
      text-decoration: underline;
      font-size: 1em;
    }
    .txt_box span.info {
        border-bottom: solid 1px;
    }
    @media screen and (max-width: 767px) {
      .txt_box.ver_center {
        margin-bottom: 10px;
        font-size: 1.4rem;
        text-align: left;
      }
      .txt_box.ver_contact {
        font-size: 1.3rem;
      }
      .page_ttl_block {
          margin-top: 0;
      }
    }
</style>
<div class="page_ttl_block">
    <div class="ttl_box">
        <h1 class="ttl main_width"><?php the_title(); ?></h1>
    </div>
    <!-- /.ttl_box -->
    <?php fit_breadcrumb(); ?>
</div>
<!-- /.page_ttl_block -->

<div class="main_section <?php echo $slug; ?>">
    <div class="main_width">
        <div class="txt_base">

            <p class="txt_box ver_center">
                <?php if (is_user_logged_in()) : ?>
                認証コードは、登録時の所属校様へ郵送にてお送りしております。
                <br>
                <span class="info">※今年度のご案内記載の認証コードをご入力ください。</span>
                <?php else: ?>
                認証コードは、教科書またはBeNs.採用校様へ郵送にてお送りしております。
                <br>
                <span class="info">※今年度のご案内記載の認証コードをご入力ください。</span>
                <br>
                未採用校の方はご利用いただけません。ご了承ください。
                <?php endif ?>
            </p>

            <form action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="post">
                <input type="hidden" name="action" value="school_auth_code_action">
                <p>
                    <label for="auth_code">認証コード</label>
                    <input type="text" id="auth_code" name="auth_code" pattern="[a-zA-Z0-9 ]+"
                           value="<?php echo isset($_POST['auth_code']) ? esc_attr($_POST['auth_code']) : ''; ?>" required>
                </p>
                <p>
                    <input type="submit" name="btn_auth_code_submit" value="送信">
                </p>

                <?php
                    if(!empty($_SESSION['school_auth_code_action_error_flag'])) {
                        echo '<div class="error-message">';
                        echo '<p>入力内容に不備があります。再度お確かめのうえ、入力ください。</p>';
                        echo '<p>※認証コードは今年度のご案内に記載の内容のみ有効です。</p>';
                        echo '<p>それでも解消されない場合は<a href="https://business.form-mailer.jp/fms/fa8b45c0209306" target="_blank">こちら</a>へお問い合わせください。</p>';
                        echo '</div>';
                        unset($_SESSION['school_auth_code_action_error_flag']);
                    }
                ?>
            </form>


            <p class="txt_box ver_contact">
                認証コードが不明、他問い合わせは<a href="https://business.form-mailer.jp/fms/fa8b45c0209306" target="_blank">こちら</a>
            </p>
        </div>


    </div>
    <!-- /.main_width -->
</div>
<!-- /.main_section -->


</main>
<?php get_footer(); ?>
