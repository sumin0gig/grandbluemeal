<?php
if (!defined("_GNUBOARD_")) exit; // 개별 페이지 접근 불가

// 1. S : 탐색 (성공여부, 획득아이템 ID, 획득아이템이름, 인벤 ID)
?>

	<? if($data_log[1] == 'S') { ?>
		<div class="log-item-box data-S">
			<div>
				<img src="<?=get_item_img($data_log[2])?>" />
			</div>
			<p>
				<span><strong><?=$data_log[3]?></strong><?=j($data_log[3], '을')?> 제작했다!</span>
			</p>
		</div>
	<? } else { ?>
		<div style="line-height:32px;">
			<p> 요리에 실패했다... </p>
		</div>
	<? } ?>