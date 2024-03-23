<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가 
add_stylesheet('<link rel="stylesheet" href="'.G5_CSS_URL.'/member.css">', 0);

?>

<div>

</div>
<div id="character_profile">

	<nav id="profile_menu" >
		<a href="/mypage/character/character_form.php?w=u&amp;ch_id=<?=$ch['ch_id']?>"  class="ui-btn ico point edit circle big">
			수정
		</a>
	<? if($article['ad_use_closet'] && $article['ad_use_body']) { ?>
		<a href="<?=G5_URL?>/member/closet.php?ch_id=<?=$ch['ch_id']?>" onclick="window.open(this.href, 'big_viewer', 'width=800 height=800 menubar=no status=no toolbar=no location=no scrollbars=yes resizable=yes'); return false;" class="ui-btn ico point camera circle big">
				옷장보기
		</a>
	<? } ?>
	<? if($article['ad_use_exp']) { ?>
		<a href="<?=G5_URL?>/member/exp.php?ch_id=<?=$ch['ch_id']?>" onclick="popup_window(this.href, 'exp', 'width=400, height=500'); return false;" class="ui-btn ico point exp circle big">
				경험치 내역 보기
		</a>
	<? } ?>
	</nav>

	<!-- 캐릭터 비쥬얼 (이미지) 출력 영역 -->
	<div class="visual-area">
		<? if($article['ad_use_body'] && $ch['ch_body']) { ?>
			<div id="character_body">
				<img src="<?=$ch['ch_body']?>" alt="캐릭터 전신" />
			</div>
		<? } ?>
		<? if($article['ad_use_head'] && $ch['ch_head']) { ?>
			<div id="character_head">
				<img src="<?=$ch['ch_head']?>" alt="캐릭터 흉상" />
			</div>
		<? } ?>
	</div>
	<!-- //캐릭터 비쥬얼 (이미지) 출력 영역 -->

	<!-- 캐릭터 기본정보 출력 영역 -->
	<table class="theme-form">
		<colgroup>
			<col style="width: 110px;">
			<col>
		</colgroup>
		<tbody>

		<? if($article['ad_use_name']) { ?>
			<tr>
				<th scope="row"><?=$article['ad_text_name']?></th>
				<td>
					<?php echo $ch['ch_name'] ?>
				</td>
			</tr>
		<? } ?>
		<? if($config['cf_side_title']) {
			// 소속 정보 출력
		?>
			<tr>
				<th><?=$config['cf_side_title']?></th>
				<td>
					<?=get_side_name($ch['ch_side'])?>
				</td>
			</tr>
		<? } ?>
		<? if($config['cf_class_title']) {
			// 종족 정보 출력
		?>
			<tr>
				<th><?=$config['cf_class_title']?></th>
				<td>
					<?=get_class_name($ch['ch_class'])?>
				</td>
			</tr>
		<? } ?>
		<? if($article['ad_use_rank']) { 
			// 랭킹정보 출력
		?>
			<tr>
				<th scope="row"><?=$config['cf_rank_name']?></th>
				<td>
					<?php echo get_rank_name($ch['ch_rank']); ?>
				</td>
			</tr>
		<? } ?>
		<? if($article['ad_use_exp']) { 
			// 경험치 정보 출력
		?>
			<tr>
				<th scope="row"><?=$config['cf_exp_name']?></th>
				<td>
					<?=$ch['ch_exp']?>
					<?=$config['cf_exp_pice']?>
				</td>
			</tr>
		<? } ?>
		<? for($i=0; $i < count($ch_ar); $i++) { 
			// 추가 프로필 항목 출력
			$ar = $ch_ar[$i];
			$key = $ar['ar_code'];
		?>
			<tr>
				<th>
					<?=$ar['ar_name']?>
				</th>
				<?
					if($ar['ar_type'] == 'file' || $ar['ar_type'] == 'url') { 
				?>
					<td>
						<img src="<?=$ch[$key]?>" />
					</td>
				<? } else { ?>
					<td>
					<?
						if($ar['ar_type'] == 'textarea') 
							echo nl2br($ch[$key]);
						else 
							echo $ch[$key];

						if($ar['ar_type'] != 'textarea' && $ar['ar_type'] != 'select')
							echo $ar['ar_text'];
					?>
					</td>
				<? } ?>
			</tr>
			<? } ?>
		</tbody>
	</table>		
<!-- // 캐릭터 기본정보 출력 영역 -->


<? if($article['ad_use_status']) { // 스탯 설정 ?>
	<hr class="padding" />
	<h3>
		STATUS
		<span style="float:right;">
			<em class="txt-point" data-type="point_space"><?=get_space_status($ch['ch_id'])?></em> / <?=$ch['ch_point']?>
		</span>
	</h3>
	<div class="theme-box">
		<div class="status-bar">
			<? for($i = 0; $i < count($status); $i++) {
				
				$status[$i]['has']	= $status[$i]['has'] ? $status[$i]['has'] : $status[$i]['min'];

				$status_percent = $status[$i]['max'] ? $status[$i]['has'] / $status[$i]['max'] * 100 : 0;
				$mine_percent = $status[$i]['max'] ? $status[$i]['now'] / $status[$i]['max'] * 100 : 0;

				$resent_use_point += $status[$i]['has'];
				
				$sub_text = "";
				if($status[$i]['drop']) $sub_text = "(".$status[$i]['now'].")"; 
			?>
				<dl>
					<dt><?=$status[$i]['name']?></dt>
					<dd>
						<p>
							<i><?=$status[$i]['has']?><?=$sub_text?></i>	
							<span style="width: <?=$status_percent?>%;"></span>
							<sup style="width: <?=$mine_percent?>%;"></sup>
						</p>
					</dd>
				</dl>
			<? } ?>
		</div>
	</div>
<? } ?>

<? if($article['ad_use_title']) { // 타이틀 설정 ?>
	<hr class="padding" />
	<h3>
		TITLE
	</h3>
	<div class="theme-box">
		<div class="title-list">
			<? for($i=0; $i < count($title); $i++) { ?>
				<img src="<?=$title[$i]['ti_img']?>" />
			<? }
				if($i == 0) { 
					echo "<div class='no-data'>보유중인 타이틀이 없습니다.</div>";
				}
			?>
		</div>
	</div>
<? } ?>

	<div>
		오늘 <b> <?= $ch['ch_eat'] ?> </b>번 식사했다. 
	</div>

	<? if($article['ad_use_inven']) { // 인벤토리 출력 ?>
		<hr class="padding" />
		<h3>
			소지품
		<? if($article['ad_use_money']) { // 소지금 사용시 현재 보유 중인 소지금 출력 ?>
			<span style="float:right;">
				<em class="txt-point"><?=$mb['mb_point']?></em><?=$config['cf_money_pice']?>
			</span>
		<? } ?>
		</h3>
		<div class="theme-box">
			<? include(G5_PATH."/inventory/list.inc.php"); ?>
		</div>
	<? } ?>

	<!-- 관계란 삭제 오너 이름 출력 삭제 -->

</div>


