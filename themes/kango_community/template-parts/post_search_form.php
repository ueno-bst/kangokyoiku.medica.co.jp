<div class="search_box">
  <div class="flex">
    <div class="item item_01">
      <p class="label">分類</p>
      <!-- 1. onChangeでvalue属性に指定したURLに遷移する -->
      <select name="select" onChange="location.href=value;">
        <option value="<?php echo home_url(); ?>/thema/">全て表示</option>
        <?php
        $opt = array(
      		'hide_empty' => 0,
      	);
        $categories = get_categories($opt);
        // 2. foreach文でカテゴリーをすべて表示する
        foreach($categories as $category) {
          $categories = get_the_category($post->ID);
          $slug = $categories[0]->term_id;
          // 3. if文でカテゴリーページの場合 & 現在表示されているページと同じカテゴリーの場合「selected」属性を付与する
          if(is_category() && $slug == $category->term_id){
            echo '<option value="'.get_category_link($category->term_id).'" selected>'.$category->name.'</option>';
          }else{
            echo '<option value="'.get_category_link($category->term_id).'">'.$category->name.'</option>';
          }
        }
        ?>
      </select>
    </div>
    <!-- /.item -->
    <form role="search" method="get" id="form02" action="<?php echo esc_url(home_url('/')); ?>" class="search_form_box item item_02">
      <p class="label">キーワード</p>
      <input id="s" type="text" value="" name="s" placeholder="キーワードでテーマを検索">
      <input type="hidden" value="post" name="post_type" id="post_type">
      <button type="submit"><img src="<?php echo esc_url(site_url('/')); ?>img/theme-list/search-ico.svg" alt=""></button>
    </form>
    <!-- /.item -->
  </div>
  <!-- /.flex -->
</div>
<!-- /.search_box -->
