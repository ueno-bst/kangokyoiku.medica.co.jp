<?php
//////////////////////////////////////////////////
/*　関数群 */
//////////////////////////////////////////////////
//画像URL自動置き換え/////////////////////////////////////////
//使用場面：管理画面中
function replaceImagePath($arg) {
    $imgdic_url = "img";
    // $content = str_replace('"'.$imgdic_url.'/', '"' . esc_url( get_template_directory_uri() ) . '/'.$imgdic_url.'/', $arg);
    $content = str_replace('"'.$imgdic_url.'/', '"' . esc_url( site_url(  ) ) . '/'.$imgdic_url.'/', $arg);
    return $content;
}
add_action('the_content', 'replaceImagePath');

//管理画面　edit 設定
function custom_editor_settings( $initArray ){
    $initArray['body_class'] = 'post-area';
    $initArray['block_formats'] = "段落=p; 見出し1=h2; 見出し2=h3; 見出し3=h4;引用=blockquote";
    return $initArray;
}
add_filter('tiny_mce_before_init', 'custom_editor_settings');

//記事一部取得/////////////////////////////////////
function part_of_post($postType='post') {
    $args = array(
        'post_type'      => $postType, // 表示するカスタム投稿名
        'posts_per_page' => 10,
    );
    $my_query = new WP_Query( $args );

    $digitalData = array();
    if ( $my_query->have_posts()) :
        $disp = '';
        while ( $my_query->have_posts() ) :
            $my_query->the_post();
            $disp .= '<dt>'.get_the_time('Y.m.d').'</dt>';
            $disp .= '<dd><a href="'.get_permalink().'">'.get_the_title().'</a></dd>';
        endwhile;
        echo $disp;
    endif;
}
add_action( 'part_of_post', 'part_of_post' );


// 字数を300文字に指定する/////////////////////////
function my_excerpt_mblength($length) {
    return 38;
}
add_filter('excerpt_mblength', 'my_excerpt_mblength');

// 本文からの抜粋末尾の文字列を指定する/////////////
function my_auto_excerpt_more($more) {
    return '...';
}
add_filter('excerpt_more', 'my_auto_excerpt_more');

// 抜粋末尾に個別投稿ページへのリンクを追加する//////
function my_custom_excerpt_more($excerpt) {
    return $excerpt;// . '&nbsp;<a href="' . get_permalink($post->ID) . '">続きを見る</a>';
}
add_filter('get_the_excerpt', 'my_custom_excerpt_more');

//ポストタイプ////////////////////////////////////
function get_post_type_query() {
    if ( is_archive() ) {
      if (is_tax()) {
        $termInfo = get_queried_object();
        $taxonomy = $termInfo->taxonomy;

        return get_taxonomy($taxonomy)->object_type[0];
      } elseif (is_category()) {
        return 'post';
      } else {
        if ($return = get_post_type()) {
            return $return;
        } else {
            return get_query_var( 'post_type' );
        }
      }
    }

    return get_post_type();
  }


//対象ポストタイプのタクソノミー取得///////////////
//前提条件：タクソノミーのスラッグ名が_catであること
function get_side_taxonomy($postType)
{
    //スラッグ名を生成
    $texonomy = $postType.'_cat';

    //カテゴリデータを取得し、ループ
    $catargs = array(
        'taxonomy' => $texonomy
    );
    $catlists = get_categories( $catargs );
    if (!isset($catlists['errors']) || !$catlists['errors']) {
        $len = count($catlists);
        $i = 0;
        foreach($catlists as $cat) {
            $url = esc_url(get_term_link($cat->slug,$texonomy));
            $i++;
            if($i <= $len){
                echo '<li><a href="'.$url.'"><span>'.$cat->cat_name.'</span></a></li>';
            }else{
                echo '<li><a href="'.$url.'"><span>'.$cat->cat_name.'</span></a></li>';

            }
        }
    }
}
add_action('get_side_taxonomy', 'get_side_taxonomy');

//対象タームの子一覧を取得///////////////
//前提条件：タクソノミーのスラッグ名が_catであること
function get_side_taxonomy_children($postType,$parentSlug){
    //スラッグ名を生成
   $texonomy = $postType.'_cat';
   $cat_id = intval(get_term_by("slug",$parentSlug,$texonomy)->term_taxonomy_id);
   $array = get_term_children($cat_id,$texonomy);
   if(!empty($array)){
       $len = count($array);
       $i = 0;
       foreach ($array as $key => $value) {
        $url = esc_url(get_term_link($value,$texonomy));
        $name = get_term($value,$texonomy)->name;
        $i++;
        if($i <= $len){
            echo '<li class="mb0"><a href="'.$url.'"><span>'.$name.'</span></a></li>';
        }else{
            echo '<li><a href="'.$url.'"><span>'.$name.'</span></a></li>';
        }
       }
   }
}
add_action('get_side_taxonomy_children', 'get_side_taxonomy_children');


