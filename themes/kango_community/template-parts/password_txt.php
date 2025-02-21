<?php $url = $_SERVER['REQUEST_URI']; ?>
<?php if(strstr($url,'?a=pwdreset')): ?>
  <p>
  パスワードをお忘れの方は、<br class="pc_only">
  ご登録のメールアドレスをご入力のうえ、再発行してください。
  </p>
<?php elseif(strstr($url,'?a=set_password_from_key')): ?>
  <p>新しく設定するパスワードをご入力してください。</p>
<?php else: ?>
<?php endif; ?>




