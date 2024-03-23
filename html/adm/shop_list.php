<?php
$sub_menu = "500100";
include_once('./_common.php');

auth_check($auth[$sub_menu], 'r');

$brand = explode("||", $config['cf_shop_brand']);
$category = explode("||", $config['cf_shop_category']);

$use_level = sql_fetch("select ad_use_rank from {$g5['article_default_table']}");
$use_level = $use_level['ad_use_rank'] ? true : false;

$sql_common = " from {$g5['shop_table']} ";
$sql_search = " where (1) ";

if ($stx) {
	$sql_search .= " and ( ";
	switch ($sfl) {
		default :
			$sql_search .= " ($sfl like '%$stx%') ";
			break;
	}
	$sql_search .= " ) ";
}

if($br) { $sql_search .= " and br_name = '{$br}' "; }
if($cate) { $sql_search .= " and ca_name = '{$cate}' "; }

if (!$sst) {
	$sst  = "sh_id";
	$sod = "asc";
}
$sql_order = " order by sh_order desc, $sst $sod ";

$sql = " select count(*) as cnt {$sql_common} {$sql_search} {$sql_order} ";
$row = sql_fetch($sql);
$total_count = $row['cnt'];

$rows = $config['cf_page_rows'];
$total_page  = ceil($total_count / $rows);  // 전체 페이지 계산
if ($page < 1) { $page = 1; } // 페이지가 없으면 첫 페이지 (1 페이지)
$from_record = ($page - 1) * $rows; // 시작 열을 구함

$sql = " select * {$sql_common} {$sql_search} {$sql_order} limit {$from_record}, {$rows} ";
$result = sql_query($sql);

$listall = '<a href="'.$_SERVER['PHP_SELF'].'" class="ov_listall">전체목록</a>';

$g5['title'] = '상점 관리';
include_once('./admin.head.php');

/*******************************
	구매 아이템 갯수 설정
*******************************/
if(!sql_query(" SELECT sh_has_item_count from {$g5['shop_table']} limit 1 ", false)) {
	sql_query(" ALTER TABLE {$g5['shop_table']} ADD `sh_has_item_count` int(11) NOT NULL DEFAULT '0' AFTER `sh_has_item`");
}

$colspan = 19;
?>

<div class="local_ov01 local_ov">
	<?php echo $listall ?>
	진열된 아이템 수 <?php echo number_format($total_count) ?>개
</div>

<form name="fsearch" id="fsearch" class="local_sch01 local_sch" method="get">

<select name="br" id="br">
	<option value="">브랜드</option>
<? for($i =0; $i < count($brand); $i++) { 
	if(!$brand[$i]) continue;
?>
	<option value="<?=$brand[$i]?>" <?=$br == $brand[$i] ? "selected" : ""?>><?=$brand[$i]?></option>
<? } ?>
</select>

<select name="cate" id="cate">
	<option value="">카테고리</option>
<? for($i =0; $i < count($category); $i++) { 
	if(!$category[$i]) continue;
?>
	<option value="<?=$category[$i]?>" <?=$cate == $category[$i] ? "selected" : ""?>><?=$category[$i]?></option>
<? } ?>
</select>


<label for="sfl" class="sound_only">검색대상</label>
<select name="sfl" id="sfl">
	<option value="it_name"<?php echo get_selected($_GET['sfl'], "it_name", true); ?>>아이템 이름</option>
	<option value="it_content"<?php echo get_selected($_GET['sfl'], "it_content"); ?>>아이템 설명</option>
</select>
<label for="stx" class="sound_only">검색어<strong class="sound_only"> 필수</strong></label>
<input type="text" name="stx" value="<?php echo $stx ?>" id="stx">
<input type="submit" value="검색" class="btn_submit">

</form>

<?php if ($is_admin == 'super') { ?>
<div class="btn_add01 btn_add">
	<a href="./shop_form.php" id="bo_add">진열 추가</a>
</div>
<?php } ?>

<form name="fshoplist" id="fshoplist" action="./shop_list_update.php" onsubmit="return fshoplist_submit(this);" method="post">
<input type="hidden" name="sst" value="<?php echo $sst ?>">
<input type="hidden" name="sod" value="<?php echo $sod ?>">
<input type="hidden" name="sfl" value="<?php echo $sfl ?>">