//ページング////////////////////////////////////
//注意事項：$wp_queryを持ってきて中身を見ているため、「クエリ発行」状態によっては正常に動かない
function pagination($pages = '')
{
     global $paged;
     if(empty($paged)) $paged = 1;

     if($pages == '') {
         global $wp_query;
         $pages = $wp_query->max_num_pages;
         if(!$pages) {
             $pages = 1;
         }
     }
     if(1 != $pages) {

         echo '<ul class="pagination">';
         if($paged > 1) {
             echo '<li><a class="prev page_numbers" href="'.get_pagenum_link($paged - 1).'"><img src="'.esc_url(site_url('/')).'img/common/arrow-l-btn.svg" alt="前へ"></a></li>';
         }
         $lastPage = '';
         for ($i=1; $i <= $pages; $i++) {
             if (
                ($paged < 3 && $i < 6)
                || ($paged > ($pages - 3) && $i > ($pages - 5))
                || ($i > ($paged - 3) && $i < ($paged + 3))
            ) {
                 if ($paged == $i) {
                     echo '<li><span aria-current="page" class="page_numbers current">'.$i.'</span></li>';
                 } else {
                     echo '<li><a class="page_numbers" href="'.get_pagenum_link($i).'" >'.$i.'</a></li>';
                 }
             }
         }
         if ($paged < $pages) {
             echo '<li><a class="next page_numbers" href="'.get_pagenum_link($paged + 1).'"><img src="'.esc_url(site_url('/')).'img/common/arrow-r-btn.svg" alt="次へ"></a></a></li>';
         }
         echo "</ul>\n";
     }
}



