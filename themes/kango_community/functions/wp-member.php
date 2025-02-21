<?php
//管理者以外管理画面のツールバーを非表示
if (! current_user_can('delete_users')){
    show_admin_bar(false);
}

//ログインしたらTOPページにリダイレクト
function my_login_redirect( $redirect_to, $user_id ) {
	return '/';
}
add_filter( 'wpmem_login_redirect', 'my_login_redirect', 10, 2 );

// 会員登録画面からユーザー名を取り除く
add_filter( 'wpmem_register_form_rows', function( $rows ) {
	unset( $rows['username'] );
	return $rows;
});
// メールアドレスからユーザー名を作成する
add_filter( 'wpmem_pre_validate_form', function( $fields ) {
	$parts = $fields['user_email'];
	$fields['username'] = $parts;
	return $fields;
});


// 新規登録画面のカスタマイズ
add_action( 'wpmem_register_heading', 'my_register_heading', 10, 2 );
function my_register_heading( $heading, $tag ){
	if( $tag == 'new' ){
		$heading = '';
	}
	return $heading;
}

//必須項目」の位置を変更する
function my_register_form_args_req( $args ) {
  $args = array(
    'req_label' => '',
    'heading_after' => '</legend>
                          <div class="req_text">
                            <span class="req">*</span>は必須項目になります。<br>
                            ※お名前などの公開・非公開の設定は、コメント投稿時に反映されます。<br>
                            ※各項目の公開・非公開は、後で変更することも可能です。
                          </div>',
    'row_before' => '<div class="form_custom_flex">',
    'row_after'  => '</div>',
  );
return $args;
}
add_filter('wpmem_register_form_args', 'my_register_form_args_req', 10, 1);



//登録完了時のリダイレクト
function my_reg_redirect( $fields ) {
  $base_url = get_home_url();
  wp_redirect($base_url."/register-complete/");
  exit();
}
add_action( 'wpmem_register_redirect', 'my_reg_redirect' );

//WP MEBER 自動抽出のテキストを更新
//https://securavita.net/wp-members_change_default_text/
add_filter( 'wpmem_default_text', 'sv_wpmem_default_text' );
function sv_wpmem_default_text( $text ) {
    $text['login_heading'] = '会員ログイン';
    $text['login_username'] = 'メールアドレス';
    $text['pwdreset_button'] = 'パスワードを再発行する';
    return $text;
}



/* comments.phpで呼び出すwp_list_commentsに指定するコールバック関数 */
function mytheme_comment($comment, $args, $depth) {
   $GLOBALS['comment'] = $comment; ?>
   <li <?php comment_class(); ?> id="li-comment-<?php comment_ID() ?>">
     <div id="comment-<?php comment_ID(); ?>">
      <div class="comment_author vcard">
        <?php
        $comment_id = $comment;
        $user_idnm = $comment_id->user_id; //投稿者のIDを取得
        // echo $comment_id->user_id;
        ?>
        <?php if( $img = get_the_author_meta( 'user_prof_img', $user_idnm )): ?>
          <?php echo get_avatar($user_idnm, 150); ?>
        <?php else: ?>
          <img src="<?php echo esc_url(site_url('/')); ?>img/common/user-no-image.jpg" alt="Noimage">
        <?php endif;?>

         <?php /*アバターも無効化*/ /*echo get_avatar($comment,$size='48',$default='<path_to_url>' );*/ ?>
         <?php /*コメント投稿時指定のURLを非表示*/ /*echo get_comment_author_link( $comment_ID );*/ ?>
         <?php /*コメント投稿時指定の投稿者名を非表示*/
          /* printf(__('<cite class="fn">%s</cite> <span class="says">より:</span>'), get_comment_author())
          */
         ?>
      </div>
      <!-- /.comment_author -->
      <?php if ($comment->comment_approved == '0') : ?>
         <em><?php _e('Your comment is awaiting moderation.') ?></em>
         <br />
      <?php endif; ?>

      <div class="comment_arrow_box">
        <div class="comment-meta commentmetadata">
          <p class="name"><?php the_author_meta("user_nickname",$user_idnm); ?></p>
          <p class="comment_time"><?php printf(__('%1$s at %2$s'), get_comment_date(),  get_comment_time()) ?></p>
        </div>
        <?php comment_text() ?>
      </div>
      <!-- /.comment_arrow_box -->

      <div class="reply">
         <?php comment_reply_link(array_merge( $args, array('depth' => $depth, 'max_depth' => $args['max_depth']))) ?>
      </div>
     </div>
<?php
        }
/* comments.phpで呼び出すwp_list_commentsに指定するコールバック関数 ここまで*/


//プロフィール画像をアバター画像に同期
add_filter( 'get_avatar', 'wpmem_ul_my_custom_avatar', 10, 6 );
function wpmem_ul_my_custom_avatar( $avatar, $id_or_email, $size, $default, $alt, $args ) {

    // What is the custom image field's meta key?
    // Set this value to match the meta key of your custom image field.
    $meta_key = "user_prof_img";

    // Nothing really to change below here, unless
    // you want to change the <img> tag HTML.
    $user = false;
    if ( is_numeric( $id_or_email ) ) {
        $user = get_user_by( 'id' , (int)$id_or_email );
    } elseif ( is_object( $id_or_email ) ) {
        if ( ! empty( $id_or_email->user_id ) ) {
            $id = (int)$id_or_email->user_id;
            $user = get_user_by( 'id' , $id );
        }
    } else {
        $user = get_user_by( 'email', $id_or_email );
    }
    if ( $user && is_object( $user ) ) {
        $post_id = get_user_meta( $user->ID, $meta_key, true );
        if ( $post_id ) {
            $attachment_url = wp_get_attachment_url( $post_id );

            // HTML for the avatar <img> tag.  This is WP default.
            // $avatar = '<img alt="' . $alt . '" src="' . $attachment_url . '" class="avatar avatar-' . $size . ' photo" height="' . $size . '" width="' . $size . '" />';
            $avatar = sprintf(
                "<img alt='%s' src='%s' class='%s' height='%d' width='%d' %s/>",
                esc_attr( $alt ),
                esc_url( $attachment_url ),
                esc_attr( "avatar avatar-" . $size . " photo" ),
                (int) $args['height'],
                (int) $args['width'],
                $args['extra_attr']
            );
        }
    }
    return $avatar;
}


//コメントフォーム部分の表示カスタマイズ
function comment_fields_control($defaults){
  $current_user = wp_get_current_user('ID');
  $user_id = $current_user->ID;
  $user_name = get_the_author_meta( 'user_nickname', $user_id );
  $defaults['title_reply_to'] = '返信コメントを入力'; //返信時のタイトルテキスト変更
  // $defaults['cancel_reply_link'] = 'ううう'; // 送信ボタンのラベル
  $defaults['label_submit'] = 'コメントを送信'; // 送信ボタンのラベル
  $defaults['logged_in_as'] = '<p class="comment_notice">※コメントはメディカ出版にて確認・承認後公開となります。</p><p class="logged-in-as">'.$user_name.'でログイン中</p>'; // 送信ボタンのラベル
  return $defaults;
}
add_filter( 'comment_form_defaults', 'comment_fields_control');

//登録時の利用規約のリンク先を固定ページに
add_filter( 'wpmem_tos_link_txt', 'custom_wpmem_tos_link_txt' );
function custom_wpmem_tos_link_txt( $text )
{
	$text = '</a>利用規約に同意する <a href="/terms/" target="_blank" rel="noopener" class="term_link">利用規約はこちら</a>';
	return $text;
}

//パスワードを忘れた方へのリンク先を変更
function my_username_link_str( $str, $link ) {
	return "<a href=\"/forgot-user/\">ユーザー名をお忘れの方はこちら</a>";
}
add_filter( 'wpmem_username_link_str', 'my_username_link_str', 10, 2 );

//会員登録フォームテキスト挿入用のhook
function my_login_form_rows( $rows, $tag ){
  // $rows['user_school_name_select']['field_after'] = '<span class="myform__notice">※学校名で検索</span></div>';
  $rows['billing_phone']['field_after'] = '<span class="myform__notice">※ハイフンありで入力して下さい
</span></div>';
  $rows['tos']['row_before'] = '<div class="form_custom_flex ver_column01">';
  $rows['tos']['field_after'] = '<p><a href="https://store.medica.co.jp/privacy.html" target="_blank" rel="noopener">個人情報の取り扱いについてはこちら</a></p></div>';
  $rows['user_school_name_select']['row_after'] = '';
  $rows['school_show_hidden']['row_before'] = '<div class="field_mini">';
  $rows['school_show_hidden']['row_after'] = '</div></div>';
  $rows['user_position']['row_after'] = '';
  $rows['position_show_hidden']['row_before'] = '<div class="field_mini">';
  $rows['position_show_hidden']['row_after'] = '</div></div>';
  $rows['user_area']['row_after'] = '';
  $rows['area_show_hidden']['row_before'] = '<div class="field_mini">';
  $rows['area_show_hidden']['row_after'] = '</div></div>';
  $rows['last_name']['row_before'] = '<div class="form_custom_flex ver_name"><label for="last_name" class="text">お名前<span class="req">*</span></label><div class="name_flex">';
  $rows['last_name']['row_after'] = '';
  $rows['first_name']['row_before'] = '';
  $rows['first_name']['row_after'] = '</div>';
  $rows['name_show_hidden']['row_before'] = '<div class="field_mini">';
  $rows['name_show_hidden']['row_after'] = '</div></div>';
	return $rows;
}
add_filter( 'wpmem_register_form_rows', 'my_login_form_rows', 10, 2 );


//コメント通知をサイト管理メールアドレスだけに通知
function se_comment_moderation_recipients( $emails, $comment_id ) {
$emails = array( get_option( 'admin_email' ) );

return $emails;
}
add_filter( 'comment_moderation_recipients', 'se_comment_moderation_recipients', 11, 2 );
add_filter( 'comment_notification_recipients', 'se_comment_moderation_recipients', 11, 2 );