<input type="hidden" name="br" value="<?php echo $br ?>">
<input type="hidden" name="cate" value="<?php echo $cate ?>">

<input type="hidden" name="stx" value="<?php echo $stx ?>">
<input type="hidden" name="page" value="<?php echo $page ?>">
<input type="hidden" name="token" value="<?php echo $token ?>">

<div class="tbl_head01 tbl_wrap">
	<table>
		<caption><?php echo $g5['title']; ?> 목록</caption>
		<thead>
			<tr>
				<th scope="col" class="bo-right">
					<input type="checkbox" name="chkall" value="1" id="chkall" onclick="check_all(this.form)">
				</th>
				<th scope="col">브랜드</th>
				<th scope="col">분류</th>
				<th scope="col" colspan="2">아이템</th>
				<th scope="col">진열기간</th>
				<th scope="col" colspan="2">구매금액</th>
				<th scope="col" colspan="2">구매<?=$config['cf_exp_name']?></th>
				<th scope="col" colspan="4">교환아이템</th>
				<th scope="col" colspan="2">교환타이틀</th>
				<th scope="col">구매갯수</th>
				<th scope="col">재고갯수</th>
				<th scope="col">순서</th>
				<th scope="col">관리</th>
			</tr>
			
		</thead>
		<tbody>
			<?php
			for ($i=0; $shop=sql_fetch_array($result); $i++) {
				$one_update = '<a href="./shop_form.php?w=u&amp;sh_id='.$shop['sh_id'].'&amp;'.$qstr.'">수정</a>';
				$bg = 'bg'.($i%2);

				$is_order_limit = false;
			?>

			<tr class="<?php echo $bg; ?>">
				<td style="width:30px; padding:0;" class="txt-center">
					<input type="checkbox" name="chk[]" value="<?php echo $i ?>" id="chk_<?php echo $i ?>">
					<input type="hidden" name="sh_id[<?php echo $i ?>]" value="<?php echo $shop['sh_id'] ?>" />
				</td>
				<td style="width:100px;">
					<select name="br_name[<?php echo $i ?>]" style="width:100%;">
						<option value="">브랜드</option>
						<? for($k =0; $k < count($brand); $k++) { 
							if(!$brand[$k]) continue;
						?>
						<option value="<?=$brand[$k]?>" <?=$shop['br_name'] == $brand[$k] ? "selected" : ""?>><?=$brand[$k]?></option>
						<? } ?>
					</select>
				</td>
				<td style="width:100px;">
					<select name="ca_name[<?php echo $i ?>]" style="width:100%;">
						<option value="">카테고리</option>
						<? for($k =0; $k < count($category); $k++) { 
							if(!$category[$k]) continue;
						?>
						<option value="<?=$category[$k]?>" <?=$shop['ca_name'] == $category[$k] ? "selected" : ""?>><?=$category[$k]?></option>
						<? } ?>
					</select>
				</td>
				<td style="width:27px; padding:0; border-right-width:0;">
					<? if($shop['it_id']) { ?>
						<img src="<?=get_item_img($shop['it_id'])?>" style="max-width:20px; max-height:20px;"/>
					<? } ?>
				</td>
				<td style="text-align:left; padding-left:0;">
					<? if($shop['it_id']) { echo get_item_name($shop['it_id']); } ?>
				</td>

				<td class="txt-left">
					<? if($shop['sh_date_s']) { $is_order_limit = true; ?>
						<p><?=date('Y-m-d', strtotime($shop['sh_date_s']))?> ~ <?=date('Y-m-d', strtotime($shop['sh_date_e']))?></p> 
					<? } ?>
					<? if($shop['sh_time_s']) { $is_order_limit = true; ?>
						<p><?=$shop['sh_time_s']?>시 ~ <?=$shop['sh_time_e']?>시</p>
					<? } ?>
					<? if($shop['sh_week']) { $is_order_limit = true; 
						echo "<p>";
						$str_week = explode("||", $shop['sh_week']);
						$add_str = "";
						for($k=0; $k < count($str_week); $k++) { 
							if($str_week[$k] == '') continue;
					?>
						<?=$add_str.$yoil[$str_week[$k]]?>
					<? 
						$add_str = ", ";
					} echo "</p>"; } 
					
					if(!$is_order_limit) { echo "<span style='opacity:.5;'>상시판매</span>"; }
					?>
				</td>

				<td style="width:30px; padding:0; border-right-width:0;">
					<input type="checkbox" name="sh_use_money[<?php echo $i ?>]" value="1" <?php echo $shop['sh_use_money']?"checked":"" ?>>
				</td>
				<td style="width:80px; padding-left:0;">
					<input type="text" name="sh_money[<?php echo $i ?>]" value="<?php echo get_text($shop['sh_money']) ?>" style="width:100%;">
				</td>

				<td style="width:30px; padding:0; border-right-width:0;">
					<input type="checkbox" name="sh_use_exp[<?php echo $i ?>]" value="1" <?php echo $shop['sh_use_exp']?"checked":"" ?>>
				</td>
				<td style="width:80px; padding-left:0;">
					<input type="text" name="sh_exp[<?php echo $i ?>]" value="<?php echo get_text($shop['sh_exp']) ?>" style="width:100%;">
				</td>


				<td style="width:30px; padding:0; border-right-width:0;">
					<input type="checkbox" name="sh_use_has_item[<?php echo $i ?>]" value="1" <?php echo $shop['sh_use_has_item']?"checked":"" ?>>
				</td>
				<td style="width:27px; padding:0; border-right-width:0;">
					<? if($shop['sh_has_item']) { ?>
						<img src="<?=get_item_img($shop['sh_has_item'])?>" style="max-width:20px; max-height:20px;"/>
					<? } ?>
				</td>
				<td style="text-align:left; padding-left:0; padding-right:0; border-right-width:0;">
					<? if($shop['sh_has_item']) { echo get_item_name($shop['sh_has_item']); } else { echo "-"; } ?>
				</td>
				<td style="width:60px;">
					<input type="text" name="sh_has_item_count[<?php echo $i ?>]" value="<?php echo get_text($shop['sh_has_item_count']) ?>" style="width:70%;">개
				</td>





				<td style="width:30px; padding:0; border-right-width:0;">
					<input type="checkbox" name="sh_use_has_title[<?php echo $i ?>]" value="1" <?php echo $shop['sh_use_has_title']?"checked":"" ?>>
				</td>
				<td style="text-align:left; padding-left:0;">
					<? if($shop['sh_has_title']) { ?>
						<?=get_title_image($shop['sh_has_title'])?>
					<? } else { ?>
					-
					<? } ?>
				</td>

				<td style="width:50px;">
					<input type="text" name="sh_limit[<?php echo $i ?>]" value="<?php echo get_text($shop['sh_limit']) ?>" style="width:100%;">
				</td>
				<td style="width:50px;">
					<input type="text" name="sh_qty[<?php echo $i ?>]" value="<?php echo get_text($shop['sh_qty']) ?>" style="width:100%;">
				</td>
				<td style="width:50px;">
					<input type="text" name="sh_order[<?php echo $i ?>]" value="<?php echo get_text($shop['sh_order']) ?>" style="width:100%;">
				</td>
				<td style="width:50px;" class="td_mngsmall">
					<?php echo $one_update ?>
					<?php echo $one_copy ?>
				</td>
			</tr>
			
			<?php
			}
			if ($i == 0)
				echo '<tr><td colspan="'.$colspan.'" class="empty_table">자료가 없습니다.</td></tr>';
			?>
		</tbody>
	</table>
</div>

<div class="btn_list01 btn_list">
	<input type="submit" name="act_button" value="선택수정" onclick="document.pressed=this.value">
	<?php if ($is_admin == 'super') { ?>
	<input type="submit" name="act_button" value="선택삭제" onclick="document.pressed=this.value">
	<?php } ?>
</div>

</form>

<?php echo get_paging(G5_IS_MOBILE ? $config['cf_mobile_pages'] : $config['cf_write_pages'], $page, $total_page, $_SERVER['PHP_SELF'].'?'.$qstr.'&amp;br='.$br.'&amp;cate='.$cate.'&amp;map_id='.$map_id.'&amp;page='); ?>

<script>
function fshoplist_submit(f)
{
	if (!is_checked("chk[]")) {
		alert(document.pressed+" 하실 항목을 하나 이상 선택하세요.");
		return false;
	}

	if(document.pressed == "선택삭제") {
		if(!confirm("선택한 자료를 정말 삭제하시겠습니까?")) {
			return false;
		}
	}

	return true;
}
</script>

<?php
include_once('./admin.tail.php');
?>

