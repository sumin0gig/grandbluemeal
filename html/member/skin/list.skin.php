<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가 
add_stylesheet('<link rel="stylesheet" href="'.G5_CSS_URL.'/member.css">', 0);
?>

<div class="memberWrap">
	<?php for($i=0; $i < count($list); $i++) {
		$ch_list = $list[$i];
	?>
	<?php if(is_array($side[$i]) && $side[$i]['si_name']) {
		echo "<div class='title'>{$side[$i]['si_name']}</div>";
	} ?>
	<div class="member-box">
		<ul class="member-list">
			<? for($k=0; $k < count($ch_list); $k++) {
				$ch = $ch_list[$k];
			?>
				<li>
					<div class="item theme-box">
						<div class="ui-thumb">
							<a href="./viewer.php?ch_id=<?=$ch['ch_id']?>">
								<div style="width:100px; height:150px; overflow:hidden;">
								<? if($ch['ch_thumb']) { ?>
									<img src="<?=$ch['ch_thumb']?>" />
								<? } else { ?>
									<img src="https://i.imgur.com/6sQ66qj.png" />
								<? } ?>
								<div>
							</a>
						</div>
						<div class="ui-profile">
							<strong><?=$ch['ch_name']?></strong>
						</div>
					</div>
				</li>
			<?
				}
				if($k == 0) { 
					echo "<li class='no-data'>등록된 멤버가 없습니다.</li>";
				}
			?>
		</ul>
	</div>
	<? } ?>
</div>

<script>
$('.send_memo').on('click', function() {
	var target = $(this).attr('href');
	window.open(target, 'memo', "width=500, height=300");
	return false;
});
</script>
