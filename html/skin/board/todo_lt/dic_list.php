<?
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가
include_once(G5_LIB_PATH.'/thumbnail.lib.php');
include_once('./_common.php');
add_stylesheet('<link rel="stylesheet" href="'.$board_skin_url.'/style.css">', 0);
add_stylesheet('<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.8.2/css/all.min.css" />', 0);

if ($sca || $stx || $stx === '0' || $log) {     //검색이면
    $is_search_bbs = true;  
}

if ($is_search_bbs) {
    $sql = " select distinct wr_parent from {$write_table} where {$sql_search} order by wr_6 ASC, wr_id DESC limit {$from_record}, $page_rows ";
} else {
    $sql = " select * from {$write_table} where wr_is_comment = 0";
    if(!empty($notice_array))
        $sql .= " and wr_id not in (".implode(', ', $notice_array).") ";
    $sql .= " order by wr_id DESC limit {$from_record}, $page_rows ";
}

// 페이지의 공지개수가 목록수 보다 작을 때만 실행
if($page_rows > 0) {
    $result = sql_query($sql);

	$i = 0;
    $k = 0;

    while ($row = sql_fetch_array($result))
    {

		//echo print_r2($row);

        // 검색일 경우 wr_id만 얻었으므로 다시 한행을 얻는다
        if ($is_search_bbs)
            $row = sql_fetch(" select * from {$write_table} where wr_id = '{$row['wr_parent']}'");

        $list[$i] = get_list($row, $board, $board_skin_url, G5_IS_MOBILE ? $board['bo_mobile_subject_len'] : $board['bo_subject_len']);
        if (strstr($sfl, 'subject')) {
            $list[$i]['subject'] = search_font($stx, $list[$i]['subject']);
        }
        $list[$i]['is_notice'] = false;
        $list_num = $total_count - ($page - 1) * $list_page_rows - $notice_count;
        $list[$i]['num'] = $list_num - $k;

        $i++;
        $k++;
    }
	
}

?>
<div <?if($board['bo_table_width']>0){?>style="max-width:<?=$board['bo_table_width']?><?=$board['bo_table_width']>100 ? "px":"%"?>;margin:0 auto;"<?}?>>
<hr class="padding">
<? if($board['bo_content_head']) { ?>
	<div class="board-notice">
		<?=stripslashes($board['bo_content_head']);?>
	</div><hr class="padding" />
<? } ?>