// コメントが承認されたときに実行されるアクションフック
add_action('wp_set_comment_status', 'send_email_on_comment_approval', 10, 2);
function send_email_on_comment_approval($comment_id, $comment_status) {
    // コメントが承認された場合のみ処理を実行
    $comment_deny_flag = get_field('comment_delete_check', 'comment_'.$comment_id);
    if ('approve' !== $comment_status || $comment_deny_flag) {
        return;
    }

    // コメントデータを取得
    $comment = get_comment($comment_id);

    // 記事の投稿者の情報を取得
    $post = get_post($comment->comment_post_ID);
    $author_email = get_the_author_meta('user_email', $post->post_author);

    // コメントの投稿者のメールアドレスを取得
    $comment_author_email = $comment->comment_author_email;
    $comment_author_info = get_user_by( 'email', $comment_author_email );
    $comment_author_id = $comment_author_info->ID;

    // 記事の投稿者にメールを送信
    $subject = '【看護教育力UP↑コミュニティ】コメントがあります';
    $message = '公開中の記事にコメントがありました。下記よりご確認ください。' . "\n";
    $message .= get_permalink($post->ID) . "\n";
    // ▼ 20240205 fsta ikawa update
    // wp_mail($author_email, $subject, $message);
    if (check_user_expireddate_by_user_id($post->post_author)) {
        wp_mail($author_email, $subject, $message);
    }
    // ▲ 20240205 fsta ikawa update

    // メールの内容を設定
    if ($comment->comment_parent) {
        $subject = '【看護教育力UP↑コミュニティ】返信投稿が承認されました';
        $message = 'あなたの返信投稿が承認されました。下記よりご確認ください。' . "\n";
        $message .= get_permalink($post->ID) . "\n";
    } else {
        $subject = '【看護教育力UP↑コミュニティ】コメントが承認されました';
        $message = '投稿したコメントが承認されました。下記よりご確認ください。' . "\n";
        $message .= get_permalink($post->ID) . "\n";
    }

    // コメントの投稿者にメールを送信
    // ▼ 20240205 fsta ikawa update
    // wp_mail($author_email, $subject, $message);
    if (check_user_expireddate_by_user_id($post->post_author)) {
        wp_mail($author_email, $subject, $message);
    }
    // ▲ 20240205 fsta ikawa update
    $user_alert_checkbox = wpmem_get_user_meta($comment_author_id, 'user_alert_checkbox', true);
    if (preg_match('/コメント投稿状態/', $user_alert_checkbox)) {
        // ▼ 20240205 fsta ikawa update
        // wp_mail($comment_author_email, $subject, $message);
        if (check_user_expireddate_by_user_id($comment_author_id)) {
            wp_mail($comment_author_email, $subject, $message);
        }
        // ▲ 20240205 fsta ikawa update
    }

    // 親コメントが有る場合、親コメントのユーザーにも送信
    if ($comment->comment_parent) {
        $parent_comment = get_comment($comment->comment_parent);
        $parent_comment_author_email = $parent_comment->comment_author_email;
        // 返信が自分でなければ送信
        if ($comment_author_email != $parent_comment_author_email) {
            $subject = '【看護教育力UP↑コミュニティ】あなたのコメントに返信がありました';
            $message = "投稿したコメントに返信がありました。下記よりご確認ください。\n";
            $message .= get_permalink($post->ID) . "\n";
            $parent_comment_author_info = get_user_by( 'email', $parent_comment_author_email );
            $parent_comment_author_id = $parent_comment_author_info->ID;
            $user_alert_checkbox = wpmem_get_user_meta($parent_comment_author_id, 'user_alert_checkbox', true);
            if (preg_match('/コメント投稿状態/', $user_alert_checkbox)) {
                // ▼ 20240205 fsta ikawa update
                // wp_mail($parent_comment_author_email, $subject, $message);
                if (check_user_expireddate_by_user_id($parent_comment_author_id)) {
                    wp_mail($parent_comment_author_email, $subject, $message);
                }
                // ▲ 20240205 fsta ikawa update
            }
        }
    }

    // 全ユーザーにメール送信（お気に入りテーマに入れている場合）
    $users = get_users();
    foreach ( $users as $user ) {
        // 自分が書いたのであれば除外
        $user_favorite = get_user_favorites($user->ID);
        // お気に入りに入っている場合と自分が書いたのではないとき
        if (in_array($post->ID, $user_favorite) && $user->ID != $comment_author_id) {
            if ($comment->comment_parent) {
                $subject = '【看護教育力UP↑コミュニティ】返信投稿のお知らせ';
                $message = 'お気に入りテーマ記事に新しい返信投稿がありました。' . "\n";
                $message .= '下記よりご確認ください。' . "\n";
                $message .= get_permalink($post->ID) . "\n";
            } else {
                $subject = '【看護教育力UP↑コミュニティ】コメント投稿のお知らせ';
                $message = 'お気に入りテーマ記事に新しいコメントが投稿されました。' . "\n";
                $message .= '下記よりご確認ください。' . "\n";
                $message .= get_permalink($post->ID) . "\n";
            }
            $user_alert_checkbox = wpmem_get_user_meta($user->ID, 'user_alert_checkbox', true);
            if (preg_match('/コメント新着/', $user_alert_checkbox)) {
                // ▼ 20240205 fsta ikawa update
                // wp_mail($user->user_email, $subject, $message);
                if (check_user_expireddate_by_user_id($user->ID)) {
                    wp_mail($user->user_email, $subject, $message);
                }
                // ▲ 20240205 fsta ikawa update
            }
        }
    }
}


// コメントが承認されたときに実行されるアクションフック
add_action('edit_comment', 'send_email_on_comment_deny', 10, 2);
function send_email_on_comment_deny($comment_id, $data) {
    // コメントが否認された場合のみ処理を実行
    $comment_deny_flag = get_field('comment_delete_check', 'comment_'.$comment_id);
    if (!$comment_deny_flag) {
        return;
    }

    // コメントデータを取得
    $comment = get_comment($comment_id);

    // 記事の投稿者の情報を取得
    $post = get_post($comment->comment_post_ID);
    $author_email = get_the_author_meta('user_email', $post->post_author);

    // コメントの投稿者のメールアドレスを取得
    $comment_author_email = $comment->comment_author_email;
    $comment_author_info = get_user_by( 'email', $comment_author_email );
    $comment_author_id = $comment_author_info->ID;

    // メールの内容を設定
    if ($comment->comment_parent) {
        $subject = '【看護教育力UP↑コミュニティ】返信投稿が取り下げられました';
        $message = "下記理由によって返信投稿が取り下げられました。ご了承ください。\n"
                  . get_field('comment_delete_reason','comment_'.$comment_id);
    } else {
        $subject = '【看護教育力UP↑コミュニティ】コメントが取り下げられました';
        $message = "下記理由によってコメントが取り下げられました。\n"
                  . get_field('comment_delete_reason','comment_'.$comment_id);
    }

    // 記事の投稿者とコメントの投稿者にメールを送信
    $user_alert_checkbox = wpmem_get_user_meta($comment_author_id, 'user_alert_checkbox', true);
    if (preg_match('/コメント投稿状態/', $user_alert_checkbox)) {
        // ▼ 20240205 fsta ikawa update
        // wp_mail($comment_author_email, $subject, $message);
        if (check_user_expireddate_by_user_id($comment_author_id)) {
            wp_mail($comment_author_email, $subject, $message);
        }
        // ▲ 20240205 fsta ikawa update
    }
    // ▼ 20240205 fsta ikawa update
    // wp_mail($author_email, $subject, $message);
    if (check_user_expireddate_by_user_id($post->post_author)) {
        wp_mail($author_email, $subject, $message);
    }
    // ▲ 20240205 fsta ikawa update

  }


//パスワードリセットの文言変更
add_filter( 'wpmem_forgot_link_str', 'my_forgot_link_str', 10, 2 );
function my_forgot_link_str( $str, $link ) {
    return "パスワードを忘れた場合<a href=\"$link\" target=\"blank\">パスワード再発行</a>";
}


//パスワードリセットの通知メールを管理者に届けない
function disable_password_change_email() {
  remove_action( 'after_password_reset', 'wp_password_change_notification' );
}
add_action( 'init', 'disable_password_change_email' );


// 投稿が合ったときの処理
function send_email_on_new_post( $new_status, $old_status, $post ) {
    if (
        $new_status != 'publish'
        || $old_status == 'publish'
    ) {
        return;
    }
  // 投稿タイプに基づいてメールの内容を設定
  $email_addresses = array();
  // 通常の投稿の場合のメール設定
  if ( $post->post_type == 'news' ) {
      $subject = '【看護教育力UP↑コミュニティ】お知らせ新着';
      $message = '新しいお知らせがあります。下記よりご確認ください。' . "\n";
      $message .= get_permalink($post->ID) . "\n";

      $news_user_list = get_field('select_user', $post->ID);

      // お知らせを許可している全ユーザーのメールアドレスを取得
      $users = get_users();
      foreach ( $users as $user ) {
          if ($news_user_list) {
              $user_alert_checkbox = wpmem_get_user_meta($user->ID, 'user_alert_checkbox', true);
              if (preg_match('/お知らせ/', $user_alert_checkbox) && in_array($user->ID, $news_user_list)) {
                    // $email_addresses[] = $user->user_email;
                    // メールを送信
                    // ▼ 20240205 fsta ikawa update
                    // wp_mail( $user->user_email, $subject, $message );
                    if (check_user_expireddate_by_user_id($user->ID)) {
                        wp_mail( $user->user_email, $subject, $message );
                    }
                    // ▲ 20240205 fsta ikawa update
              }
          } else {
              $user_alert_checkbox = wpmem_get_user_meta($user->ID, 'user_alert_checkbox', true);
              if (preg_match('/お知らせ/', $user_alert_checkbox)) {
                    // メールを送信
                    // ▼ 20240205 fsta ikawa update
                    // wp_mail( $user->user_email, $subject, $message );
                    if (check_user_expireddate_by_user_id($user->ID)) {
                        wp_mail( $user->user_email, $subject, $message );
                    }
                    // ▲ 20240205 fsta ikawa update
              }
          }
      }
      // $message .= implode(',', $news_user_list) . "\n";
      // $message .= $user_alert_checkbox . "\n";
      // $message .= implode(',', $email_addresses) . "\n";
  } else if ( $post->post_type == 'post' ) {
      $subject = '【看護教育力UP↑コミュニティ】テーマ新着';
      $message = '新しいテーマ記事が配信されました。下記よりご確認ください。' . "\n";
      $message .= get_permalink($post->ID) . "\n";

      // お知らせを許可している全ユーザーのメールアドレスを取得
      $users = get_users();
      foreach ( $users as $user ) {
          $user_alert_checkbox = wpmem_get_user_meta($user->ID, 'user_alert_checkbox', true);
          if (preg_match('/テーマ新着/', $user_alert_checkbox)) {
                // メールを送信
                // ▼ 20240205 fsta ikawa update
                // wp_mail( $user->user_email, $subject, $message );
                if (check_user_expireddate_by_user_id($user->ID)) {
                    wp_mail( $user->user_email, $subject, $message );
                }
                // ▲ 20240205 fsta ikawa update
          }

      }
  }

}
// add_action( 'new_to_publish', 'send_email_on_new_post', 10, 3 );
// add_action( 'pending_to_publish', 'send_email_on_new_post', 10, 3 );
// add_action( 'draft_to_publish', 'send_email_on_new_post', 10, 3 );
// add_action( 'auto-draft_to_publish', 'send_email_on_new_post', 10, 3 );
// add_action( 'future_to_publish', 'send_email_on_new_post', 10, 3 );
// add_action( 'private_to_publish', 'send_email_on_new_post', 10, 3 );
// add_action( 'publish_news', 'send_email_on_new_post', 10, 3 );
add_action( 'transition_post_status', 'send_email_on_new_post', 10, 3 );


