<?php


add_action('parse_request', function (WP $wp) {
});

add_filter('template_include', function ($template) {
    global $wp_query;

    $user = wp_get_current_user();

    if ($wp_query->is_singular('article')) {
        return article_is_accessing($wp_query->get_queried_object()) ? $template : get_403_template();
    }

    return $template;
});

/**
 * 403ページのテンプレートを返却する
 * テンプレートがない場合はその場で終了する
 * @return mixed|string
 */
function get_403_template()
{
    // 403テンプレートを探索
    $template = locate_template(['403.php']);

    // テンプレートがない場合は終了
    if ($template === '') {
        wp_die('コンテンツへのアクセス権がありません。', '403 Forbidden', ['response' => 403]);
    }

    // 403 ヘッダを送信
    status_header(403, 'Forbidden');

    // 403専用テンプレートパスを返却する
    return $template;
}

/**
 * post_type=article を閲覧できるかどうか検証する
 * @param WP_Post $post
 * @return bool
 */
function article_is_accessing(WP_Post $post): bool
{
    $user = wp_get_current_user();

    // 非ログインユーザーは不可
    if (!$user) {
        return false;
    }

    // 管理者, 編集者, 投稿者, 寄稿者は可
    if (
        $user->has_cap('administrator') ||
        $user->has_cap('editor') ||
        $user->has_cap('author') ||
        $user->has_cap('contributor')
    ) {
        return true;
    }

    // 購読者以外は不可
    if (!$user->has_cap('subscriber')) {
        return false;
    }

    // 投稿の公開範囲 = 全公開である場合はアクセス許可
    if (get_field('permission', $post->ID, false) === 'all') {
        return true;
    }

    // 投稿の公開範囲 != 全公開 かつ 公開する購読者 にユーザーIDが含まれる場合 アクセスを許可
    $permit_users = get_field('permit_users', $post->ID, false);

    if (is_array($permit_users) && in_array($user->ID, $permit_users)) {
        return true;
    }

    $subscribe_type = get_field('subscribe_type', 'user_' . $user->ID, false);

    // 購読者種別 = DNG の場合はすべて許可
    if ($subscribe_type === 'dng') {
        return true;
    }

    // 購読者種別 = DNG の場合はすべて不可
    if ($subscribe_type === 'bens') {
        return false;
    }

    // 購読者種別 = NG の場合
    if ($subscribe_type === 'ng') {
        $subscribe_products = get_field('subscribe_products', 'user_' . $user->ID, false);

        // ユーザーと投稿がそれぞれ所属する"採用種別" 分類に重複があれば許可
        if (is_array($subscribe_products) && count($subscribe_products) > 0 && is_object_in_term($post->ID, 'product', $subscribe_products)) {
            return true;
        }

        // その他の場合は不許可
        return false;
    }

    // 上記条件のいずれにも一致しない場合は不許可
    return false;
}