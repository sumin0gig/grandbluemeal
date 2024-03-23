<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가
add_stylesheet('<link rel="stylesheet" href="'.$board_skin_url.'/style.css">', 0);

if($board['bo_use_chick'] && $w == '') { 
	goto_url(G5_HTTP_BBS_URL.'/board.php?bo_table='.$bo_table.$qstr);
}

$is_error = false;
$option = '';
$option_hidden = '';
if ($is_notice || $is_html || $is_secret || $is_mail) {
	$option = '';
	if ($is_notice) {
		$option .= "\n".'<input type="checkbox" id="notice" name="notice" value="1" '.$notice_checked.'>'."\n".'<label for="notice">공지</label>';
	}

	if ($is_html) {
		if ($is_dhtml_editor) {
			$option_hidden .= '<input type="hidden" value="html1" name="html">';
		} else {
			$option .= "\n".'<input type="checkbox" id="html" name="html" onclick="html_auto_br(this);" value="'.$html_value.'" '.$html_checked.'>'."\n".'<label for="html">html</label>';
		}
	}

	if ($is_secret) {
		if ($is_admin || $is_secret==1) {
			$option .= "\n".'<input type="checkbox" id="secret" name="secret" value="secret" '.$secret_checked.'>'."\n".'<label for="secret">비밀글</label>';
		} else {
			$option_hidden .= '<input type="hidden" name="secret" value="secret">';
		}
	}

	if ($is_mail) {
		$option .= "\n".'<input type="checkbox" id="mail" name="mail" value="mail" '.$recv_email_checked.'>'."\n".'<label for="mail">답변메일받기</label>';
	}
}

if(!$is_error) { 

	// 카테고리 재정의
	$is_category = false;
	$category_option = '';
	if ($board['bo_use_category']) {
		$ca_name = "";
		if (isset($write['ca_name']))
			$ca_name = $write['ca_name'];

		$categories = explode("|", $board['bo_category_list']); // 구분자가 , 로 되어 있음
		$category_option = "";
		for ($i=0; $i<count($categories); $i++) {
			$checked = '';
			$class = '';
			$category = trim($categories[$i]);
			if (!$category) continue;

			if($i==0 && $ca_name == '') { 
				$ca_name = $category;
			}
			if ($category == $ca_name) {
				$class = ' class="on"';
				$checked = 'checked';
			}
			
			$category_option .= "<li $class>";
			
			$category_option .= "
				<input type='radio' name='ca_name' value='{$category}' id='ca_name_{$i}' {$checked} />
				<label for='ca_name_{$i}' data-index='view_{$i}'>$category</label>
			</li>\n";
		}

		$is_category = true;
	}


	?>

	<div id="load_log_board">

		<section id="anc_002">
		<h2 class="h2_frm">새 <?=$config['cf_side_title']?> 생성</h2>
		<?php echo $pg_anchor ?>

		<form name="fsideform" method="post" id="fsideform" action="../adm/side_update.php" autocomplete="off" enctype="multipart/form-data">
		<input type="hidden" name="sfl" value="<?php echo $sfl ?>">
		<input type="hidden" name="stx" value="<?php echo $stx ?>">
		<input type="hidden" name="sst" value="<?php echo $sst ?>">
		<input type="hidden" name="sod" value="<?php echo $sod ?>">
		<input type="hidden" name="page" value="<?php echo $page ?>">
		<input type="hidden" name="token" value="<?php echo $token ?>">

		<div class="tbl_frm01 tbl_wrap">
			<table>
			<colgroup>
				<col style="width: 120px;">
				<col>
			</colgroup>
			<tbody>
			<tr>
				<th scope="row"><label for="si_name"><?=$config['cf_side_title']?>명<strong class="sound_only">필수</strong></label></th>
				<td><input type="text" name="si_name" id="si_name" class="required frm_input" required></td>
			</tr>
			<tr>
				<th scope="row"><label for="si_img">이미지<strong class="sound_only">필수</strong></label></th>
				<td><input type="file" name="si_img" id="si_img"></td>
			</tr>
			</tbody>
			</table>
			<input  id="si_auth" type="hidden" value="2" />
		</div>

		<div class="btn_confirm01 btn_confirm">
			<input type="submit" value="확인" class="btn_submit">
		</div>

		</form>

	</section>

	<!--  게시물 작성/수정 끝 -->
	</div>

<? } ?>