<div class="board-skin-basic">

    <form name="fboardlist" id="fboardlist" action="./board_list_update.php" onsubmit="return fboardlist_submit(this);" method="post">
    <input type="hidden" name="bo_table" value="<?php echo $bo_table ?>">
    <input type="hidden" name="sfl" value="<?php echo $sfl ?>">
    <input type="hidden" name="stx" value="<?php echo $stx ?>">
    <input type="hidden" name="spt" value="<?php echo $spt ?>">
    <input type="hidden" name="sca" value="<?php echo $sca ?>">
    <input type="hidden" name="sst" value="<?php echo $sst ?>">
    <input type="hidden" name="sod" value="<?php echo $sod ?>">
    <input type="hidden" name="page" value="<?php echo $page ?>">
    <input type="hidden" name="sw" value="">
	<?//@201023 게시판 글 다중체크용 폼 필드 추가?>

	<ul class="list_todo">
		<!-- 도감 목록 시작 -->
		<!-- 출몰 지역 ; 탐색 / 처치 / 낚시 -->
		<!-- 레시피 ; 요리 / 도구 / 기타 -->
		<?
			for ($i=0; $i<count($list); $i++) :
				$thumb_class = 'n';
				if ($list[$i]['file']['count'] > 0) :
					$thumb = @get_list_thumbnail($board['bo_table'], $list[$i]['wr_id'], 300, 140,  false, true, 'center');
				else :
					$thumb = '';
				endif;

				if ((isset($thumb['src'])) || $list[$i]['wr_1']) :
					$thumb_class = 'y';
				endif;
		?>

		<?
			$dic_cate = $list[$i]['subject'];

			if($list[$i]['ca_name'] == "레시피"){
				$show_count_result = sql_query("SELECT COUNT(re_id) AS count FROM {$g5['recepi_table']} WHERE re_use = 1 AND re_dic_cate = '{$dic_cate}' AND dic_show = 1");
				$count_result = sql_query("SELECT COUNT(re_id) AS count FROM {$g5['recepi_table']} WHERE re_use = 1 AND re_dic_cate = '{$dic_cate}'");
			}else{
				switch ($dic_cate) {
						case '낚시':
							$show_cate = 'it_use_fishing';
							break;
						case '탐색':
							$show_cate = 'it_use_search';
							break;
						default:
							$show_cate = 'it_use_reward';
							break;
					}

				$show_count_result = sql_query("SELECT COUNT(it_id) AS count FROM {$g5['item_table']} WHERE it_use = 'Y' AND $show_cate = 1 AND dic_show = 1");
				$count_result = sql_query("SELECT COUNT(it_id) AS count FROM {$g5['item_table']} WHERE it_use = 'Y' AND $show_cate = 1");

			};

			$show_count_row = mysqli_fetch_assoc($show_count_result); // 결과 객체에서 데이터를 추출합니다.
			$show_count = $show_count_row ? intval($show_count_row['count']) : 0;

			$count_row = mysqli_fetch_assoc($count_result);
			$count = $count_row ? intval($count_row['count']) : 0;

			$_percent =  ($count != 0) ? ($show_count / $count)*100 : 0;
			$_achieve = round($_percent,2).'%';
		?>

		<?
			$params = $_percent == 0 ? '' : '&amp;dic_cate='.$list[$i]['subject'] ;
		?>
		<li <?=($_percent >= 100) ? ' class="complete"' : ''; ?> onclick=" location.href='./board.php?bo_table=<?=$bo_table?>&amp;ca_name=<?=$list[$i]['ca_name']?><?=$params?>' "  >

			<? if ((isset($thumb['src'])) || $list[$i]['wr_1']) : ?>
				<div class="thumb_img">
					<div class="thumb_img_wrap">
					<? if ($list[$i]['wr_1']) : ?>
						<img src="<?=$list[$i]['wr_1']; ?>" alt="<?=$list[$i]['wr_subject']; ?>">
					<? elseif (isset($thumb['src'])) : ?>
						<img src="<?=$thumb['src']; ?>" alt="<?=$list[$i]['wr_subject']; ?>">
					<? endif; ?>
					</div>
				</div>
			<? else : ?>
				<div class="thumb_img thumb_img_no">
					<div class="thumb_img_wrap">
						<i class="fas fa-clipboard-list"></i>
					</div>
				</div>
			<? endif; ?>

			<div class="todo_name"><?= $_percent == 0 ? '???' : $list[$i]['subject']; ?></div>
			<?php if ($_percent != 0 ){ ?>
			<div class="todo_progress">
				<div class="progress_bar">
					<div class="bar_inner" style="width:<?= round($_percent,0); ?>%"></div>
					<span><?=$_achieve; ?></span>
				</div>
			</div>

				<? if ($list[$i]['wr_content']) : ?>
					<div class="todo_memo"><?=$list[$i]['wr_content']; ?></div>
				<? endif;?>
			<? } ?>
		<?
			if ($is_admin || $list[$i]['mb_id']==$member['mb_id']) :

				// 수정, 삭제 링크
				$update_href = $delete_href = '';
				// 로그인중이고 자신의 글이라면 또는 관리자라면 비밀번호를 묻지 않고 바로 수정, 삭제 가능
				if (($member['mb_id'] && ($member['mb_id'] === $write['mb_id'])) || $is_admin) {
					$update_href = './write.php?w=u&amp;bo_table='.$bo_table.'&amp;wr_id='.$list[$i]['wr_id'];
					set_session('ss_delete_token', $token = uniqid(time()));
					$delete_href ='./delete.php?bo_table='.$bo_table.'&amp;wr_id='.$list[$i]['wr_id'].'&amp;token='.$token;
				}
				else if (!$write['mb_id']) { // 회원이 쓴 글이 아니라면
					$update_href = './password.php?w=u&amp;bo_table='.$bo_table.'&amp;wr_id='.$list[$i]['wr_id'];
					$delete_href = './password.php?w=d&amp;bo_table='.$bo_table.'&amp;wr_id='.$list[$i]['wr_id'];
				}
		?>
			<div class="btn_manage">
				<a href="<?=$update_href; ?>" title="수정"><i class="fas fa-cog" aria-hidden="true"></i></a>
				<a href="<?=$delete_href; ?>" onclick="del(this.href); return false;" title="삭제"><i class="fas fa-trash"></i></a>
			</div>
		<? endif; ?>
		</li>
		<? endfor; ?>


	</ul>
	
	<? if ($list_href || $is_checkbox || $write_href) { ?>
	<div class="bo_fx txt-right">
		<? if ($list_href || $write_href) { ?>
			<? if ($list_href) { ?><a href="<? echo $list_href ?>" class="ui-btn">목록</a><? } ?>
			<? if ($write_href) { ?><a href="<? echo $write_href ?>" class="ui-btn point">글쓰기</a><? } ?>
		<? } ?>
		<? if($admin_href){?><a href="<?=$admin_href?>" class="ui-btn admin" target="_blank">관리자</a><?}?>
	</div>
	<? } ?>

    </form> <?//@201023?>
	<!-- 페이지 -->
	<? echo $write_pages;  ?>

</div>
</div>
<div class="pro_edit">

</div>

<script>
function open_progress(table,idx) {
	var pro_link='<?=$board_skin_url; ?>/pro_edit.php?bo_table='+table+'&wr_id='+idx;
	$.ajax({
		async: true
		, url: pro_link
		, beforeSend: function() {}
		, success: function(data) {
			// Toss
			var response = data;
			$('.pro_edit').empty().append(response);
			$('.pro_edit').addClass('on');
		}
		, error: function(data, status, err) {
			$('.pro_edit').empty();
			$('.pro_edit').removeClass('on');
		}
		, complete: function() { 
			// Complete
		}
	});
	return false;
}

$(function(){
	$(document).mouseup(function (e){
	if($("#progress_edit").has(e.target).length === 0){
		$(".pro_edit").removeClass('on');
		$(".pro_edit").empty();
		return false;
	}
});
})

</script>
<!-- } 게시판 목록 끝 -->
