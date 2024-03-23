<?php
if (!defined("_GNUBOARD_")) exit; // 개별 페이지 접근 불가
// 1 : 몬스터 상태, 2: 몬스터 공격력. 3: 유저공격력, 4:캐릭터 HP, 5: 다이스1/다이스2/아이템, 6: 획득 아이템
$data_detail = explode("+", $data_log[5]);

$monster_comment = "";
$monster_comment_detail = "";
$reward_comment = "";

if($data_log[1] != "E") { 
	// 이벤트가 진행 중인 경우

	$monster_comment = "대상을 공격했다!";

	if( 0 < $data_log[3]) { 
		$monster_comment_detail = "공격 성공! ".($data_log[3])."의 피해를 입혔다."."<br/>".($data_log[2])."의 피해를 입었다.";
	} else {
		$monster_comment_detail = "공격 실패!";
	}
	
} else {
	$monster_comment = "대상을 처치하는데 성공했다!";
	$monster_comment_detail = "공격 성공! ".($data_log[3])."의 피해를 입혔다."."<br/>".($data_log[2])."의 피해를 입었다.";
}

if($data_log[4] <= 0 ){
	$monster_comment_detail .= "<br/>체력이 다 떨어졌다. 더이상 공격할 수 없다...";
}

if ($data_log[6]) {
	$reward_comment = $data_log[6];
	// "ㅇ은 을(를) 획득했다."
}
?>

<div class="log-data-box">
	<p><?=$monster_comment?></p>
	<? 
		if($data_log[5]) {
			// 다이스 정보 추출
			// 해당 부분은 커뮤니티 내의 공격력 산출 공식에 따라 커스텀 한다.
			$dice_result = explode("+", $data_log[5]);
	?>
		<p>
			<?=number_format($dice_result[0])?>
			+
			<?=number_format($dice_result[1])?>
			<? if($dice_result[2]) { 
				// 아이템을 사용했을 경우
			?>
			+ <?=number_format($dice_result[2])?>
			<? } ?>
		</p>
	<? } ?>
	<p><?=$monster_comment_detail?></p>
	<p><?=$reward_comment?></p>
</div>