//カスタム投稿のカレンダー
//注意事項：Custom Post Type Permalinks のプラグイン使用でパーマリンクが正常化
function get_calendar_custom($posttype,$initial = true) {
    global $wpdb, $m, $monthnum, $year, $wp_locale, $posts;


    $key = md5( $m . $monthnum . $year );
    if ( $cache = wp_cache_get( 'get_calendar_custom', 'calendar_custom' ) ) {
        if ( isset( $cache[ $key ] ) ) {
            echo $cache[ $key ];
            return;
        }
    }

    ob_start();
    // Quick check. If we have no posts at all, abort!
    if ( !$posts ) {
        $gotsome = $wpdb->get_var("SELECT ID from $wpdb->posts WHERE post_type = 'post' AND post_status = 'publish' ORDER BY post_date DESC LIMIT 1");
        if ( !$gotsome )
            return;
    }

    if ( isset($_GET['w']) )
        $w = ''.intval($_GET['w']);

    // week_begins = 0 stands for Sunday
    $week_begins = intval(get_option('start_of_week'));

    // Let's figure out when we are
    if ( !empty($monthnum) && !empty($year) ) {
        $thismonth = ''.zeroise(intval($monthnum), 2);
        $thisyear = ''.intval($year);
    } elseif ( !empty($w) ) {
        // We need to get the month from MySQL
        $thisyear = ''.intval(substr($m, 0, 4));
        $d = (($w - 1) * 7) + 6; //it seems MySQL's weeks disagree with PHP's
        $thismonth = $wpdb->get_var("SELECT DATE_FORMAT((DATE_ADD('${thisyear}0101', INTERVAL $d DAY) ), '%m')");
    } elseif ( !empty($m) ) {
        $thisyear = ''.intval(substr($m, 0, 4));
        if ( strlen($m) < 6 )
                $thismonth = '01';
        else
                $thismonth = ''.zeroise(intval(substr($m, 4, 2)), 2);
    } else {
        $thisyear = gmdate('Y', current_time('timestamp'));
        $thismonth = gmdate('m', current_time('timestamp'));
    }

    $unixmonth = mktime(0, 0 , 0, $thismonth, 1, $thisyear);

    // Get the next and previous month and year with at least one post
/*
    $previous = $wpdb->get_row("SELECT DISTINCT MONTH(post_date) AS month, YEAR(post_date) AS year
        FROM $wpdb->posts
        LEFT JOIN $wpdb->term_relationships ON($wpdb->posts.ID = $wpdb->term_relationships.object_id)
        LEFT JOIN $wpdb->term_taxonomy ON($wpdb->term_relationships.term_taxonomy_id = $wpdb->term_taxonomy.term_taxonomy_id)

        WHERE post_date < '$thisyear-$thismonth-01'

        AND post_type = '$posttype' AND post_status = 'publish'
            ORDER BY post_date DESC
            LIMIT 1");
*/

        $previous = $wpdb->get_row($wpdb->prepare("SELECT DISTINCT MONTH(post_date) AS month, YEAR(post_date) AS year
        FROM $wpdb->posts
        LEFT JOIN $wpdb->term_relationships ON($wpdb->posts.ID = $wpdb->term_relationships.object_id)
        LEFT JOIN $wpdb->term_taxonomy ON($wpdb->term_relationships.term_taxonomy_id = $wpdb->term_taxonomy.term_taxonomy_id)

        WHERE post_date < '$thisyear-$thismonth-01'

        AND post_type = %s AND post_status = 'publish'
            ORDER BY post_date DESC
            LIMIT 1",$posttype));
/*
    $next = $wpdb->get_row("SELECT  DISTINCT MONTH(post_date) AS month, YEAR(post_date) AS year
        FROM $wpdb->posts
        LEFT JOIN $wpdb->term_relationships ON($wpdb->posts.ID = $wpdb->term_relationships.object_id)
        LEFT JOIN $wpdb->term_taxonomy ON($wpdb->term_relationships.term_taxonomy_id = $wpdb->term_taxonomy.term_taxonomy_id)

        WHERE post_date >   '$thisyear-$thismonth-01'

        AND MONTH( post_date ) != MONTH( '$thisyear-$thismonth-01' )
        AND post_type = '$posttype' AND post_status = 'publish'
            ORDER   BY post_date ASC
            LIMIT 1");
*/
        $next = $wpdb->get_row($wpdb->prepare("SELECT  DISTINCT MONTH(post_date) AS month, YEAR(post_date) AS year
        FROM $wpdb->posts
        LEFT JOIN $wpdb->term_relationships ON($wpdb->posts.ID = $wpdb->term_relationships.object_id)
        LEFT JOIN $wpdb->term_taxonomy ON($wpdb->term_relationships.term_taxonomy_id = $wpdb->term_taxonomy.term_taxonomy_id)

        WHERE post_date >   '$thisyear-$thismonth-01'

        AND MONTH( post_date ) != MONTH( '$thisyear-$thismonth-01' )
        AND post_type = %s AND post_status = 'publish'
            ORDER   BY post_date ASC
            LIMIT 1",$posttype));

    echo '<div id="calendar_wrap">
    <table id="wp-calendar" summary="' . __('Calendar') . '">';
    // <caption>' . sprintf(_c('%1$s %2$s|Used as a calendar caption'), $wp_locale->get_month($thismonth), date('Y', $unixmonth)) . '</caption>
    echo '<caption>' . date('Y年m月', $unixmonth) . '</caption>
    <thead>
    <tr>';

    $myweek = array();

    for ( $wdcount=0; $wdcount<=6; $wdcount++ ) {
        $myweek[] = $wp_locale->get_weekday(($wdcount+$week_begins)%7);
    }

    $numbars = 0;
    foreach ( $myweek as $wd ) {
        $day_name = (true == $initial) ? $wp_locale->get_weekday_initial($wd) : $wp_locale->get_weekday_abbrev($wd);
        echo "\n\t\t<th abbr=\"$wd\" scope=\"col\" title=\"$wd\" class=\"day$numbars\">$day_name</th>";
        $numbars++;
    }

    echo '
    </tr>
    </thead>

    <tfoot>
    <tr>';

    if ( $previous ) {
        echo "\n\t\t".'<td abbr="' . $wp_locale->get_month($previous->month) . '" colspan="3" id="prev"><a href="' .  get_bloginfo('url') . '/' . $posttype .  '/date/' . $previous->year . '/' . $previous->month . '" title="' . sprintf(__('View posts for %1$s %2$s'), $wp_locale->get_month($previous->month),
            date('Y', mktime(0, 0 , 0, $previous->month, 1, $previous->year))) . '">&laquo; ' . $wp_locale->get_month_abbrev($wp_locale->get_month($previous->month)) . '</a></td>';
    } else {
        echo "\n\t\t".'<td colspan="3" id="prev" class="pad">&nbsp;</td>';
    }

    echo "\n\t\t".'<td class="pad">&nbsp;</td>';

    if ( $next ) {
        echo "\n\t\t".'<td abbr="' . $wp_locale->get_month($next->month) . '" colspan="3" id="next"><a href="' .  get_bloginfo('url') . '/' . $posttype .  '/date/' . $next->year . '/' . $next->month . '" title="' . sprintf(__('View posts for %1$s %2$s'), $wp_locale->get_month($next->month),
            date('Y', mktime(0, 0 , 0, $next->month, 1, $next->year))) . '">' . $wp_locale->get_month_abbrev($wp_locale->get_month($next->month)) . ' &raquo;</a></td>';
    } else {
        echo "\n\t\t".'<td colspan="3" id="next" class="pad">&nbsp;</td>';
    }

    echo '
    </tr>
    </tfoot>
    <tbody>
    <tr>';

    // Get days with posts
    $dyp_sql = "SELECT DISTINCT DAYOFMONTH(post_date)
        FROM $wpdb->posts

        LEFT JOIN $wpdb->term_relationships ON($wpdb->posts.ID = $wpdb->term_relationships.object_id)
        LEFT JOIN $wpdb->term_taxonomy ON($wpdb->term_relationships.term_taxonomy_id = $wpdb->term_taxonomy.term_taxonomy_id)

        WHERE MONTH(post_date) = '$thismonth'

        AND YEAR(post_date) = '$thisyear'
        AND post_type = '$posttype' AND post_status = 'publish'
        AND post_date < '" . current_time('mysql') . "'";

    $dayswithposts = $wpdb->get_results($dyp_sql, ARRAY_N);

    if ( $dayswithposts ) {
        foreach ( (array) $dayswithposts as $daywith ) {
            $daywithpost[] = $daywith[0];
        }
    } else {
        $daywithpost = array();
    }

    if (strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== false || strpos(strtolower($_SERVER['HTTP_USER_AGENT']), 'camino') !== false || strpos(strtolower($_SERVER['HTTP_USER_AGENT']), 'safari') !== false)
        $ak_title_separator = "\n";
    else
        $ak_title_separator = ', ';

    $ak_titles_for_day = array();
    $ak_post_titles = $wpdb->get_results("SELECT post_title, DAYOFMONTH(post_date) as dom "
        ."FROM $wpdb->posts "

        ."LEFT JOIN $wpdb->term_relationships ON($wpdb->posts.ID = $wpdb->term_relationships.object_id) "
        ."LEFT JOIN $wpdb->term_taxonomy ON($wpdb->term_relationships.term_taxonomy_id = $wpdb->term_taxonomy.term_taxonomy_id) "

        ."WHERE YEAR(post_date) = '$thisyear' "

        ."AND MONTH(post_date) = '$thismonth' "
        ."AND post_date < '".current_time('mysql')."' "
        ."AND post_type = '$posttype' AND post_status = 'publish'"
    );
    if ( $ak_post_titles ) {
        foreach ( (array) $ak_post_titles as $ak_post_title ) {

                $post_title = apply_filters( "the_title", $ak_post_title->post_title );
                $post_title = str_replace('"', '&quot;', wptexturize( $post_title ));

                if ( empty($ak_titles_for_day['day_'.$ak_post_title->dom]) )
                    $ak_titles_for_day['day_'.$ak_post_title->dom] = '';
                if ( empty($ak_titles_for_day["$ak_post_title->dom"]) ) // first one
                    $ak_titles_for_day["$ak_post_title->dom"] = $post_title;
                else
                    $ak_titles_for_day["$ak_post_title->dom"] .= $ak_title_separator . $post_title;
        }
    }


    // See how much we should pad in the beginning
    $pad = calendar_week_mod(date('w', $unixmonth)-$week_begins);
    if ( 0 != $pad )
        echo "\n\t\t".'<td colspan="'.$pad.'" class="pad">&nbsp;</td>';

    $daysinmonth = intval(date('t', $unixmonth));
    for ( $day = 1; $day <= $daysinmonth; ++$day ) {
        if ( isset($newrow) && $newrow )
            echo "\n\t</tr>\n\t<tr>\n\t\t";
        $newrow = false;

        if ( $day == gmdate('j', (time() + (get_option('gmt_offset') * 3600))) && $thismonth == gmdate('m', time()+(get_option('gmt_offset') * 3600)) && $thisyear == gmdate('Y', time()+(get_option('gmt_offset') * 3600)) )
            echo '<td id="today">';
        else
            echo '<td>';

        if ( in_array($day, $daywithpost) ) // any posts today?
                echo '<a href="' .  get_bloginfo('url') . '/' . $posttype .  '/date/' . $thisyear . '/' . $thismonth . '/' . $day . "\" title=\"$ak_titles_for_day[$day]\">$day</a>";
        else
            echo $day;
        echo '</td>';

        if ( 6 == calendar_week_mod(date('w', mktime(0, 0 , 0, $thismonth, $day, $thisyear))-$week_begins) )
            $newrow = true;
    }

    $pad = 7 - calendar_week_mod(date('w', mktime(0, 0 , 0, $thismonth, $day, $thisyear))-$week_begins);
    if ( $pad != 0 && $pad != 7 )
        echo "\n\t\t".'<td class="pad" colspan="'.$pad.'">&nbsp;</td>';

    echo "\n\t</tr>\n\t</tbody>\n\t</table></div>";

    $output = ob_get_contents();
    ob_end_clean();
    echo $output;
    $cache[ $key ] = $output;
    wp_cache_set( 'get_calendar_custom', $cache, 'calendar_custom' );
}

//月別アーカイブ////////////////////////////
global $my_archives_post_type;
function my_getarchives_where( $where, $r ) {
  global $my_archives_post_type;
  if ( isset($r['post_type']) ) {
    $my_archives_post_type = $r['post_type'];
    $where = str_replace( '\'post\'', '\'' . $r['post_type'] . '\'', $where );
  } else {
    $my_archives_post_type = '';
  }
  return $where;
}
add_filter( 'getarchives_where', 'my_getarchives_where', 10, 2 );

function my_get_archives_link( $link_html ) {
  global $my_archives_post_type;
  if ( '' != $my_archives_post_type )
    $add_link .= '?post_type=' . $my_archives_post_type;
    $link_html = preg_replace("/href=\'(.+)\'\s/","href='$1".$add_link."'",$link_html);

  return $link_html;
}
add_filter( 'get_archives_link', 'my_get_archives_link' );


//ページ検索数表示////////////////////////////
function my_result_count() {
  global $wp_query;

  $paged = get_query_var( 'paged' ) - 1;
  $ppp   = get_query_var( 'posts_per_page' );
  $count = $total = $wp_query->post_count;
  $from  = 0;
  if ( 0 < $ppp ) {
    $total = $wp_query->found_posts;
    if ( 0 < $paged )
      $from  = $paged * $ppp;
  }
  printf(
    '<span>%1$s</span>件中 <span>%2$s%3$s</span>件表示',
    $total,
    ( 1 < $count ? ($from + 1 . '〜') : '' ),
    ($from + $count )
  );
}

//検索別、結果内容出力////////////////////////////
function my_search_results(){
        if (is_date()) {
            if (is_month()) {
                $key = get_the_time('Y年m月');
            } elseif (is_day()) {
                $key = get_the_time('Y年m月d日');
            } elseif (is_year()) {
                $key = get_the_time('Y年');
            }
            echo '<p class="search">日付：【<span>'.$key.'</span>】での検索結果 ';
            my_result_count();
            echo '</p>';
        }elseif (is_search()) {
            $key = esc_html(get_query_var('s'));
            echo '<p class="search">【<span>'.$key.'</span>】での検索結果 ';
            my_result_count();
            echo '</p>';
        }elseif(is_category()){
            $key = esc_html(get_category(get_query_var('cat'))->name);
            echo '<p class="search">【<span>'.$key.'</span>】での検索結果 ';
            my_result_count();
            echo '</p>';
        }
}


//ターム配列を一行としてやり直し////////////////////////////
//タームを「１つの文字列」としてやり直しているので、各項目のリンクが正常機能しない可能性あり
function my_correction_category_list($_id,$_type = 'category'){
    $category = get_the_terms($_id,$_type);
    $catList = '';
    foreach ($category as $key => $cat) :
    $catList .= $cat->name.',';
    endforeach;
    $catList = substr($catList, 0, -1);
    return $catList;
}

//////////////////////////////////////////////////
/*　ショートコード */
//////////////////////////////////////////////////
//パンくずリスト
function fit_breadcrumb( $args = array() ){
	global $post;
	$str ='';
	$defaults = array(
		'class' => "bread_box",
		'home' => "HOME",
		'search' => "の検索結果 ",
		'tag' => "",
		'author' => "",
		'notfound' => "Hello! My Name Is 404",
	);

	$args = wp_parse_args( $args, $defaults );
	extract( $args, EXTR_SKIP );

		if( !is_home() && !is_admin() ){
			$str.= '<div class="'. $class .'" >';
			$str.= '<div class="main_width" >';
      $str.= '<nav class="nav_list" >';
      $breadcrumbLevel = 1;
      $str.= '<ol class="bread_crumb" vocab="https://schema.org/" typeof="BreadcrumbList">';
			$str.= '<li class="breadcrumb__item" property="itemListElement" typeof="ListItem"><a href="'. home_url() .'/" property="item" typeof="WebPage"><span class="icon-home" property="name">'. $home .'</span></a><meta property="position" content="' . $breadcrumbLevel++ . '" /></li>';
			$my_taxonomy = get_query_var( 'taxonomy' );
			$cpt = get_query_var( 'post_type' );

		if( $my_taxonomy && is_tax( $my_taxonomy ) ) {
			$my_tax = get_queried_object();
			$post_types = get_taxonomy( $my_taxonomy )->object_type;
			$cpt = $post_types[0];
			$str.='<li class="breadcrumb__item" property="itemListElement" typeof="ListItem"><a href="' .get_post_type_archive_link( $cpt ).'" property="item" typeof="WebPage"><span property="name">'. get_post_type_object( $cpt )->label.'</span></a><meta property="position" content="' . $breadcrumbLevel++ . '" /></li>';

		if( $my_tax -> parent != 0 ) {
			$ancestors = array_reverse( get_ancestors( $my_tax -> term_id, $my_tax->taxonomy ) );

			foreach( $ancestors as $ancestor ){
				$str.='<li class="breadcrumb__item" property="itemListElement" typeof="ListItem"><a href="'. get_term_link( $ancestor, $my_tax->taxonomy ) .'" property="item" typeof="WebPage"><span property="name">'. get_term( $ancestor, $my_tax->taxonomy )->name .'</span></a><meta property="position" content="' . $breadcrumbLevel++ . '" /></li>';
			}
		}
			$str.='<li class="breadcrumb__item" property="itemListElement" typeof="ListItem"><span property="name">'. $my_tax -> name . '</span><meta property="position" content="' . $breadcrumbLevel++ . '" /></li>';
		}

		elseif( is_category() ) {
			$cat = get_queried_object();
			if( $cat -> parent != 0 ){
				$ancestors = array_reverse( get_ancestors( $cat -> cat_ID, 'category' ));
				foreach( $ancestors as $ancestor ){
					$str.='<li class="breadcrumb__item" property="itemListElement" typeof="ListItem"><a href="'. get_category_link( $ancestor ) .'" property="item" typeof="WebPage"><span property="name">'. get_cat_name( $ancestor ) .'</span></a><meta property="position" content="' . $breadcrumbLevel++ . '" /></li>';
				}
			}
			$str.='<li class="breadcrumb__item" property="itemListElement" typeof="ListItem"><span property="name">'. $cat -> name . '</span><meta property="position" content="' . $breadcrumbLevel++ . '" /></li>';
		}

		elseif( is_post_type_archive() ) {
			$cpt = get_query_var( 'post_type' );
			$str.='<li class="breadcrumb__item" property="itemListElement" typeof="ListItem"><span property="name">'. get_post_type_object( $cpt )->label . '</span><meta property="position" content="' . $breadcrumbLevel++ . '" /></li>';
		}

		elseif( $cpt && is_singular( $cpt ) ){
			$taxes = get_object_taxonomies( $cpt );
			$mytax = $taxes[0];
			$str.='<li class="breadcrumb__item" property="itemListElement" typeof="ListItem"><a href="' .get_post_type_archive_link( $cpt ).'" property="item" typeof="WebPage"><span property="name">'. get_post_type_object( $cpt )->label.'</span></a><meta property="position" content="' . $breadcrumbLevel++ . '" /></li>';
			$taxes = get_the_terms( $post->ID, $mytax );
			$tax = get_youngest_tax( $taxes, $mytax );

		if( $tax -> parent != 0 ){
			$ancestors = array_reverse( get_ancestors( $tax -> term_id, $mytax ) );
			foreach( $ancestors as $ancestor ){
				$str.='<li class="breadcrumb__item" property="itemListElement" typeof="ListItem"><a href="'. get_term_link( $ancestor, $mytax ).'" property="item" typeof="WebPage"><span property="name">'. get_term( $ancestor, $mytax )->name . '</span></a><meta property="position" content="' . $breadcrumbLevel++ . '" /></li>';
			}
		}
			$str.='<li class="breadcrumb__item" property="itemListElement" typeof="ListItem"><a href="'. get_term_link( $tax, $mytax ).'" property="item" typeof="WebPage"><span property="name">'. $tax -> name . '</span></a><meta property="position" content="' . $breadcrumbLevel++ . '" /></li>';
			$str.= '<li class="breadcrumb__item" property="itemListElement" typeof="ListItem"><span property="name">'. $post -> post_title .'</span><meta property="position" content="' . $breadcrumbLevel++ . '" /></li>';
		}

		elseif( is_single() ){
			$categories = get_the_category( $post->ID );
			$cat = get_youngest_cat( $categories );
			if( $cat -> parent != 0 ){
				$ancestors = array_reverse( get_ancestors( $cat -> cat_ID, 'category' ) );
			foreach( $ancestors as $ancestor ){
				$str.='<li class="breadcrumb__item" property="itemListElement" typeof="ListItem"><a href="'. get_category_link( $ancestor ).'" property="item" typeof="WebPage"><span property="name">'. get_cat_name( $ancestor ). '</span></a><meta property="position" content="' . $breadcrumbLevel++ . '" /></li>';
			}
		}
			$str.='<li class="breadcrumb__item" property="itemListElement" typeof="ListItem"><a href="'. get_category_link( $cat -> term_id ). '" property="item" typeof="WebPage"><span property="name">'. $cat-> cat_name . '</span></a><meta property="position" content="' . $breadcrumbLevel++ . '" /></li>';
			$str.= '<li class="breadcrumb__item" property="itemListElement" typeof="ListItem"><span property="name">'. $post -> post_title .'</span><meta property="position" content="' . $breadcrumbLevel++ . '" /></li>';
        }

		elseif( is_page() ){
			if( $post -> post_parent != 0 ){
				$ancestors = array_reverse( get_post_ancestors( $post->ID ) );
				foreach( $ancestors as $ancestor ){
					$str.='<li class="breadcrumb__item" property="itemListElement" typeof="ListItem"><a href="'. get_permalink( $ancestor ).'" property="item" typeof="WebPage"><span property="name">'. get_the_title( $ancestor ) .'</span></a><meta property="position" content="' . $breadcrumbLevel++ . '" /></li>';
				}
			}
			$str.= '<li class="breadcrumb__item" property="itemListElement" typeof="ListItem"><span property="name">'. $post -> post_title .'</span><meta property="position" content="' . $breadcrumbLevel++ . '" /></li>';
		}

		elseif( is_date() ){
			if( get_query_var( 'day' ) != 0){
				$str.='<li class="breadcrumb__item" property="itemListElement" typeof="ListItem"><a href="'. get_year_link(get_query_var('year')). '" property="item" typeof="WebPage"><span property="name">' . get_query_var( 'year' ). '年</span></a><meta property="position" content="' . $breadcrumbLevel++ . '" /></li>';
				$str.='<li class="breadcrumb__item" property="itemListElement" typeof="ListItem"><a href="'. get_month_link(get_query_var( 'year' ), get_query_var( 'monthnum' ) ). '" property="item" typeof="WebPage"><span property="name">'. get_query_var( 'monthnum' ) .'月</span></a><meta property="position" content="' . $breadcrumbLevel++ . '" /></li>';
				$str.='<li class="breadcrumb__item" property="itemListElement" typeof="ListItem"><span property="name">'. get_query_var('day'). '日</span><meta property="position" content="' . $breadcrumbLevel++ . '" /></li>';
		}

		elseif( get_query_var('monthnum' ) != 0){
			$str.='<li class="breadcrumb__item" property="itemListElement" typeof="ListItem"><a href="'. get_year_link( get_query_var('year') ) .'" property="item" typeof="WebPage"><span property="name">'. get_query_var( 'year' ) .'年</span></a><meta property="position" content="' . $breadcrumbLevel++ . '" /></li>';
			$str.='<li class="breadcrumb__item" property="itemListElement" typeof="ListItem"><span property="name">'. get_query_var( 'monthnum' ). '月</span><meta property="position" content="' . $breadcrumbLevel++ . '" /></li>';
		}

		else {
			$str.='<li class="breadcrumb__item" property="itemListElement" typeof="ListItem"><span property="name">'. get_query_var( 'year' ) .'年</span><meta property="position" content="' . $breadcrumbLevel++ . '" /></li>';
		}
		}

		elseif( is_search() ) {
			$str.='<li class="breadcrumb__item" property="itemListElement" typeof="ListItem"><span property="name">「'. get_search_query() .'」'. $search .'</span><meta property="position" content="' . $breadcrumbLevel++ . '" /></li>';
		}

		elseif( is_author() ){
			$str .='<li class="breadcrumb__item" property="itemListElement" typeof="ListItem"><span property="name">'. $author . get_the_author_meta('display_name', get_query_var( 'author' )).'</span><meta property="position" content="' . $breadcrumbLevel++ . '" /></li>';
		}

		elseif( is_tag() ){
			$str.='<li class="breadcrumb__item" property="itemListElement" typeof="ListItem"><span property="name">'. $tag . single_tag_title( '' , false ). '</span><meta property="position" content="' . $breadcrumbLevel++ . '" /></li>';
		}

		elseif( is_attachment() ){
			$str.= '<li class="breadcrumb__item" property="itemListElement" typeof="ListItem"><span property="name">'. $post -> post_title .'</span><meta property="position" content="' . $breadcrumbLevel++ . '" /></li>';
		}

		elseif( is_404() ){
			$str.='<li class="breadcrumb__item" property="itemListElement" typeof="ListItem"><span property="name">'.$notfound.'</span><meta property="position" content="' . $breadcrumbLevel++ . '" /></li>';
		}

		else{
			$str.='<li class="breadcrumb__item" property="itemListElement" typeof="ListItem"><span property="name">'. wp_title( '', true ) .'</span><meta property="position" content="' . $breadcrumbLevel++ . '" /></li>';
		}

			$str.='</ol>';
			$str.='</nav>';
      $str.='</div>';
			$str.='</div>';
		}
	echo $str;
}

function get_youngest_cat( $categories ){
	global $post;
	if(count( $categories ) == 1 ){
		$youngest = $categories[0];
	}
	else{
		$count = 0;
		foreach( $categories as $category ){
			$children = get_term_children( $category -> term_id, 'category' );
			if($children){
				if ( $count < count( $children ) ){
					$count = count( $children );
					$lot_children = $children;
					foreach( $lot_children as $child ){
						if( in_category( $child, $post -> ID ) ){
							$youngest = get_category( $child );
						}
					}
				}
			}
			else{
				$youngest = $category;
			}
		}
	}
	return $youngest;
}

function get_youngest_tax( $taxes, $mytaxonomy ){
	global $post;
	if( count( $taxes ) == 1 ){
		$youngest = $taxes[ key( $taxes )];
	}
	else{
		$count = 0;
		foreach( $taxes as $tax ){
			$children = get_term_children( $tax -> term_id, $mytaxonomy );
			if($children){
				if ( $count < count($children) ){
					$count = count($children);
					$lot_children = $children;
					foreach($lot_children as $child){
						if( is_object_in_term( $post -> ID, $mytaxonomy ) ){
							$youngest = get_term($child, $mytaxonomy);
						}
					}
				}
			}
			else{
				$youngest = $tax;
			}
		}
	}
	return $youngest;
}
add_shortcode('breadcrumb_code', 'fit_breadcrumb');




// カウント数をリンクに含める
function include_cat_link_count($string) {
    return preg_replace('/<\/a>\s(\([0-9]*\))/', ' <span class="count">$1</spn></a>', $string);
}
function include_archive_link_count($string) {
    return preg_replace('/<\/a>&nbsp;(\([0-9]*\))/', ' <span class="count">$1</spn></a>', $string);
}


//ホームへ/////////////////////////////////////////
function set_sc_home() {
    return esc_url(home_url());
}
add_shortcode('site_url', 'set_sc_home');

/*
 * get term
 */
function get_the_term_custom($postId, $taxonomy, $parent=null) {

    $termId = '';
    $termSlug = '';
    $termName = '';
    $termUrl = '';

    $categoryList = get_the_terms($postId, $taxonomy);

    if ($categoryList && is_array($categoryList)) {
        foreach ($categoryList as $key => $term) {
            if (!$parent && $term->parent == 0) {
                break;
            } elseif ($parent && $parent == $term->parent) {
                break;
            } else {
                $term = false;
            }
        }

        if ($term) {
            $termId = $term->term_id;
            $termSlug = $term->slug;
            $termName = $term->name;
            $termUrl = get_term_link($term->term_id, $taxonomy);
        }
    }

    $termData = array(
        'id' => $termId,
        'name' => $termName,
        'slug' => $termSlug,
        'url' => $termUrl,
    );

    return $termData;
}



//サイドバーの取得//////////////////////////////////
/*
function set_c_sidebar(){
    ob_start();
    get_template_part('sidebar');
    return ob_get_clean();
}
add_shortcode('get_sidebar', 'set_c_sidebar');
*/