//wp_list_commentsコールバック
function wp_list_comments_default_callback($comment, $args, $depth) {
  // コメントの表示に関するデフォルトのマークアップがここに含まれます
  $comment_id = get_comment_ID();
  $school_show_hidden = get_user_meta($comment->user_id, 'school_show_hidden', true);
  $user_school_name_select = get_user_meta($comment->user_id, 'user_school_name_select', true);
  $position_show_hidden = get_user_meta($comment->user_id, 'position_show_hidden', true);
  $user_position = get_user_meta($comment->user_id, 'user_position', true);
  $area_show_hidden = get_user_meta($comment->user_id, 'area_show_hidden', true);
  $user_area = get_user_meta($comment->user_id, 'user_area', true);

  $user_nickname = get_user_meta($comment->user_id, 'user_nickname', true);
  $name_show_hidden = get_user_meta($comment->user_id, 'name_show_hidden', true);

  $last_name = get_user_meta($comment->user_id, 'last_name', true);
  $first_name = get_user_meta($comment->user_id, 'first_name', true);

  $school_name = show_school_name_by_shcool_id($user_school_name_select);
  ?>
  <li <?php comment_class(); ?> id="comment-<?php comment_ID(); ?>">
      <div id="comment-<?php comment_ID(); ?>" class="comment-body">
              <div class="comment_author vcard">
                  <?php
                  echo get_avatar($comment, 50);
                  ?>
              </div><!-- .comment-author -->

              <div class="comment_arrow_box">
              <div class="comment-meta commentmetadata">
              <p class="comment_time"><?php comment_date(); ?></p>
              <p class="name">
        <?php if ($name_show_hidden === '公開') :?>
          <?php echo esc_html($last_name); ?>  <?php echo esc_html($first_name); ?>
            <!-- <?php echo esc_html($comment->comment_author); ?> -->
        <?php else:?>
            <?php echo esc_html($user_nickname); ?>
        <?php endif;?>

            <?php if ($school_show_hidden === '公開') :?>
                <p class="school_name"><?php echo esc_html($school_name); ?></p>
            <?php endif;?>
            <?php if ($position_show_hidden === '公開') :?>
                <p class="position"><?php echo esc_html($user_position); ?></p>
            <?php endif;?>
            <?php if ($area_show_hidden === '公開') :?>
                <p class="area"><?php echo esc_html($user_area); ?></p>
            <?php endif;?>

            </div><!-- .comment-metadata -->

              <?php if ('0' == $comment->comment_approved) : ?>
                  <p class="comment-awaiting-moderation"><?php _e('Your comment is awaiting moderation.'); ?></p>
              <?php endif; ?>
          <div class="comment-content">
              <?php comment_text(); ?>
          </div><!-- .comment-content -->

          <div class="single_button_box ver_comment_btn">
          <?php echo do_shortcode('[wp_ulike for="comment" slug="comment" id="'.$comment_id.'" style="wpulike-heart" wrapper_class="reaction_btn nice_btn"]') ?>
          <?php echo do_shortcode('[wp_ulike for="comment" slug="comment" id="100000000'.$comment_id.'" style="wpulike-heart" wrapper_class="reaction_btn favorite_btn"]') ?>

        </div>

        </div>
        <!-- /.comment_arrow_box -->

      </div><!-- .comment-body -->
      <div class="reply comment-reply">
              <?php
              comment_reply_link(
                  array_merge(
                      $args,
                      array(
                          'add_below' => 'div-comment',
                          'depth'     => $depth,
                          'max_depth' => $args['max_depth'],
                          'reply_text' => '返信コメントを入力<br class="sp_only">(入力フォームへ移動します)',
                          )
                  )
              );
              ?>
          </div><!-- .comment-reply -->
  <?php
}

// ▼ fsta ikawa 20240219
define('ADOPTED_KBN_TEXT', 1);
define('ADOPTED_KBN_BENS', 4);
// ▲ fsta ikawa 20240219

// ▼ fsta ikawa 20240116

// 会員情報編集時に使用する、古い学校ID
$oldSchoolId = null;

