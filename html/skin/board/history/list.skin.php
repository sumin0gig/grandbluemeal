<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가
add_stylesheet('<link rel="stylesheet" href="'.$board_skin_url.'/style.css">', 0);
?>

<!-- 게시판 목록 시작 { -->
<?php if ($is_admin) { ?>
	<!-- 게시판 페이지 정보 및 버튼 시작 { -->
    <div class="bo_fx checkbox">
        <div id="bo_list_total">
            <label for="chkall" class="sound_only">현재 페이지 게시물 전체</label>
			<input type="checkbox" id="chkall" onclick="if (this.checked) all_checked(true); else all_checked(false);"> 
			전체선택
        </div>

        <?php if ($rss_href || $write_href) { ?>
        <ul class="btn_bo_user">
            <?php if ($admin_href) { ?><li><a href="<?php echo $admin_href ?>" class="btn_admin">관리자</a></li><?php } ?>
        </ul>
        <?php } ?>
    </div>
    <!-- } 게시판 페이지 정보 및 버튼 끝 -->
<?php } ?>
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
	
	
	
<div id="couple_page" style="text-align: center;
    background-image: url();
    padding-bottom: 40px;
    font-size: 38px;
    font-weight: bold;
    padding-top: 16px;">
    
HISTORY
</div>

	<div style="
    padding: 50px;
    border-radius: 30px;
    border: solid;
">
	
<div id="bo_list">

	<?
	$sql = "select * from ".$write_table."  where wr_is_comment = '0' group by wr_subject  order by  wr_subject  asc";
	$result = sql_query($sql);
	for ($i=0; $history_y=sql_fetch_array($result); $i++) {
	?>
		<div class="list_year">
		<?php echo $history_y[wr_subject]?>
			<?
			$sql2 = "select * from ".$write_table."  where wr_is_comment = '0'  and wr_subject = '{$history_y[wr_subject]}' order by  wr_1 asc";
			$result2 = sql_query($sql2);
			for ($i=0; $history_m=sql_fetch_array($result2); $i++) {
			?>
			<div class="mc_con"style="
    padding-bottom: 45px; 
">
				<div  class="list_month"><?php echo $history_m[wr_1]?></div>
				<div class="list_content">
					<?php echo nl2br($history_m[wr_content]);?> 
					 <?php if ($history_m['mb_id'] == $member['mb_id']||$is_admin) { ?>
						<a href="<?php echo G5_BBS_URL?>/write.php?w=u&bo_table=<?php echo $bo_table?>&wr_id=<?php echo $history_m[wr_id]?>" class="m_btn">M</a>
						<label for="chk_wr_id_<?php echo $i ?>" class="sound_only"><?php echo $history_m['wr_subject'] ?></label>
						<input type="checkbox" name="chk_wr_id[]" value="<?php echo $history_m['wr_id'] ?>" id="chk_wr_id_<?php echo $i ?>">
					<?php } ?>	
				</div>
			</div>
			<?php } ?>
		</div>
		
	<?php } ?>
</div>

<?php if ($list_href || $is_checkbox || $write_href) { ?>
    <div class="bo_fx">
        <?php if ($is_checkbox) { ?>
        <ul class="btn_bo_adm">
            <li><input type="submit" name="btn_submit" value="선택삭제" onclick="document.pressed=this.value"></li>
        </ul>
        <?php } ?>

        <?php if ($list_href || $write_href) { ?>
        <ul class="btn_bo_user">
            <?php if ($list_href) { ?><li><a href="<?php echo $list_href ?>" class="btn_b01">목록</a></li><?php } ?>
            <?php if ($write_href) { ?><li><a href="<?php echo $write_href ?>" class="btn_b02">글쓰기</a></li><?php } ?>
        </ul>
        <?php } ?>
    </div>
    <?php } ?>
</form>

	</div>

<?php if ($is_admin) { ?>
<script>
function all_checked(sw) {
    var f = document.fboardlist;

    for (var i=0; i<f.length; i++) {
        if (f.elements[i].name == "chk_wr_id[]")
            f.elements[i].checked = sw;
    }
}

function fboardlist_submit(f) {
    var chk_count = 0;

    for (var i=0; i<f.length; i++) {
        if (f.elements[i].name == "chk_wr_id[]" && f.elements[i].checked)
            chk_count++;
    }

    if (!chk_count) {
        alert(document.pressed + "할 게시물을 하나 이상 선택하세요.");
        return false;
    }

    if(document.pressed == "선택복사") {
        select_copy("copy");
        return;
    }

    if(document.pressed == "선택이동") {
        select_copy("move");
        return;
    }

    if(document.pressed == "선택삭제") {
        if (!confirm("선택한 게시물을 정말 삭제하시겠습니까?\n\n한번 삭제한 자료는 복구할 수 없습니다\n\n답변글이 있는 게시글을 선택하신 경우\n답변글도 선택하셔야 게시글이 삭제됩니다."))
            return false;

        f.removeAttribute("target");
        f.action = "./board_list_update.php";
    }

    return true;
}

// 선택한 게시물 복사 및 이동
function select_copy(sw) {
    var f = document.fboardlist;

    if (sw == "copy")
        str = "복사";
    else
        str = "이동";

    var sub_win = window.open("", "move", "left=50, top=50, width=500, height=550, scrollbars=1");

    f.sw.value = sw;
    f.target = "move";
    f.action = "./move.php";
    f.submit();
}
</script>
<?php } ?>


<!-- } 게시판 목록 끝 -->
