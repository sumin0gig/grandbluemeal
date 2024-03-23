<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

function get_status($ch_id, $st_id) { 
	global $g5;

	$result = array();
	$sc = sql_fetch("select sc_value, sc_max from {$g5['status_table']} where ch_id = '{$ch_id}' and st_id = '{$st_id}'");
	$sl = sql_fetch("select st_id, st_use_max, st_max, st_min from {$g5['status_config_table']} where st_id = '{$st_id}'");
	
	$result['config_max'] = $sl['st_use_max'] ? true : false;
	$result['max'] = (int)$sl['st_max'];
	$result['min'] = (int)$sl['st_min'];
	$result['drop'] = (int)$sc['sc_value'];
	$result['now'] = $sc['sc_max'] - $sc['sc_value'];
	$result['has'] = (int)$sc['sc_max'];

	return $result;
}
function get_status_by_name($ch_id, $st_name) { 
	global $g5;

	$result = array();

	$sl = sql_fetch("select st_id, st_use_max, st_max from {$g5['status_config_table']} where st_name = '{$st_name}'");
	$sc = sql_fetch("select sc_value, sc_max from {$g5['status_table']} where ch_id = '{$ch_id}' and st_id = '{$sl['st_id']}'");

	if($sl['st_use_max']) {
		// 최대값 기준으로 출력 시, 스탯 설정에서 등록한 최대값을 MAX 값으로 둔다
		$result['max'] = $sl['st_max'];
	} else {
		$result['max'] = $sl['sc_max'];
	}
	
	$result['drop'] = $sc['sc_value'];
	$result['now'] = $sc['sc_max'] - $sc['sc_value'];
	$result['has'] = $sc['sc_max'];

	return $result;
}

// 사용한 포인트
function get_use_status($ch_id) { 
	global $g5;

	$sc = sql_fetch("select SUM(sc_max) as total from {$g5['status_table']} where ch_id = '{$ch_id}'");
	$result = $sc['total'];

	return $result;
}

// 미분배 포인트
function get_space_status($ch_id) { 
	global $g5, $config;

	$ch = get_character($ch_id);
	$use_point = get_use_status($ch_id);


	if(!$ch['ch_point']) $ch['ch_point'] = $config['cf_status_point'];

	$result = $ch['ch_point'] - $use_point;

	return $result;
}

//스탯 변동 적용
function set_status($ch_id, $st_id, $hunt, $msg='') { 
	global $g5; 

	$result = array(); 
	
	$sl = sql_fetch("select st_id from {$g5['status_config_table']} where st_id = '{$st_id}'");
	$sc = sql_fetch("select sc_id, sc_value, sc_max from {$g5['status_table']} where ch_id = '{$ch_id}' and st_id = '{$sl['st_id']}'");

	$sc['sc_value'] = $sc['sc_value'] + $hunt; 

	$message = ""; 

	if($sc['sc_value'] >= $sc['sc_max']) { 
		$message = $msg; 
		$sc['sc_value'] = $sc['sc_max']; 
	} else if($sc['sc_value'] < 0) { 
		$sc['sc_value'] = 0; 
	} 
	sql_query(" update {$g5['status_table']} set sc_value = '{$sc['sc_value']}' where sc_id = '{$sc['sc_id']}'"); 

	return $message; 

}
function set_status_by_name($ch_id, $st_name, $hunt, $msg='') { 
	global $g5; 

	$result = array(); 
	
	$sl = sql_fetch("select st_id from {$g5['status_config_table']} where st_name = '{$st_name}'");
	$sc = sql_fetch("select sc_id, sc_value, sc_max from {$g5['status_table']} where ch_id = '{$ch_id}' and st_id = '{$sl['st_id']}'");

	$sc['sc_value'] = $sc['sc_value'] + $hunt; 

	$message = ""; 

	if($sc['sc_value'] >= $sc['sc_max']) { 
		$message = $msg; 
		$sc['sc_value'] = $sc['sc_max']; 
	} else if($sc['sc_value'] < 0) { 
		$sc['sc_value'] = 0; 
	} 
	sql_query(" update {$g5['status_table']} set sc_value = '{$sc['sc_value']}' where sc_id = '{$sc['sc_id']}'"); 

	return $message; 
} 

//음식 식사
function eat_food($ch_id, $in_id, $it_exp, $st_id, $it_value, $it_name, $msg=''){
	global $g5, $config;

	$character = get_character($ch_id);

	if($character['ch_eat_date'] != G5_TIME_YMD){
		// 마지막 음식 식사 일자가 오늘이 아닐 경우
		// 음식 식사 일자 갱신 및 음식 식사 횟수 초기화
		sql_query("
			update {$g5['character_table']}
					set		ch_eat = 0,
							ch_eat_date = '".G5_TIME_YMD."'

				where		ch_id = '$ch_id'
		");

		$character['ch_eat'] = 0;
		$character['ch_eat_date'] = G5_TIME_YMD;
	// 식사 횟수
	} else{
		if( $character['ch_eat'] >= $config['cf_eat_count']){
			return '오늘은 이미 '.$config['cf_eat_count'].'번의 식사를 했다...'; 
		}else{
			// 음식 식사 횟수 증가
			sql_query("update {$g5['character_table']} set ch_eat = ch_eat+1 where ch_id = '$ch_id'");
		};
	};

	// 스탯에 영향을 줄 경우 스탯
	if (empty($st_id) == false || empty($it_value) == false) {
		set_status($ch_id, $st_id, $it_value);
	};
	
	// 캐릭터 경험치
	$ch_exp = get_exp_sum($ch_id);
	
	// 경험치 건별 생성
	$ex_ch_exp = intval($ch_exp) + $it_exp;

	switch ($character['ch_eat']) {
		case 0:
			$eat_log_str = "아침";
			break;
		case 1:
			$eat_log_str = "점심";
			break;
		case 2:
			$eat_log_str = "저녁";
			break;
		default:
			$eat_log_str = "";
			break;
	}
	$sql = " insert into {$g5['exp_table']}
				set ch_id = '$ch_id',
					ch_name = '{$ch['ch_name']}',
					ex_datetime = '".G5_TIME_YMDHIS."',
					ex_content = '$eat_log_str 식사 「 $it_name 」',
					ex_point = '$it_exp',
					ex_ch_exp = '$ex_ch_exp',
					ex_rel_action = '$rel_action' ";
	sql_query($sql);
	
	// 경험치 UPDATE
	$sql = " update {$g5['character_table']} set ch_exp = '$ex_ch_exp' where ch_id = '$ch_id' ";
	sql_query($sql);

	delete_inventory($in_id);

	return $it_exp."경험을 쌓았다."; 

}
?>


