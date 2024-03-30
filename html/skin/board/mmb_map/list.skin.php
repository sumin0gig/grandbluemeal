<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가
include_once(G5_LIB_PATH.'/thumbnail.lib.php');
add_stylesheet('<link rel="stylesheet" href="'.$board_skin_url.'/style.css">', 0);

set_session('ss_bo_table', $_REQUEST['bo_table']);
set_session('ss_wr_id', $_REQUEST['wr_id']);

if($character['ch_id']) { 
	// 사용가능 아이템 검색
	$temp_sql = "select it.it_id, it.it_name, inven.in_id from {$g5['inventory_table']} inven, {$g5['item_table']} it where it.it_id = inven.it_id and it.it_use_mmb_able = '1' and inven.ch_id = '{$character['ch_id']}' order by it_id asc";
	$mmb_item_result = sql_query($temp_sql);
	$mmb_item = array();
	for($i = 0; $row = sql_fetch_array($mmb_item_result); $i++) {
		$mmb_item[$i] = $row;
	}
}


$owner_front = get_style('mmb_owner_name', 'cs_etc_2');		// 자기 로그 접두문자
$owner_front = $owner_front['cs_etc_2'];
$owner_behind = get_style('mmb_owner_name', 'cs_etc_3');		// 자기 로그 접미문자
$owner_behind = $owner_behind['cs_etc_3'];

?>

<div id="load_log_board">


<!-- status -->
<?= $character['ch_name'] ?>
<img src="<?= $character['ch_thumb'] ?>"/>
<?
  $hp = get_status_by_name($character['ch_id'], '체력');
  $str = get_status_by_name($character['ch_id'], '힘');
  $mp = get_status_by_name($character['ch_id'], '마력');
  $dir = get_status_by_name($character['ch_id'], '관찰력');
?>
 <div> 체력 <?= $hp['now'] ?> / <?= $hp['has'] ?>  </div>
 <div> 힘 <?= $str['now'] ?> / <?= $str['has'] ?>  </div>
 <div> 마력 <?= $mp['now'] ?> / <?= $mp['has'] ?>  </div>
 <div> 관찰력 <?= $dir['now'] ?> / <?= $dir['has'] ?>  </div>

<!-- status -->

<!-- now location -->
<?= get_map_name($character['ma_id']) ?>
<!-- now location -->

<!-- 자비란 상단 공지 부분 -->
<? if($board['bo_content_head']) { ?>
	<div class="board-notice">
		<?=stripslashes($board['bo_content_head']);?>
	</div>
<? } ?>

<?
	/*-------------------------------------------
		동접자 카운터 설정
	---------------------------------------------*/
	$wiget = get_style('mmb_counter');
	if($wiget['cs_value']) { echo '<div class="connect-wiget">'.$wiget['cs_value'].'</div>'; }
?>

	<!-- 공지사항 한줄 롤링 -->
	<div class="marquee mmb-notice">
		<span><i><?=$config['cf_10']?></i></span>
	</div>
	<!-- // 공지사항 한줄 롤링 -->

<!-- 게시판 카테고리 시작 { -->
	<?php if ($is_category) { ?>
	<nav id="navi_category">
		<ul>
			<?php echo $category_option ?>
		</ul>
	</nav>
	<?php } ?>
<!-- } 게시판 카테고리 끝 -->

	<div class="ui-mmb-button">
		<?php if ($write_href) {  ?>
			<a href="<?php echo $write_href ?>" class="ui-btn point small">이동하기</a>
		<? } ?>
			<a href="<?php echo $list_href ?>" class="ui-btn small">새로고침</a>
			<a href="<?php echo $board_skin_url ?>/emoticon_list.php" class="ui-btn small new_win">이모티콘</a>
	</div>
</div>

<!-- 리스트 시작 -->
<div id="log_list" class="none-trans box">
	<? if($write_pages) { ?><div class="ui-paging"><?php echo $write_pages;  ?></div><? } ?>

	<?
		for ($i=0; $i<count($list); $i++) {
			$list_item = $list[$i];
			include($board_skin_path."/list.log.skin.php");
		}
		if (count($list) == 0) { echo "<div class=\"empty_list\">등록된 로그가 없습니다.</div>"; } 
	?>	
	
	<? if($write_pages) { ?><div class="ui-paging"><?php echo $write_pages;  ?></div><? } ?>

</div>


<script>
var avo_mb_id = "<?=$member['mb_id']?>";
var avo_board_skin_path = "<?=$board_skin_path?>";
var avo_board_skin_url = "<?=$board_skin_url?>";

var save_before = '';
var save_html = '';

function fviewcomment_submit(f)
{
	set_comment_token(f);
	var pattern = /(^\s*)|(\s*$)/g; // \s 공백 문자

	var content = "";
	$.ajax({
		url: g5_bbs_url+"/ajax.filter.php",
		type: "POST",
		data: {
			"content": f.wr_content.value
		},
		dataType: "json",
		async: false,
		cache: false,
		success: function(data, textStatus) {
			content = data.content;
		}
	});

	if (content) {
		alert("내용에 금지단어('"+content+"')가 포함되어있습니다");
		f.wr_content.focus();
		return false;
	}
	
	if (!f.wr_content.value) {
		alert("댓글을 입력하여 주십시오.");
		return false;
	}

	if (typeof(f.wr_name) != 'undefined')
	{
		f.wr_name.value = f.wr_name.value.replace(pattern, "");
		if (f.wr_name.value == '')
		{
			alert('이름이 입력되지 않았습니다.');
			f.wr_name.focus();
			return false;
		}
	}

	if (typeof(f.wr_password) != 'undefined')
	{
		f.wr_password.value = f.wr_password.value.replace(pattern, "");
		if (f.wr_password.value == '')
		{
			alert('비밀번호가 입력되지 않았습니다.');
			f.wr_password.focus();
			return false;
		}
	}

	return true;
}

function comment_delete()
{
	return confirm("이 댓글을 삭제하시겠습니까?");
}

function comment_box(co_id, wr_id) { 
	$('.modify_area').hide();
	$('.original_comment_area').show();

	$('#c_'+co_id).find('.modify_area').show();
	$('#c_'+co_id).find('.original_comment_area').hide();

	$('#save_co_comment_'+co_id).focus();

	var modify_form = document.getElementById('frm_modify_comment');
	modify_form.wr_id.value = wr_id;
	modify_form.comment_id.value = co_id;
}

function modify_commnet(co_id) { 
	var modify_form = document.getElementById('frm_modify_comment');
	var wr_content = $('#save_co_comment_'+co_id).val();

	modify_form.wr_content.value = wr_content;
	$('#frm_modify_comment').submit();
}

</script>

<form name="modify_comment" id="frm_modify_comment"  action="./write_comment_update.php" onsubmit="return fviewcomment_submit(this);" method="post" autocomplete="off">
	<input type="hidden" name="w" value="cu">
	<input type="hidden" name="bo_table" value="<?php echo $bo_table ?>">
	<input type="hidden" name="sca" value="<?php echo $sca ?>">
	<input type="hidden" name="sfl" value="<?php echo $sfl ?>">
	<input type="hidden" name="stx" value="<?php echo $stx ?>">
	<input type="hidden" name="spt" value="<?php echo $spt ?>">
	<input type="hidden" name="page" value="<?php echo $page ?>">

	<input type="hidden" name="comment_id" value="">
	<input type="hidden" name="wr_id" value="">
	<textarea name="wr_content" style="display: none;"></textarea>
	<button type="submit" style="display: none;"></button>
</form>

<script src="<?php echo $board_skin_url ?>/js/load.board.js"></script>
