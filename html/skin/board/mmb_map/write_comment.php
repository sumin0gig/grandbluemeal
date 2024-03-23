<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

if ($is_comment_write) {
	if($w == '') $w = 'c';
?>
<!-- 댓글 쓰기 시작 { -->
<aside class="bo_vc_w" id="bo_vc_w_<?=$list_item['wr_id']?>">
	<form name="fviewcomment" action="./write_comment_update.php" onsubmit="return fviewcomment_submit(this);" method="post" autocomplete="off">
	<input type="hidden" name="w" value="<?php echo $w ?>" id="w">
	<input type="hidden" name="bo_table" value="<?php echo $bo_table ?>">
	<input type="hidden" name="wr_id" value="<?php echo $list_item['wr_id'] ?>">
	<input type="hidden" name="sca" value="<?php echo $sca ?>">
	<input type="hidden" name="sfl" value="<?php echo $sfl ?>">
	<input type="hidden" name="stx" value="<?php echo $stx ?>">
	<input type="hidden" name="spt" value="<?php echo $spt ?>">
	<input type="hidden" name="page" value="<?php echo $page ?>">

	<input type="hidden" name="ch_id" value="<?=$character['ch_id']?>" />
	<input type="hidden" name="ti_id" value="<?=$character['ch_title']?>" />
	<input type="hidden" name="ma_id" value="<?=$character['ma_id']?>" />
	<input type="hidden" name="wr_subject" value="<?=$character['ch_name'] ? $character['ch_name'] : "GUEST"?>" />

	<div class="input-comment">
	<? if(empty($mmb_item) == false) { ?>
		<select name="use_item" class="full">
			<option value="">사용할 아이템 선택</option>
		<?	for($h=0; $h < count($mmb_item); $h++) { ?>
			<option value="<?=$mmb_item[$h]['in_id']?>">
				<?=$mmb_item[$h]['it_name']?>
			</option>
		<? } ?>
		</select>
	<? } ?>

		<textarea name="wr_content" required class="required" title="내용"></textarea>

		<div class="action-check form-input">
		<? if($character['ch_state']=='승인') { ?>
			<input type="radio" name="action" id="action_<?=$list_item['wr_id']?>_" value="" checked/>
			<label for="action_<?=$list_item['wr_id']?>_">일반행동</label>

			<?
			/******************************************************
					위치이동 커맨드 추가 - 몬스터 공략관련
					- 같은 지역에 있는 유저들 끼리 공격 할 수 있다.
					- 해당 조건을 제거하고 싶다면 아래의 [  && $list_item['ma_id'] == $character['ma_id']  ] 코드 제거 할것
					- 작성자만 공격이 가능하도록 하고 싶다면, $list_item['ma_id'] == $character['ma_id'] 이 조건을
					  $list_item['mb_id'] == $member['mb_id'] 로 변경할것
			******************************************************/	
			$state_result = sql_fetch("select * from {$g5['status_table']} where ch_id = '{$character['ch_id']}' and st_id = 1");

			if($list_item['wr_mon_state'] =='S' && $list_item['ma_id'] == $character['ma_id'] && $state_result['sc_max'] > $state_result['sc_value']  ) { ?>
			<input type="radio" name="action" id="action_<?=$list_item['wr_id']?>_MAP_MON" value="MAP_MON" />
			<label for="action_<?=$list_item['wr_id']?>_MAP_MON">물리 공격</label>

			<input type="radio" name="action" id="action_<?=$list_item['wr_id']?>_MAP_MON_MAGIC" value="MAP_MON_MAGIC" />
			<label for="action_<?=$list_item['wr_id']?>_MAP_MON_MAGIC">마법 공격</label>
			<? }
			/******************************************************
					위치이동 커맨드 추가 종료 - 몬스터 공략관련
			******************************************************/	
			?>


		<? } ?>
		</div>

	</div>
	<div class="btn_confirm">
		<button type="submit" class="ui-comment-submit ui-btn">입력</button>
	</div>

	</form>
</aside>
<?
}
?>

