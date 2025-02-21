<?php

// 【MW MW Form】アラートカスタマイズ
function MW_text_rule_weblog($error, $key, $rule ) {
    if($key === 'name' && $rule === 'noempty'){
        return 'お名前をご記入ください。';
    }else if($key === 'mail' && $rule === 'noempty'){
        return 'メールアドレスをご記入ください。';
    }else if($key === 'comment' && $rule === 'noempty'){
          return 'お問い合わせ内容をご記入ください。';
    }
    return $error;
}
add_filter('mwform_error_message_mw-wp-form-86', 'MW_text_rule_weblog',10,3);