// 会員登録編集時学校マスタバリデート
function add_school_validate( $fields ) {
    global $wpmem_themsg;
    global $wpdb;

    $oldSchoolId = get_user_meta($fields['ID'], 'user_school_name_select', true);

    // fsta ikawa 20240221 追加
    // 編集の場合、権限が「管理者」「編集者」「寄稿者」だったらチェックなし
    if (is_user_logged_in()) {
        $user = wp_get_current_user();
        if (in_array('administrator', $user->roles)
            || in_array('editor', $user->roles)
            || in_array('contributor', $user->roles)) {
                return;
        }
    }

    $msg = '教科書またはBeNs.の採用が必須となります。<br>誠に申し訳ございませんが、現在ご利用いただけません。';

    $schoolId = $fields['user_school_name_select'];
    $results = $wpdb->get_results( "
        SELECT sa.*
        FROM mkc_school_adoptions AS sa
        LEFT JOIN mkc_schools AS s ON sa.school_id = s.school_id
        where sa.school_id = '".$schoolId."'
        AND s.delete_flag = 0
    ");
    // 有効期限が過ぎているか、不採用ではないかチェック
    if (!$results || count($results) <= 0) {
        $wpmem_themsg = $msg;
    } elseif ($results && count($results) > 0) {
        $errorFlag = false;
        $arrAdoptedKbn = $results[0]->adopted_kbn ? explode(',', $results[0]->adopted_kbn) : null;
        $lockScreenUserExpiredDate = getNextMonthLastDayFormatYmd($results[0]->expired_date, 3);
        if (!$results[0]->expired_date || $lockScreenUserExpiredDate < date('Y-m-d')) {
            $errorFlag = true;
        // } elseif ($results[0]->adopted_kbn != 1 && $results[0]->adopted_kbn != 4) {
        } elseif (!in_array(ADOPTED_KBN_TEXT, $arrAdoptedKbn) && !in_array(ADOPTED_KBN_BENS, $arrAdoptedKbn)) {
            $errorFlag = true;
        }
        if ($errorFlag) {
            $wpmem_themsg = $msg;
        }
    }

    return $wpmem_themsg;
}
add_action( 'wpmem_pre_register_data', 'add_school_validate' );
add_action( 'wpmem_pre_update_data', 'add_school_validate' );

// ユーザ新規登録時にユーザ情報に有効期限とアラートチェックを追加する
function register_user_after_update_usermeta( $fields ) {
    global $wpdb;

    // 有効期限
    $meta  = 'user_expired_date';
    $schoolId = $fields['user_school_name_select'];
    $results = $wpdb->get_results( "
        SELECT sa.*
        FROM mkc_school_adoptions AS sa
        LEFT JOIN mkc_schools AS s ON sa.school_id = s.school_id
        where sa.school_id = '".$schoolId."'
        AND s.delete_flag = 0
    ");
    if ($results && count($results) > 0) {
        $arrAdoptedKbn = $results[0]->adopted_kbn ? explode(',', $results[0]->adopted_kbn) : null;
        // if ($results[0]->expired_date && ($results[0]->adopted_kbn == 1 || $results[0]->adopted_kbn == 4)) {
        if ($results[0]->expired_date && (in_array(ADOPTED_KBN_TEXT, $arrAdoptedKbn) || in_array(ADOPTED_KBN_BENS, $arrAdoptedKbn))) {
            $value = $results[0]->expired_date;
            update_user_meta( $fields['ID'], $meta, $value );
        }
    }

    // アラートチェック
    $meta = 'user_alert_check';
    // 新規登録時はアラートをOFFにする
//    update_user_meta( $fields['ID'], $meta, get_field('alert_check', 'options'));
    update_user_meta( $fields['ID'], $meta, '');

    return;
}
add_action( 'wpmem_post_register_data', 'register_user_after_update_usermeta' );

// ユーザ情報編集で学校が変更された場合は有効期限は更新、アラートチェックは空文字に更新
function update_user_after_update_usermeta( $fields) {
    global $wpdb;
    global $oldSchoolId;

    // // 学校ID
    // $schoolId =  get_user_meta($fields['ID'], 'user_school_name_select', true);

    // 学校IDが異なる場合、有効期限を更新
    if ($fields['user_school_name_select'] != $oldSchoolId) {
        $updateExpiredDate = null;
        $meta  = 'user_expired_date';
        $results = $wpdb->get_results( "
            SELECT sa.*
            FROM mkc_school_adoptions AS sa
            LEFT JOIN mkc_schools AS s ON sa.school_id = s.school_id
            WHERE sa.school_id = '".$fields['user_school_name_select']."'
            AND s.delete_flag = 0
        ");
        if ($results && count($results) > 0) {
            $arrAdoptedKbn = $results[0]->adopted_kbn ? explode(',', $results[0]->adopted_kbn) : null;
            // if ($results[0]->expired_date && ($results[0]->adopted_kbn == 1 || $results[0]->adopted_kbn == 4)) {
            if ($results[0]->expired_date && (in_array(ADOPTED_KBN_TEXT, $arrAdoptedKbn) || in_array(ADOPTED_KBN_BENS, $arrAdoptedKbn))) {
                $updateExpiredDate = $results[0]->expired_date;
            }
        }
        $updateExpiredDate = $updateExpiredDate ? $updateExpiredDate : '';
        update_user_meta( $fields['ID'], $meta, $updateExpiredDate );
    }
    // 古い学校IDをリセット
    $oldSchoolId = null;

    // アラートチェック
    $meta = 'user_alert_check';
    update_user_meta( $fields['ID'], $meta, '');


	return;
}
add_action( 'wpmem_post_update_data', 'update_user_after_update_usermeta', 10, 2);

// 管理画面でのユーザ情報編集で、学校が変更されたとき、有効期限も更新する
function update_user_expired_date_admin_page( $user_id) {
    global $wpdb;

    // 学校ID
    $schoolId =  get_user_meta($user_id, 'user_school_name_select', true);

    // 学校IDが異なる場合、有効期限を更新
    if ($_POST['user_school_name_select'] != $schoolId) {
        $updateExpiredDate = null;
        $meta  = 'user_expired_date';
        $results = $wpdb->get_results( "
            SELECT sa.*
            FROM mkc_school_adoptions AS sa
            LEFT JOIN mkc_schools AS s ON sa.school_id = s.school_id
            where sa.school_id = '".$_POST['user_school_name_select']."'
            AND s.delete_flag = 0
        ");
        if ($results && count($results) > 0) {
            $arrAdoptedKbn = $results[0]->adopted_kbn ? explode(',', $results[0]->adopted_kbn) : null;
            // if ($results[0]->expired_date && ($results[0]->adopted_kbn == 1 || $results[0]->adopted_kbn == 4)) {
            if ($results[0]->expired_date && (in_array(ADOPTED_KBN_TEXT, $arrAdoptedKbn) || in_array(ADOPTED_KBN_BENS, $arrAdoptedKbn))) {
                $updateExpiredDate = $results[0]->expired_date;
            }
        }
        $updateExpiredDate = $updateExpiredDate ? $updateExpiredDate : '';
        $_POST[$meta] = $updateExpiredDate;
    }

	return;
}
add_action( 'profile_update', 'update_user_expired_date_admin_page');

// 学校マスタをuser_school_name_selectのoptionに設定
function set_school_options( $rows, $tag ) {
    global $wpdb;

//    echo $_SESSION['validate_school_auth_code_school_id'];
//    echo $_SESSION['school_auth_code_school_id'];

    // ▼ fsta ikawa 20240515
    // 表示する学校を制限するか？
    $limitedSchoolsFlag = false;
    if (is_page('register')) {
        $limitedSchoolsFlag = true;
    } elseif (is_user_logged_in()) {
        // 権限が「管理者」「編集者」「寄稿者」の場合チェックなし
        if (isLockScreen()) {
            $limitedSchoolsFlag = true;
        }
    }
    $schoolId = null;
    if ($limitedSchoolsFlag) {
        // 認証コードから取得学校IDを取得
        $schoolId = isset($_SESSION['school_auth_code_school_id'])
                ? $_SESSION['school_auth_code_school_id']
                : (isset($_SESSION['validate_school_auth_code_school_id'])
                    ? $_SESSION['validate_school_auth_code_school_id']
                    : null);
    }
    // ▲ fsta ikawa 20240515

    $values = ['選択してください|'];
    // ▼ fsta ikawa 20240515
    if ($schoolId) {
        $results = $wpdb->get_results($wpdb->prepare("SELECT * FROM mkc_schools WHERE school_id = %s AND delete_flag = 0", $schoolId));
    } else {
        $results = $wpdb->get_results("SELECT * FROM mkc_schools WHERE delete_flag = 0");
    }
    // ▲ fsta ikawa 20240515
    if ($results && count($results) > 0) {
        foreach ($results as $row) {
            // ▼ fsta ikawa update 20240522
//            $values[] = esc_html($row->school_name).'|'.esc_attr($row->school_id);
            $option = esc_html($row->school_name).'|'.esc_attr($row->school_id);
            if ($schoolId && $schoolId == $row->school_id) {
                $option .= '|selected';
            }
            $values[] = $option;
            // ▲ fsta ikawa update 20240522
        }
    }
    foreach ($rows as $key => &$row) {
        if ($key == 'user_school_name_select') {
            $row['values'] = $values;
            $row[7] = $values;
        }
    }
    return $rows;
}
add_filter( 'wpmem_fields', 'set_school_options', 10, 2 );

// 学校名表示ショートコード
function show_school_name() {
    global $wpdb;

    $userData = wp_get_current_user();
    $userId = $userData->ID;
    $schoolId = get_user_meta($userId, 'user_school_name_select', true);

    if ($schoolId) {
        $results = $wpdb->get_results( "SELECT * FROM mkc_schools where school_id = '".$schoolId."'");
        if ($results && count($results) > 0) {
            return $results[0]->school_name;
        }
    }
    return '';
}
add_shortcode('show_school_name', 'show_school_name');

// 学校名表示ショートコード ※wp_queryで回す用
function show_school_name_02() {
    global $wpdb;

    $userData = new WP_User( get_the_author_meta( 'ID' ) );
    $userId = get_the_author_meta('ID');
    $schoolId = get_user_meta($userId, 'user_school_name_select', true);

    if ($schoolId) {
        $results = $wpdb->get_results( "SELECT * FROM mkc_schools where school_id = '".$schoolId."'");
        if ($results && count($results) > 0) {
            return $results[0]->school_name;
        }
    }
    return '';
}
add_shortcode('show_school_name_02', 'show_school_name_02');


// ▼ fsta ikawa update 20240130
// 学校名表示 by 学校ID
function show_school_name_by_shcool_id($attr) {
    global $wpdb;
    // ▼ fsta ikawa update 20240705 start ------------------------
//    if ($attr[0]) {
//        $results = $wpdb->get_results( "SELECT * FROM mkc_schools where school_id = '".$attr[0]."'");
//        if ($results && count($results) > 0) {
//            return $results[0]->school_name;
//        }
//    }
    if (is_array($attr) && isset($attr[0])) {
        $attr = $attr[0];
    }
    if ($attr) {
        $results = $wpdb->get_results( "SELECT * FROM mkc_schools where school_id = '".$attr."'");
        if ($results && count($results) > 0) {
            return $results[0]->school_name;
        }
    }
    // ▲ fsta ikawa update 20240705 end --------------------------
    return '';
}
add_shortcode('show_school_name_by_shcool_id', 'show_school_name_by_shcool_id');
// ▲ fsta ikawa update 20240130

// 会員情報に有効期限、初回登録日（採用日）、採用教科書一覧を表示
function show_expired_date_and_adopted_info( $rows, $tag ) {
    global $wpdb;

    if ($tag == 'edit') {
        $userData = wp_get_current_user();
        $userId = $userData->ID;
        $schoolId = get_user_meta($userId, 'user_school_name_select', true);

        $field = '<h3>初回登録</h3> ';
        $adoptions = $wpdb->get_results( "SELECT * FROM mkc_school_adoptions where school_id = '".$schoolId."'");
        if ($adoptions && count($adoptions) > 0) {
            $field .= $adoptions[0]->adopted_date;
        }
        $field .= '<br>';
        $field .= '<br>';

        $field .= '<h3>有効期限</h3>';
        $userExpiredDate = get_user_meta($userId, 'user_expired_date', true);
        if ($userExpiredDate) {
            $field .= $userExpiredDate;
        }

        // $field .= '<br>';
        // $field .= '<br>';

        // $field .= '<h3>採用商品</h3>';
        // $items = $wpdb->get_results( "SELECT * FROM mkc_school_items where school_id = '".$schoolId."'");
        // if ($items && count($items) > 0) {
        //     $field .= '<ul style="list-style: none;">';
        //     foreach ($items as $item) {
        //         $field .= '<li>'.$item->item_name.'</li>';
        //     }
        //     $field .= '</ul>';
        // }

        $adopted_date['info'] = array(
            'order' => '',
            'meta' => '',
            'type' => '',
            'value' => '',
            'row_before' => '<div class="form_custom_flex">',
            'label' => '',
            'field_before' => '<div class="div_text">',
            'field' => $field,
            'field_after' => '</div>',
            'row_after' => '</div>'
        );
        $rows = wpmem_array_insert( $rows, $adopted_date, 'user_alert_checkbox', 'after');
    }
    return $rows;
}
add_filter( 'wpmem_register_form_rows', 'show_expired_date_and_adopted_info', 10, 2 );

// 管理画面ログインページか否か？
function is_login_page() {
    return in_array($GLOBALS['pagenow'], array('wp-login.php', 'wp-register.php'));
}
// ログイン失敗エラーメッセージに有効期限の文言に変更。
function expiredate_login_failed( $str )
{
    $str ='看護教育力UP↑コミュニティ」先行オープン期間は<br>2024年1月31日をもって終了となりました。<br>誠に申し訳ございませんが、現在ご利用いただけません。';
	return $str;
}
// 有効期限のログインバリデート
// function check_expiredate( $user) {
//     if ( is_wp_error( $user ) ) {
//         return $user;
//     }
//     if (!wp_check_password($_POST['pwd'], $user->user_pass, $user->id)) {
//         return $user;
//     }

//     $userExpiredDate = wpmem_get_user_meta($user->id, 'user_expired_date');
//     if (!is_login_page()) {
//         if (!$userExpiredDate|| $userExpiredDate < date('Y-m-d')) {
//             $message = esc_html__( 'exceed expired date', 'wp-members');
//             add_filter('wpmem_login_failed', 'expiredate_login_failed');
//             return new WP_Error( 'login_failed', $message );
//         }
//     }
//     return $user;
// }
// add_filter( 'wp_authenticate_user', 'check_expiredate', 99, 3 );
// ▼ fsta ikawa 20240214
function check_login_expiredate( $user) {
    if ( is_wp_error( $user ) ) {
        return $user;
    }
    if (!wp_check_password($_POST['pwd'], $user->user_pass, $user->id)) {
        return $user;
    }

    $userExpiredDate = wpmem_get_user_meta($user->id, 'user_expired_date');
    if (!is_login_page()) {
        // 権限が「管理者」「編集者」「寄稿者」の場合チェックなし
        if (in_array('administrator', $user->roles)
            || in_array('editor', $user->roles)
            || in_array('contributor', $user->roles)) {
        } else {
            $lockScreenUserExpiredDate = getNextMonthLastDayFormatYmd($userExpiredDate, 3);
            if (!$userExpiredDate || $lockScreenUserExpiredDate < date('Y-m-d')) {
                $message = esc_html__( 'exceed expired date', 'wp-members');
                add_filter('wpmem_login_failed', 'expiredate_login_failed');
                return new WP_Error( 'login_failed', $message );
            }
        }
    }
    return $user;
}
add_filter( 'wp_authenticate_user', 'check_login_expiredate', 99, 3 );
    // ▲ fsta ikawa 20240214
// ▲ fsta ikawa 20240116


// ▼ fsta ikawa 20240202
// メールで学校名を表示
add_filter( 'wpmem_email_shortcodes', function ( $shortcodes, $tag, $user_id ) {
    global $wpdb;

    $schoolId = get_user_meta($user_id, 'user_school_name_select', true);

    $schoolName = '';

    if ($schoolId) {
        $results = $wpdb->get_results( "SELECT * FROM mkc_schools where school_id = '".$schoolId."'");
        if ($results && count($results) > 0) {
            $schoolName = $results[0]->school_name;
        }
    }

    // This is an example of a shortcode replaced with a user meta field (by user ID).
    $shortcodes['email_show_school_name'] = $schoolName;

    return $shortcodes;

}, 10, 3 );
// ▲ fsta ikawa 20240202

// ▼ fsta ikawa 20240205
// 指定ユーザIDからのユーザ有効期限チェック
function check_user_expireddate_by_user_id($userId) {

    // 権限が「管理者」「編集者」「寄稿者」の場合チェックなし
    $user = get_userdata($userId);
    if (in_array('administrator', $user->roles)
        || in_array('editor', $user->roles)
        || in_array('contributor', $user->roles)) {
            return true;
    } else {
        $userExpiredDate = get_user_meta($userId, 'user_expired_date', true);
        if ($userExpiredDate && $userExpiredDate >= date('Y-m-d')) {
            return true;
        }
    }

    return false;
}
// ▲ fsta ikawa 20240205

// 寄稿者と購読者のコメントのみを非承認にする
function custom_comment_approval($approved) {
    if (current_user_can('contributor') || current_user_can('subscriber')) {
        return '0';
    }
    return $approved;
}
add_filter('pre_comment_approved', 'custom_comment_approval');


// ▼ fsta ikawa 20240202
// セッション、クッキーでログインした状態で、フロント会員マイページにアクセスした際の権限制御
function check_member_permissions() {
    // ログインしているかどうかを確認
    if (is_user_logged_in()) {
        // ログイン中のユーザーの権限を取得
        $user = wp_get_current_user();

        // 権限が「管理者」「編集者」「寄稿者」の場合チェックなし
        if (in_array('administrator', $user->roles)
                || in_array('editor', $user->roles)
                || in_array('contributor', $user->roles)) {
        } else {
            // 会員の場合は有効期限をチェック。超過の場合ログアウトさせる
            // 学校の削除フラグはチェックしない
            $lockScreenUserExpiredDate = getNextMonthLastDayFormatYmd($user->user_expired_date, 3);
            if (!$user->user_expired_date || $lockScreenUserExpiredDate < date('Y-m-d')) {
                wp_logout();
                wp_redirect(home_url());
                exit();
            }
        }
    }
}
// wp-membersの会員マイページにアクセス時に実行
add_action('wpmem_after_init', 'check_member_permissions');




// 指定年月から指定月数を加算して末日を取得
function getNextMonthLastDayFormatYmd ($ymd, $addMonth=1) {

    // 1日を取得
    $baseDate = date('Y-m-01', strtotime($ymd));

    // 指定加算月より１日戻すので１月分加算する。
    $addMonth += 1;

    // 指定された日付の翌月の月初日を取得
    $nextMonthFirstDay = date('Y-m-01', strtotime("+$addMonth month", strtotime($baseDate)));

    // 指定された日付の翌月の月初日から1日前を取得し、それが翌月の月末日になります
    $nextMonthLastDay = date('Y-m-d', strtotime('-1 day', strtotime($nextMonthFirstDay)));

    // 結果を出力
    return $nextMonthLastDay;
}

// ▲ fsta ikawa 20240202

// ▼ fsta ikawa 20240123
function init_session_start() {

    // セッションが開始されていなければここで開始
    if( session_status() !== PHP_SESSION_ACTIVE ) {

        session_start();
    }
}
add_action( 'init', 'init_session_start' );

// 管理画面メニューに学校マスタ登録を追加
function add_schools_menu_new()
{
    add_menu_page(
        '学校マスタ',
        '学校マスタ',
        'manage_options',
        'schools_csv_upload',
        'schools_csv_form',
        'dashicons-welcome-learn-more',
        4
    );
}
add_action('admin_menu', 'add_schools_menu_new');
// 学校マスタ登録フォーム
function school_new_form()
{
?>
    <div class="wrap">
        <h2>学校マスタ</h2>
    </div>
<?php
}
// 管理画面メニューに採用情報登録を追加
function add_school_adoptions_menu_new()
{
    add_submenu_page(
        'schools_csv_upload',
        '採用情報管理',
        '採用情報管理',
        'manage_options',
        'school_adoptions_csv_upload',
        'school_adoptions_csv_form',
        1
    );
}
add_action('admin_menu', 'add_school_adoptions_menu_new');
// // 管理画面メニューに採用情報更新を追加
// function add_school_adoptions_menu_update()
// {
//     add_submenu_page(
//         'schools_new',
//         '採用情報更新',
//         '採用情報更新',
//         'manage_options',
//         'school_adoptions_update',
//         'school_adoptions_csv_form_update',
//         1
//     );
// }
// add_action('admin_menu', 'add_school_adoptions_menu_update');

// 学校マスタ登録更新フォーム
function schools_csv_form()
{
    $arrMessages = [];
    if (isset($_SESSION['school_csv_messages'])) {
        $arrMessages = $_SESSION['school_csv_messages'];
        unset($_SESSION["school_csv_messages"]);

        foreach ($arrMessages as $type => $messages) {
            if ($type == 'register') {
                echo '<h4>登録データ</h4>';
            } elseif ($type == 'update') {
                echo '<h4>更新データ</h4>';
            } elseif ($type == 'error') {
                echo '<h4>エラーデータ</h4>';
            }
            if (count($messages) > 0) {
                echo '<ul>';
                foreach ($messages as $message) {
                    echo '<li>';
                    echo $message;
                    echo '</li>';
                }
                echo '</ul>';
            } else {
                echo '<div>なし</div>';
            }
        }
    }
?>
    <div class="wrap">
        <h1>学校マスタ</h1>

        <h2>学校マスタ登録更新CSVアップロード</h2>
        <form id="schools_csv" enctype="multipart/form-data" method="POST" action="<?php echo admin_url('admin-post.php') ?>">
            <?php wp_nonce_field('schools_csv_action', 'schools_csv_nonce'); // csrf ?>
            <input type="hidden" name="action" value="schools_csv_action">
            <input type="file" name="csv_schools" accept=".csv" />
            <button type="submit">ファイルを送信する</button>
        </form>
        <!-- // ▼ 20240326 fsta ikawa add -->
        <br>
        <br>
        <h2>学校マスタCSVダウンロード</h2>
        <form id="schools_csv_download_action" enctype="multipart/form-data" method="POST" action="<?php echo admin_url('admin-post.php') ?>">
            <?php wp_nonce_field('schools_csv_download_action', 'schools_csv_download_nonce'); // csrf ?>
            <button type="submit" name="schools_csv_download_submit">CSVダウンロード</button>
        </form>
        <!-- // ▲ 20240326 fsta ikawa add -->
    </div>
<?php
}
// 学校マスタ登録更新CSVアクション
function schools_csv_action() {
    global $wpdb;

    // フォームがPOSTされたか確認
    if (isset($_POST['action']) && $_POST['action'] == 'schools_csv_action') {

        // setLocale(LC_ALL, 'English_United States.1252');

        // var_dump($_POST);
        // var_dump($_FILES);

        // nonceチェック
        if (isset($_POST['schools_csv_nonce']) && wp_verify_nonce($_POST['schools_csv_nonce'], 'schools_csv_action')) {

            setLocale(LC_ALL, 'English_United States.1252');

            $file_tmp_name = $_FILES["csv_schools"]["tmp_name"];
            $file_name = $_FILES["csv_schools"]["name"];

            if (is_uploaded_file($_FILES["csv_schools"]["tmp_name"])) {
                if (pathinfo($file_name, PATHINFO_EXTENSION) != 'csv') {
                    die('Only CSV files are supported.');
                } else {
                    // ファイルの一時的な保存先
                    $upload_dir = wp_upload_dir();
                    $target_dir = $upload_dir['basedir'] . '/csv_uploads/';
                    $target_path = $target_dir . basename($file_name);

                    // ディレクトリが存在しない場合は作成
                    if (!file_exists($target_dir)) {
                        mkdir($target_dir, 0755, true);
                    }

                    // ファイルを一時的に保存
                    $r = move_uploaded_file($file_tmp_name, $target_path);
                    if ($r) {
                        $fp = fopen($target_path, "r");

                        // テーブル名
                        $table = $wpdb->prefix . 'schools';

                        // トランザクション開始
                        $wpdb->query('START TRANSACTION');
                        $wpdb->query("LOCK TABLES $table WRITE");

                        $lineCount = 0;

                        $arrSchoolDepartmentCode = [];

                        $registerMessage = [];
                        $updateMessage = [];
                        $errorMessage = [];
                        try {
                            while (($record = fgetcsv($fp, 0, ",")) !== FALSE) {
                                // $asins[] = $data;

                                if($lineCount == 0){
                                    $lineCount++;
                                    continue;
                                }

                                $record = mb_convert_encoding($record, 'UTF-8', 'SJIS');

                                // データ数のチェック
                                if ($record && count($record) == 9) {

                                    // 読み込んだデータから空白を除去
                                    $schooCode = str_pad(trim($record[0]), 10, '0', STR_PAD_LEFT);              // 学校コード
                                    $schoolName = trim($record[1]);                 // 学校名
                                    $schoolDepartmentCode = str_pad(trim($record[2]), 5, '0', STR_PAD_LEFT);    // 学科コード
                                    $schoolDepartmentName = trim($record[3]);       // 学科名
                                    $schoolType = trim($record[4]);                 // 学校_種別(名称)
                                    $schoolType1_1 = trim($record[5]);              // 学校_種別1-1(名称)
                                    $schoolType1_2 = trim($record[6]);              // 学校_種別1-2(名称)
                                    $schoolType2 = trim($record[7]);                // 学校_種別2
                                    $deleteFlag = trim($record[8]);

                                    // バリデート
                                    if ($schooCode && mb_strlen($schooCode) > 10) {
                                        throw new Exception('学校コードは10桁までです。');
                                    }
                                    if (!$schoolName) {
                                        throw new Exception('学校名は必須項目です。');
                                    }
                                    if (!$schoolDepartmentCode) {
                                        throw new Exception('学科コードは必須項目です。');
                                    } elseif (mb_strlen($schoolDepartmentCode) > 5) {
                                        throw new Exception('学科コードは5桁までです。');
                                    }
                                    if (($schoolName && mb_strlen($schoolName) > 255)
//                                        || ($schooCode && mb_strlen($schooCode) > 255)
//                                        || ($schoolDepartmentCode && mb_strlen($schoolDepartmentCode) > 255)
                                        || ($schoolDepartmentName && mb_strlen($schoolDepartmentName) > 255)
                                        || ($schoolType && mb_strlen($schoolType) > 255)
                                        || ($schoolType1_1 && mb_strlen($schoolType1_1) > 255)
                                        || ($schoolType1_2 && mb_strlen($schoolType1_2) > 255)
                                        || ($schoolType2 && mb_strlen($schoolType2) > 255)) {
                                        throw new Exception('文字数が255文字を超えた項目があります。');
                                    }
                                    if (!is_numeric($deleteFlag) || ($deleteFlag != 0 && $deleteFlag != 1)) {
                                        throw new Exception('削除フラグは0（有効）か1（削除）を設定していください。');
                                    }

                                    // 学科コードがCSVファイル内で重複していないかチェック
                                    $dupulicateIndex = array_search($schoolDepartmentCode, $arrSchoolDepartmentCode);
                                    if ($dupulicateIndex) {
                                        throw new Exception($dupulicateIndex.'行目と'.$lineCount.'行目の学科コードが重複しています。');
                                    }
                                    $arrSchoolDepartmentCode[$lineCount] = $schoolDepartmentCode;


                                    // 学校ID取得
                                    // $schoolId = $wpdb->get_var($wpdb->prepare("SELECT school_id FROM $table WHERE school_name = %s", $schoolName));
                                    // $schoolId = $wpdb->get_var($wpdb->prepare("SELECT school_id FROM $table WHERE school_department_code = %s", $schoolDepartmentCode));
                                    $schoolId = null;
                                    $results = $wpdb->get_results($wpdb->prepare("SELECT school_id FROM $table WHERE school_department_code = %s", $schoolDepartmentCode)); //クエリを実行
                                    if (count($results) > 1) {
                                        throw new Exception('データベース上で学科コードが重複しています。システム管理者にお問い合わせてください。');
                                    } elseif (count($results) == 1) {
                                        $schoolId = $results[0]->school_id;
                                    }
                                    // define( 'SAVEQUERIES', true );
                                    // print_r( $wpdb->queries );


                                    if ($schoolId) {
                                        // 学校IDが存在する場合更新
                                        // 削除フラグが立っても所属する会員の有効期限は変更しない

                                        // フィールド
                                        $updateParam = array(
                                            'school_code' => $schooCode,                        // 学校コード
                                            'school_name' => $schoolName,                       // 学校名
                                            // 'school_department_code' => $schoolDepartmentCode,  // 学科コード
                                            'school_department_name' => $schoolDepartmentName,  // 学科名
                                            'school_type' => $schoolType,                       // 学校_種別(名称)
                                            'school_type1_1' => $schoolType1_1,                 // 学校_種別1-1(名称)
                                            'school_type1_2' => $schoolType1_2,                 // 学校_種別1-2(名称)
                                            'school_type2' => $schoolType2,                     // 学校_種別2
                                            'updated_at' => date('Y-m-d H:i:s'),
                                            'delete_flag' => $deleteFlag,
                                        );

                                        // 更新条件
                                        $where = array(
                                            'school_id' => $schoolId,
                                        );

                                        // 更新
                                        if($wpdb->update($table, $updateParam, $where) !== false) {
                                            $updateMessage[] = $lineCount.'行目 学科コード：'.$schoolDepartmentCode.', 学校名：'.$schoolName;
                                        } else {
                                            throw new Exception('学科コード：'.$schoolDepartmentCode.', 学校名：'.$schoolName.' で更新に失敗しました。');
                                        }


                                    } else {
                                        // 学校IDが存在しない場合登録

                                        // フィールド
                                        $insertParam = array(
                                            'school_code' => $schooCode,                        // 学校コード
                                            'school_name' => $schoolName,                       // 学校名
                                            'school_department_code' => $schoolDepartmentCode,  // 学科コード
                                            'school_department_name' => $schoolDepartmentName,  // 学科名
                                            'school_type' => $schoolType,                       // 学校_種別(名称)
                                            'school_type1_1' => $schoolType1_1,                 // 学校_種別1-1(名称)
                                            'school_type1_2' => $schoolType1_2,                 // 学校_種別1-2(名称)
                                            'school_type2' => $schoolType2,                     // 学校_種別2
                                            'updated_at' => date('Y-m-d H:i:s'),
                                            'delete_flag' => $deleteFlag,
                                        );


                                        // 登録
                                        if($wpdb->insert($table, $insertParam) !== false) {
                                            $registerMessage[] = $lineCount.'行目 学校名：'.$record[1];
                                        } else {
                                            throw new Exception('学科コード：'.$schoolDepartmentCode.', 学校名：'.$schoolName.' で登録に失敗しました。');
                                        }
                                    }
                                } else {
                                    throw new Exception('レコードが不正か、レコードのカラム数が一致しません');
                                }

                                $lineCount++;
                            }

                            fclose($fp);

                            // 一時的に保存したファイルを削除
                            unlink($target_path);

                            // コミット
                            $wpdb->query('COMMIT');

                        } catch (Exception $e) {
                            $errorMessage[] = $lineCount.' 行目のデータのアップロードに失敗しました。（'.$e->getMessage().'）';
                            $registerMessage = array();
                            $updateMessage = array();
                            $wpdb->query('ROLLBACK');
                        } finally {
                            // テーブルのロックを解除
                            $wpdb->query('UNLOCK TABLES');
                        }

                    } else {
                        die('CSVファイルの保存に失敗しました。.');
                    }

                }
            }
        } else {
            // Nonceが無効な場合の処理
            die('Security check failed!');
        }

        $_SESSION['school_csv_messages']['register'] = $registerMessage;
        $_SESSION['school_csv_messages']['update'] = $updateMessage;
        $_SESSION['school_csv_messages']['error'] = $errorMessage;

        // 保存が完了したらリダイレクト
        wp_redirect(admin_url('admin.php?page=schools_csv_upload'));
        exit();
    }
}
add_action('admin_post_schools_csv_action', 'schools_csv_action');

// 採用情報登録更新フォーム
function school_adoptions_csv_form()
{
    $arrMessages = [];
    if (isset($_SESSION['school_csv_messages'])) {
        $arrMessages = $_SESSION['school_csv_messages'];
        unset($_SESSION["school_csv_messages"]);

        foreach ($arrMessages as $type => $messages) {
            if ($type == 'register') {
                echo '<h4>登録データ</h4>';
            } elseif ($type == 'update') {
                echo '<h4>更新データ</h4>';
            } elseif ($type == 'error') {
                echo '<h4>エラーデータ</h4>';
            }
            if (count($messages) > 0) {
                echo '<ul>';
                foreach ($messages as $message) {
                    echo '<li>';
                    echo $message;
                    echo '</li>';
                }
                echo '</ul>';
            } else {
                echo '<div>なし</div>';
            }
        }
    }
?>
    <div class="wrap">
        <h1>採用情報管理</h1>

        <h2>採用情報登録更新CSVアップロード</h2>
        <form id="school_adoptions_csv" enctype="multipart/form-data" method="POST" action="<?php echo admin_url('admin-post.php') ?>">
            <?php wp_nonce_field('school_adoptions_csv_action', 'school_adoptions_csv_nonce'); // csrf ?>
            <input type="hidden" name="action" value="school_adoptions_csv_action">
            <input type="file" name="csv_school_adoptions" accept=".csv" />
            <button type="submit">ファイルを送信する</button>
        </form>

        <!-- // ▼ 20240326 fsta ikawa add -->
        <br>
        <br>
        <h2>採用情報CSVダウンロード</h2>
        <form id="school_adoptions_csv_download_action" enctype="multipart/form-data" method="POST" action="<?php echo admin_url('admin-post.php') ?>">
            <?php wp_nonce_field('school_adoptions_csv_download_action', 'school_adoptions_csv_download_nonce'); // csrf ?>
            <button type="submit" name="school_adoptions_csv_download_submit">CSVダウンロード</button>
        </form>
        <!-- // ▲ 20240326 fsta ikawa add -->
    </div>
<?php
}
// 採用情報登録更新アクション
function school_adoptions_csv_action() {
    global $wpdb;

    // フォームがPOSTされたか確認
    if (isset($_POST['action']) && $_POST['action'] == 'school_adoptions_csv_action') {

        // setLocale(LC_ALL, 'English_United States.1252');

        // nonceチェック
        if (isset($_POST['school_adoptions_csv_nonce']) && wp_verify_nonce($_POST['school_adoptions_csv_nonce'], 'school_adoptions_csv_action')) {

            setLocale(LC_ALL, 'English_United States.1252');

            $file_tmp_name = $_FILES["csv_school_adoptions"]["tmp_name"];
            $file_name = $_FILES["csv_school_adoptions"]["name"];

            if (is_uploaded_file($_FILES["csv_school_adoptions"]["tmp_name"])) {
                if (pathinfo($file_name, PATHINFO_EXTENSION) != 'csv') {
                    die('Only CSV files are supported.');
                } else {
                    // ファイルの一時的な保存先
                    $upload_dir = wp_upload_dir();
                    $target_dir = $upload_dir['basedir'] . '/csv_uploads/';
                    $target_path = $target_dir . basename($file_name);

                    // ディレクトリが存在しない場合は作成
                    if (!file_exists($target_dir)) {
                        mkdir($target_dir, 0755, true);
                    }

                    // ファイルを一時的に保存
                    $r = move_uploaded_file($file_tmp_name, $target_path);
                    if ($r) {
                        $fp = fopen($target_path, "r");

                        // テーブル名
                        $table1 = $wpdb->prefix . 'schools';
                        $table2 = $wpdb->prefix . 'school_adoptions';
                        $tblUser = $wpdb->prefix . 'usermeta';

                        // トランザクション開始
                        $wpdb->query('START TRANSACTION');
                        $wpdb->query("LOCK TABLES $table2 WRITE, $table1 WRITE, $tblUser WRITE");

                        $lineCount = 0;

                        $registerMessage = [];
                        $updateMessage = [];
                        $errorMessage = [];

                        $arrAuthCodeDepartmentCode = [];

                        try {

                            while (($record = fgetcsv($fp, 0, ",")) !== FALSE) {
                                // $asins[] = $data;

                                if($lineCount == 0){
                                    $lineCount++;
                                    continue;
                                }

                                $record = mb_convert_encoding($record, 'UTF-8', 'SJIS');

                                // データ数のチェック
                                if ($record && count($record) == 8) {

                                    // 読み込んだデータから空白を除去
                                    $adoptedKbn = trim($record[0]);     // 採用区分
                                    $schoolCode = trim($record[1]);     // 学校コード
                                    $schoolName = trim($record[2]);     // 学校名
                                    $schoolDepartmentCode = trim($record[3]);   // 学科コード
                                    $schoolDepartmentName = trim($record[4]);   // 学科名
                                    $adoptedDate = trim($record[5]);    // 採用日付
                                    $expiredDate = trim($record[6]);    // 有効期限
                                    $authCode = trim($record[7]);    // 認証コード

                                    // バリデート
                                    // 採用区分チェック
                                    if ($adoptedKbn && !is_numeric($adoptedKbn)) {
                                        throw new Exception('採用区分は数値で入力してください。');
                                    }
                                    // 学校コード
                                    if ($schoolCode && !is_numeric($schoolCode)) {
                                        throw new Exception('学校コードは数値で入力してください。');
                                    }
                                    // 学校名
                                    if (!$schoolName) {
                                        throw new Exception('学校名は必須項目です。');
                                    }
                                    // 学科コード
                                    if ($schoolDepartmentCode && !is_numeric($schoolDepartmentCode)) {
                                        throw new Exception('学科コードは数値で入力してください。');
                                    }
                                    // 採用日付チェック
                                    if($adoptedDate && !validateDate($adoptedDate)){
                                        throw new Exception('採用日付が不正、または存在しない日付です。「yyyy-mm-dd」または「yyyy/mm/dd」の形式で入力してください。（例：2024-04-01、2024/04/01）');
                                    }
                                    // 有効期限チェック
                                    if($expiredDate && !validateDate($expiredDate)){
                                        throw new Exception('有効期限が不正、または存在しない日付です。「yyyy-mm-dd」または「yyyy/mm/dd」の形式で入力してください。（例：2024-04-01、2024/04/01）');
                                    }
                                    // 認証コード
                                    if (!$authCode) {
                                        throw new Exception('認証コードは必須項目です。');
                                    } else {
                                        // 認証コードがCSVファイル内で重複していないかチェック
                                        $dupulicateIndex = array_search($authCode, $arrAuthCodeDepartmentCode);
                                        if ($dupulicateIndex) {
                                            throw new Exception($dupulicateIndex.'行目と'.$lineCount.'行目の認証コードが重複しています。');
                                        }
                                        $arrAuthCodeDepartmentCode[$lineCount] = $authCode;
                                    }

                                    // 学校マスタからの学校ID取得
                                    $schoolId = $wpdb->get_var($wpdb->prepare("SELECT school_id FROM $table1 WHERE school_name = %s AND delete_flag = 0", $schoolName));
                                    // 学校IDが存在しない場合エラー
                                    if (!$schoolId) {
                                        throw new Exception('学校マスタに「'.$schoolName.'」が存在しません。※削除された学校の採用情報を登録する場合、学校マスタで削除フラグを0に変更してください。');
                                    } else {

                                        // 採用情報を取得
                                        // $schoolAdotionId = $wpdb->get_var($wpdb->prepare("SELECT school_adoption_id FROM $table2 WHERE school_id = %s", $schoolId));
                                        $schoolAdotionId = null;
                                        $results = $wpdb->get_results($wpdb->prepare("SELECT school_adoption_id FROM $table2 WHERE school_id = %s", $schoolId)); //クエリを実行
                                        if (count($results) > 1) {
                                            throw new Exception('データベース上で学校の採用情報が重複しています。システム管理者にお問い合わせてください。');
                                        } elseif (count($results) == 1) {
                                            $schoolAdotionId = $results[0]->school_adoption_id;
                                        }

                                        // 認証コードが既存データと重複していないかチェック
                                        $results2 = $wpdb->get_results($wpdb->prepare("SELECT school_adoption_id FROM $table2 WHERE school_id <> %s AND auth_code = %s", [$schoolId, $authCode])); //クエリを実行
                                        if (count($results2) >= 1) {
                                            throw new Exception('データベース上で認証コードが重複しています。システム管理者にお問い合わせてください。');
                                        }

                                        // 日付関連を0埋め
                                        $datetime = new DateTime($adoptedDate);
                                        $adoptedDate = $datetime->format('Y-m-d');
                                        $datetime = new DateTime($expiredDate);
                                        $expiredDate = $datetime->format('Y-m-d');

                                        // 採用情報が存在する場合、更新
                                        if ($schoolAdotionId) {
                                            // フィールド
                                            $updateParam = array(
                                                'adopted_date' => $adoptedDate,     // 採用日付
                                                'adopted_kbn' => $adoptedKbn,       // 採用区分
                                                'expired_date' => $expiredDate,     // 有効期限
                                                'auth_code' => $authCode,           // 認証コード
                                                'updated_at' => date('Y-m-d H:i:s'),
                                            );

                                            // 更新条件
                                            $where = array(
                                                'school_id' => $schoolId,
                                            );
                                            // 更新
                                            if($wpdb->update($table2, $updateParam, $where) !== false) {
                                                $updateMessage[] = $lineCount.'行目 学校名：'.$schoolName;
                                            } else {
                                                throw new Exception('学校名：'.$schoolName.' で更新に失敗しました。');
                                            }

                                            // 所属する会員を取得
                                            $metaKey = 'user_school_name_select';
                                            $metaValue = $schoolId;
                                            $users = $wpdb->get_results(
                                                $wpdb->prepare("SELECT user_id FROM $tblUser WHERE meta_key = %s AND meta_value = %d", $metaKey, $metaValue)
                                            );

                                            // 所属する会員の有効期限を更新
                                            // ※トランザクションを張っているためトランザクションを無視するupdate_user_meta()は使用しない。
                                            foreach ($users as $user) {
                                                // フィールド
                                                $updateParam = array(
                                                    'meta_value' => $expiredDate
                                                );
                                                // 更新条件
                                                $where = array(
                                                    'user_id' => $user->user_id,
                                                    'meta_key' => 'user_expired_date'
                                                );
                                                // 更新
                                                if($wpdb->update($tblUser, $updateParam, $where) === false) {
                                                    throw new Exception('学校名：'.$schoolName.' で更新に失敗しました。会員:'.$user->user_id.' の有効期限更新に失敗しました。');
                                                }
                                            }
                                        } else {

                                            // フィールド
                                            $insertParam = array(
                                                'school_id' => $schoolId,
                                                'adopted_date' => $adoptedDate,     // 採用日付
                                                'adopted_kbn' => $adoptedKbn,       // 採用区分
                                                'expired_date' => $expiredDate,     // 有効期限
                                                'auth_code' => $authCode,           // 認証コード
                                                'created_at' => date('Y-m-d H:i:s'),
                                            );

                                            // 登録
                                            if($wpdb->insert($table2, $insertParam) !== false) {
                                                $registerMessage[] = $lineCount.'行目 学校名：'.$schoolName;
                                            } else {
                                                throw new Exception('学校名：'.$schoolName.' で登録に失敗しました。');
                                            }

                                        }
                                    }

                                } else {
                                    throw new Exception('レコードが不正か、レコードのカラム数が一致しません');
                                }

                                $lineCount++;
                            }

                            fclose($fp);

                            // 一時的に保存したファイルを削除
                            unlink($target_path);

                            // コミット
                            $wpdb->query('COMMIT');

                        } catch (Exception $e) {
                            $errorMessage[] = $lineCount.' 行目のデータのアップロードに失敗しました。（'.$e->getMessage().'）';
                            $registerMessage = array();
                            $updateMessage = array();
                            $wpdb->query('ROLLBACK');
                        } finally {
                            // テーブルのロックを解除
                            $wpdb->query('UNLOCK TABLES');
                        }

                    } else {
                        die('CSVファイルの保存に失敗しました。.');
                    }

                }
            }
        } else {
            // Nonceが無効な場合の処理
            die('Security check failed!');
        }

        $_SESSION['school_csv_messages']['register'] = $registerMessage;
        $_SESSION['school_csv_messages']['update'] = $updateMessage;
        $_SESSION['school_csv_messages']['error'] = $errorMessage;

        // 保存が完了したらリダイレクト
        wp_redirect(admin_url('admin.php?page=school_adoptions_csv_upload'));
        exit();
    }
}
add_action('admin_post_school_adoptions_csv_action', 'school_adoptions_csv_action');
// ▲ fsta ikawa 20240123

// ▼ fsta ikawa 20240207
// フロントマイページの有効期限切れロックスクリーン
function expired_date_screen_lock() {
    global $wp_query;
    // 現在のページが管理画面ではなく、かつログインしているかどうかを確認
    if (is_user_logged_in() && !is_admin()) {
        // 会員情報ページ、認証コード入力ページはロックスリーン中も操作できる
        if (is_page('user_edit') || is_page('code-input')) {
            return;
        }

        // ログイン中のユーザーの権限を取得
        $user = wp_get_current_user();

        // 権限が「管理者」「編集者」「寄稿者」の場合チェックなし
        if (in_array('administrator', $user->roles)
            || in_array('editor', $user->roles)
            || in_array('contributor', $user->roles)) {
        } else {
            if (!$user->user_expired_date) {
                // 有効期限未設定ならログアウト
                wp_logout();
                wp_redirect(home_url());
                exit();
            } else {
                // // 有効期限が３か月以内の場合ロックスクリーン
                // $lockScreenUserExpiredDate = getNextMonthLastDayFormatYmd($user->user_expired_date, 3);
                // if ($user->user_expired_date < date("Y-m-d") && $lockScreenUserExpiredDate >= date("Y-m-d")) {


                // 管理者アラートチェック、会員アラートチェックが有効になっている場合、ロックスリーン
                if (get_field('alert_check', 'options') == 1 && $user->user_alert_check == 1) {

                    // 表示テキストを取得
                    $text = get_field('alert_text', 'options')
                    ?>
                    <!DOCTYPE html>
    <html>
    <head>
        <title>Password Change Required</title>
        <style>
            #js_modal.open_modal {
              display: none;
            }
            /* オーバーレイ用のスタイル */
            .overlay {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background-color: rgba(0, 0, 0, 0.75); /* 半透明の黒色 */
                z-index: 9999; /* オーバーレイが最前面に表示されるようにします */
            }

            /* メッセージとボタンのスタイル */
            .message-box {
                position: absolute;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%);
                /* background-color: white; */
                padding: 20px;
                border-radius: 5px;
                text-align: center;
            }
            .message-box p {
                color: #fff;
                font-size: 1.6rem;
                line-height: 2.1;
                margin-bottom: 2em;
            }

            /* ボタン */
            button.btn_02 {
                display: block;
                text-align: center;
                vertical-align: middle;
                text-decoration: none;
                min-width: 260px;
                margin: auto;
                margin-bottom: 10px;
                padding: 1.2em 2em;
                font-weight: bold;
                border: 2px solid #3F559F;
                background: #3F559F;
                color: #fff;
                transition: 0.5s;
                cursor: pointer;
                font-size: 1.5rem;
            }
            button.btn_02:hover {
                color: #3F559F;
                background: #fff;
            }
            @media screen and (max-width: 767px) {
              .message-box {
                width: 100%;
              }
              .message-box p {
                text-align: left;
              }
              button.btn_02 {
                width: 100%;
              }

            }

        </style>
    </head>
    <body>
        <div class="overlay">
            <div class="message-box">
              <p>
                <?php echo $text; ?>
                <?php /*
                  本サービスは、当社商品「ナーシング・グラフィカ」または<br class="pc_only">
                  「デジタルナーシング・グラフィカ」、「BeNs」いずれかの<br class="pc_only">
                  採用校に所属する看護教員限定の会員制サービスです。<br>
                  お客様のご所属と採用状況のご確認と更新をお願いいたします。
                  */ ?>
              </p>

                <button class="btn_02" onclick="location.href='<?php echo esc_url(home_url('/')); ?>user_edit'">会員情報を確認・更新する</button>
                <button class="btn_02" onclick="location.href='<?php echo wpmem_logout_link() ?>'">ログアウト</button>
            </div>
        </div>
    </body>
    </html>
                    <?php
                }
            }
        }
    }
}
add_action('template_redirect', 'expired_date_screen_lock');
// ▲ fsta ikawa 20240207


// ▼ fsta ikawa 20240215
// 日付の形式、有効性のチェック
function validateDate ($date) {

//    if(!preg_match('/^\d{4}[-\/]\d{2}[-\/]\d{2}$/', $date)){
    if (!preg_match('/^\d{4}-(0?[1-9]|1[0-2])-(0?[1-9]|[12][0-9]|3[01])$/', $date)
        && !preg_match('/^\d{4}\/(0?[1-9]|1[0-2])\/(0?[1-9]|[12][0-9]|3[01])$/', $date)) {
        return false;
    }
    list($y1, $m1, $d1) = explode('/', $date);
    list($y2, $m2, $d2) = explode('-', $date);
    if(!checkdate($m1, $d1, $y1) && !checkdate($m2, $d2, $y2)){
        return false;
    }
    return true;
}
// ▲ fsta ikawa 20240215

// ▼ fsta ikawa 20240326
// 学校マスタCSVダウンロード
function schools_csv_download_action() {
    // CSVファイルの名前を設定
    $filename = 'schools_' . date('Ymd') . '.csv';

    // ヘッダーを設定
    header('Content-Type: text/csv; charset=SJIS');
    header('Content-Disposition: attachment; filename="' . $filename . '"');

    // 出力バッファリングを開始
    ob_start();

    // ファイルを書き込みモードで開く
    $file = fopen('php://output', 'w');

    // schoolsテーブルからデータを取得
    global $wpdb;
    $schools_data = $wpdb->get_results("
        SELECT
            school_code AS '学校コード'
            , school_name AS '学校名'
            , school_department_code AS '学科コード'
            , school_department_name AS '学科名'
            , school_type AS '学校_種別（名称）'
            , school_type1_1 AS '学校_種別1-1(名称)'
            , school_type1_2 AS '学校_種別1-2(名称)'
            , school_type2 AS '学校_種別2'
            , delete_flag AS '削除フラグ（1=削除）'
        FROM {$wpdb->prefix}schools
    ", ARRAY_A);

    // ヘッダー行を書き込む
    fputcsv($file, array_keys($schools_data[0]));

    // データを書き込む
    foreach ($schools_data as $school) {
        fputcsv($file, $school);
    }

    // ファイルを閉じる
    fclose($file);

    // 出力バッファの内容を取得し、SJISにエンコードして出力
    echo mb_convert_encoding(ob_get_clean(), 'SJIS-win', 'UTF-8');

    exit;
}
add_action('schools_csv_download_action', 'schools_csv_download_action');

// CSVダウンロードボタンがクリックされたときの処理
function handle_schools_csv_download_submit() {
    // セキュリティチェック
    if (isset($_POST['schools_csv_download_submit'])) {
        // nonceの検証
        if (isset($_POST['schools_csv_download_nonce']) && wp_verify_nonce($_POST['schools_csv_download_nonce'], 'schools_csv_download_action')) {
            // CSVダウンロード処理の実行
            schools_csv_download_action();
        }
    }
}
add_action('admin_init', 'handle_schools_csv_download_submit');

// 採用情報CSVダウンロード
function school_adoptions_csv_download_action() {
    // CSVファイルの名前を設定
    $filename = 'school_adoptions_' . date('Ymd') . '.csv';

    // ヘッダーを設定
    header('Content-Type: text/csv; charset=SJIS');
    header('Content-Disposition: attachment; filename="' . $filename . '"');

    // 出力バッファリングを開始
    ob_start();

    // ファイルを書き込みモードで開く
    $file = fopen('php://output', 'w');

    // データを取得
    global $wpdb;
    $schools_data = $wpdb->get_results("
        SELECT
            adopted_kbn AS '採用区分(値)'
            , school_code AS '学校カルテ - 学校_学校コード'
            , school_name AS '学校カルテ - カルテ名'
            , school_department_code AS '学校カルテ - 学校_学科コード'
            , school_department_name AS '学校カルテ - 学校_学科名'
            , adopted_date AS '採用日付'
            , expired_date AS '有効期限'
            , auth_code AS '認証コード'
        FROM {$wpdb->prefix}school_adoptions sa
        JOIN {$wpdb->prefix}schools s ON s.school_id = sa.school_id
    ", ARRAY_A);

    // ヘッダー行を書き込む
    fputcsv($file, array_keys($schools_data[0]));

    // データを書き込む
    foreach ($schools_data as $school) {
        fputcsv($file, $school);
    }

    // ファイルを閉じる
    fclose($file);

    // 出力バッファの内容を取得し、SJISにエンコードして出力
    echo mb_convert_encoding(ob_get_clean(), 'SJIS-win', 'UTF-8');

    exit;
}
add_action('school_adoptions_csv_download_action', 'school_adoptions_csv_download_action');

// CSVダウンロードボタンがクリックされたときの処理
function handle_school_adoptions_csv_download_submit() {
    // セキュリティチェック
    if (isset($_POST['school_adoptions_csv_download_submit'])) {
        // nonceの検証
        if (isset($_POST['school_adoptions_csv_download_nonce']) && wp_verify_nonce($_POST['school_adoptions_csv_download_nonce'], 'school_adoptions_csv_download_action')) {
            // CSVダウンロード処理の実行
            school_adoptions_csv_download_action();
        }
    }
}
add_action('admin_init', 'handle_school_adoptions_csv_download_submit');
// ▲ fsta ikawa 20240326

// ▼ fsta ikawa 20240328
// 管理者アラートチェック変更に連動して会員アラートフラグを更新
function update_user_alert_check_on_acf_alertonoff($post_id) {

    // 管理画面のみ
    if (is_admin()) {
        // ACFオプションページのスクリーンID
        $screenId = 'toplevel_page_alert_on_off';

        $screen = get_current_screen();

        // 現在のページがアラートON/OFFのACFオプションページであるかを確認
        if ($screen && $screen->id = $screenId) {

            // ACFの値を取得
            $alertValue = get_field('alert_check', $post_id); // ACFオプションページのフィールド名を指定

            // // 全ユーザの user_alert_check の値を一斉に更新する
            // update_metadata('user', 0, 'user_alert_check', $alertValue, true);

            // var_dump($alertValue);
            // exit();
            // alert_flag の値を更新
            $users = get_users();
            foreach ($users as $user) {
                update_user_meta($user->ID, 'user_alert_check', $alertValue);
            }
        }
    }
}
add_action('acf/options_page/save', 'update_user_alert_check_on_acf_alertonoff', 20);
// ▲ fsta ikawa 20240328

// ▼ fsta ikawa 20240516

// 認証コード関連のリダイレクト
function school_auth_code_page_redirect() {

    // 認証コード入力ページアクセス時、ロックスクリーンではなく、かつ、ログイン済みの場合、トップページへ遷移
    if (is_page('code-input')) {
        unset($_SESSION['school_auth_code_school_id']);

        if (is_user_logged_in() && !isLockScreen()) {
            wp_redirect(get_home_url());
            exit;
        }
    }
}
add_action('template_redirect', 'school_auth_code_page_redirect');

// 認証コードによる認証。認証後は新規登録画面化会員情報変種画面に遷移。
function handle_school_auth_code_form (){
    global $wpdb;
    unset($_SESSION['school_auth_code_school_id']);
    unset($_SESSION['school_auth_code_action_error_flag']);
    unset($_SESSION['validate_school_auth_code_school_id']);

    $auth_code = sanitize_text_field($_POST["auth_code"]);

    // バリデーション
    $errorFlag = false;
    if (empty($auth_code)) {
        $errorFlag = true;
    }

    // 認証コードの存在チェック
    if (!$errorFlag) {
        $adoptions = $wpdb->get_results( "SELECT school_id FROM mkc_school_adoptions where auth_code = '".$auth_code."'");
        if ($adoptions && count($adoptions) > 0) {
            // 学校コードをセッションに保存
            $schoolId = $adoptions[0]->school_id;
            $_SESSION['school_auth_code_school_id'] = $schoolId;
        } else {
            $errorFlag = true;
        }
    }

    // エラーがあった場合はエラーメッセージをセットして元のページへリダイレクト
    if ($errorFlag) {
        $_SESSION['school_auth_code_action_error_flag'] = $errorFlag;
        wp_redirect(wp_get_referer());
        exit;
    }

    if (is_user_logged_in()) {
        // ログイン済みの場合は会員編集へリダイレクト
        wp_redirect(get_home_url()."/user_edit/");
        exit;
    } else {
        // 未ログインの場合は新規登録へリダイレクト
        wp_redirect(get_home_url()."/register/");
        exit;
    }
}
add_action('admin_post_nopriv_school_auth_code_action', 'handle_school_auth_code_form');
add_action('admin_post_school_auth_code_action', 'handle_school_auth_code_form');

// 新規会員登録、ロックスリーン中の会員情報編集の認証コード入力済みチェック。
// 未入力の場合、認証コード入力ページへリダイレクト。
function user_register_has_input_school_auth_code() {
    if (is_page('register') || (is_page('user_edit') && isLockScreen())) {

        if (isset($_POST['submit'])) {
            // 新規登録ページポスト時
            if ($_SESSION['validate_school_auth_code_school_id']) {
                // 新規登録ページを表示
            } else {
                // 認証コード入力ページへリダイレクト。
                wp_redirect(get_home_url()."/code-input/");
                exit();
            }
        } elseif(!$_SESSION['school_auth_code_school_id']) {
            // 初回表示時に認証コードがない場合、認証コード入力ページへ遷移
            wp_redirect(get_home_url()."/code-input/");
            exit();
        } else {
            // 初回表示
            // 認証コードを別セッションへ保持
            $_SESSION['validate_school_auth_code_school_id'] = $_SESSION['school_auth_code_school_id'];
            // 認証ページからのセッションを削除
            unset($_SESSION['school_auth_code_school_id']);
        }
    }
}
add_action('template_redirect', 'user_register_has_input_school_auth_code');

// ロックスクリーン中か？
function isLockScreen()
{
    if (is_user_logged_in()) {
        $user = wp_get_current_user();
        // 権限が「管理者」「編集者」「寄稿者」の場合ロックなし
        if (in_array('administrator', $user->roles)
            || in_array('editor', $user->roles)
            || in_array('contributor', $user->roles)) {
        } else {
            // 管理者アラートチェック、会員アラートチェックが有効になっている場合、ロックスリーン中
            if (get_field('alert_check', 'options') == 1 && $user->user_alert_check == 1) {
                return true;
            }
        }
    }
    return false;
}
// ▲ fsta ikawa 20240516
?>
