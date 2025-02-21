


<?php if(have_comments()): ?>
<div class="comment_list_block">
<b class="fukidashi_ttl">このテーマへのコメント</b>
<ol>
  <?php wp_list_comments(array(
      'avatar_size'=>48,
      'style'=>'ul',
      'type'=>'all',
      'callback'=>'wp_list_comments_default_callback',
      'reverse_top_level' => true
    ));
  ?>

</ol>
</div>
<!-- /.comment_list_block -->
<?php endif; ?>
<?php comment_form(); ?>
