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


	<div id="default_box">
		<!-- item 제작 -->
		<div style="width:49%;">
			<div class="tab-box" id="inventory_area">
				<? include_once(__DIR__."/inventory.skin.php"); ?>
			</div>
			<?# include($board_skin_path."/write.skin.php");?>
		</div>
		
		<!--  -->
		<div style="width:2%;"></div>

		<!-- 리스트 시작 -->
		<div style="width: 40%;">
			<? if($write_pages) { ?><div class="ui-paging"><?php echo $write_pages;  ?></div><? } ?>
			<div id="log_list" class="none-trans">
			<?
				for ($i=0; $i<count($list); $i++) {
					$list_item = $list[$i];
					include($board_skin_path."/list.log.skin.php");
				}
				if (count($list) == 0) { echo "<div class=\"empty_list\">등록된 로그가 없습니다.</div>"; } 
			?>
			</div>
			<? if($write_pages) { ?><div class="ui-paging"><?php echo $write_pages;  ?></div><? } ?>

			<div class="searc-sub-box">

				<form name="fsearch" method="get">
					<input type="hidden" name="bo_table" value="<?php echo $bo_table ?>">
					<input type="hidden" name="sca" value="<?php echo $sca ?>">
					<input type="hidden" name="sop" value="and">
					<input type="hidden" name="hash" value="<?=$hash?>">

					<div class="ui-search-box">
						<fieldset class="sch_category select-box">
							<select name="sfl" id="sfl">
								<option value="wr_subject,1"<?php echo get_selected($sfl, 'wr_subject,1', true); ?>>캐릭터</option>
								<option value="wr_content"<?php echo get_selected($sfl, 'wr_content'); ?>>코멘트</option>
								<option value="wr_name,1"<?php echo get_selected($sfl, 'wr_name,1'); ?>>오너</option>
								<option value="wr_name"<?php echo get_selected($sfl, 'wr_name'); ?>>오너(코)</option>
							</select>
						</fieldset>
						<fieldset class="sch_text">
							<input type="text" name="stx" value="<?php echo stripslashes($stx) ?>" id="stx" class="frm_input" maxlength="20">
						</fieldset>
						<fieldset class="sch_button">
							<button type="submit" class="ui-btn point">검색</button>
						</fieldset>
					</div>
					<div class="ui-search-box">
						<fieldset class="sch_category">
							<span>해시태그</span>
						</fieldset>
						<fieldset class="sch_text">
							<input type="text" name="hash" value="<?=$hash?>" class="frm_input" maxlength="20">
						</fieldset>
						<fieldset class="sch_button">
							<button type="submit" class="ui-btn point">검색</button>
						</fieldset>
					</div>

					<div class="ui-search-box last">
						<fieldset class="sch_category">
							<span>로그번호</span>
						</fieldset>
						<fieldset class="sch_text">
							<input type="text" name="log" value="<?=$log?>" class="frm_input" maxlength="20">
						</fieldset>
						<fieldset class="sch_button">
							<button type="submit" class="ui-btn point">검색</button>
						</fieldset>
					</div>

				</form>
			</div>

		</div>
		<!-- 리스트 시작 -->
	


	</div>






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

	if($('#c_'+co_id).find('.modify_area').is(':visible')) {
		$('.modify_area').hide();
		$('.original_comment_area').show();
		co_id = '';
		wr_id = '';
	} else {
		$('.modify_area').hide();
		$('.original_comment_area').show();

		$('#c_'+co_id).find('.modify_area').show();
		$('#c_'+co_id).find('.original_comment_area').hide();
		$('#save_co_comment_'+co_id).focus();
	}

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
