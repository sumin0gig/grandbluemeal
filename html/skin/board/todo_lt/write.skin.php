<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

// add_stylesheet('css 구문', 출력순서); 숫자가 작을 수록 먼저 출력됨
add_stylesheet('<link rel="stylesheet" href="'.$board_skin_url.'/style.css">', 0);
?>


<hr class="padding">
<section id="bo_w" <?if($board['bo_table_width']>0){?>style="max-width:<?=$board['bo_table_width']?><?=$board['bo_table_width']>100 ? "px":"%"?>;margin:0 auto;"<?}?>>
	<!-- 게시물 작성/수정 시작 { -->
	<form name="fwrite" id="fwrite" action="<?php echo $action_url ?>" onsubmit="return fwrite_submit(this);" method="post" enctype="multipart/form-data" autocomplete="off">
	<input type="hidden" name="uid" value="<?php echo get_uniqid(); ?>">
	<input type="hidden" name="w" value="<?php echo $w ?>">
	<input type="hidden" name="bo_table" value="<?php echo $bo_table ?>">
	<input type="hidden" name="wr_id" value="<?php echo $wr_id ?>">
	<input type="hidden" name="sca" value="<?php echo $sca ?>">
	<input type="hidden" name="sfl" value="<?php echo $sfl ?>">
	<input type="hidden" name="stx" value="<?php echo $stx ?>">
	<input type="hidden" name="spt" value="<?php echo $spt ?>">
	<input type="hidden" name="sst" value="<?php echo $sst ?>">
	<input type="hidden" name="sod" value="<?php echo $sod ?>">
	<input type="hidden" name="page" value="<?php echo $page ?>">
	<?php
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
				if($secret_checked)$sec_select="selected";
				$sec .='<option value="secret" '.$sec_select.'>비밀글</option>';
			} else {
				$option_hidden .= '<input type="hidden" name="secret" value="secret">';
			}
		}

		if ($is_mail) {
			$option .= "\n".'<input type="checkbox" id="mail" name="mail" value="mail" '.$recv_email_checked.'>'."\n".'<label for="mail">답변메일받기</label>';
		}
	}

	echo $option_hidden;
		if($write['wr_secret']=='1') $mem_select="selected";
		if($write['wr_protect']!='') $pro_select="selected";
		if($is_member) {
			$sec .='<option value="protect" '.$pro_select.'>보호글</option>';
			$sec .='<option value="member" '.$mem_select.'>멤버공개</option>';
		}
	?>

	<div class="board-write">
	<?php if ($is_category) { ?>
	<dl>
		<dt>분류</dt>
		<dd><nav id="write_category">
			<select name="ca_name" id="ca_name" required class="required" >
				<option value="">선택하세요</option>
				<?php echo $category_option ?>
			</select> 
		</nav>
		</dd>
	</dl>
	<?}?>
	<dl id="set_protect" style="display:<?=$w=='u' && $pro_select ? 'block':'none'?>;">
		<dt><label for="wr_protect">보호글 암호</label></dt>
		<dd><input type="text" name="wr_protect" id="wr_protect" value="<?=$write['wr_protect']?>" maxlength="20"></dd>
	</dl>
	<dl>
		<dt>썸네일</dt>
		<dd> 
			<button type="button" class="btn_thumb thumb_link<?=($w == 'u' && $file[0]['file']) ? '' : ' active'; ?>"><i class="fas fa-link"></i> 외부링크</button>
			<button type="button" class="btn_thumb thumb_upload<?=($w == 'u' && $file[0]['file']) ? ' active' : ''; ?>"><i class="fas fa-upload"></i> 업로드</button>
			<div class="div_thumb_link" style="<?=($w == 'u' && $file[0]['file']) ? 'display:none;' : 'display:block;'; ?>">
				<input type="url" name="wr_1" value="<?php echo $wr_1 ?>" id="wr_1" class="frm_input full" maxlength="255" placeholder="이미지 외부링크 주소를 넣어 주세요. 외부링크는 업로드한 이미지보다 우선순위가 높습니다.">
			</div>
			<div class="div_thumb_upload" style="<?=($w == 'u' && $file[0]['file']) ? 'display:block;' : 'display:none;'; ?>">
				<input type="file" name="bf_file[]" title="썸네일 : 용량 <?php echo $upload_max_filesize ?> 이하만 업로드 가능" class="frm_file frm_input full" accept="image/*">
			<?php if ($is_file_content) { ?>
				<input type="text" name="bf_content[]" value="<?php echo ($w == 'u') ? $file[0]['bf_content'] : ''; ?>" title="파일 설명을 입력해주세요." class="frm_file frm_input">
			<?php } ?>
			<?php if($w == 'u' && $file[0]['file']) { ?>
				<span class="txt-point"><input type="checkbox" id="bf_file_del0" name="bf_file_del[0]" value="1"> <label for="bf_file_del0"><?php echo $file[0]['source'].'('.$file[0]['size'].')';  ?> 파일 삭제</label></span>
			<?php } ?>
			</div>			
		</dd>
	</dl>
	<dl>
		<dt><label for="wr_subject" class="required">제목</label></dt>
		<dd><input type="text" name="wr_subject" value="<?php echo $subject ?>" id="wr_subject" required class="frm_input required full" size="50" maxlength="255"></dd>
	</dl>
	<dl>
		<dt><span class="required">진행도 타입</span></dt>
		<dd>
			<input type="radio" id="progress" name="wr_2" value="1"<?=($wr_2 == 1 || !$wr_2) ? ' checked="checked"' : '' ?> />
			<label for="progress">퍼센트</label>
			<input type="radio" id="progress2" name="wr_2" value="2"<?=($wr_2 == 2) ? ' checked="checked"' : '' ?> />
			<label for="progress2">목표수치</label>
			<div class="div_pro pro_1"<?=($wr_2 == 2) ? ' style="display:none;"' : ''; ?>>
				<input type="number" value="<?=$wr_3; ?>" class="frm_input" name="wr_3" placeholder="달성도" max="100"<?=($wr_2 == 2) ? '' : ' required'; ?> /> %
			</div>
			<div class="div_pro pro_2"<?=($wr_2 == 2) ? '' : ' style="display:none;"'; ?>>
				<input type="number" value="<?=$wr_4; ?>" class="frm_input" name="wr_4" placeholder="달성도" /> / <input type="number" value="<?=$wr_5; ?>" name="wr_5" class="frm_input" placeholder="목표치" />
			</div>
			<input type="hidden" id="wr_6" name="wr_6" value="<?=$wr_6; ?>" />
		</dd>
	</dl>
	<dl>
		<dt><label for="wr_subject">한줄메모</label></dt>
		<dd><input type="text" value="<?php echo $content ?>" id="wr_content" name="wr_content" class="frm_input full" /></dd>
	</dl>
<?if(!$is_member){?>
	<dl>
		<dt></dt>
		<dd class="txt-right">
    <?php if ($is_name) { ?>
        <label for="wr_name">NAME<strong class="sound_only">필수</strong></label>
        <input type="text" name="wr_name" value="<?php echo $name ?>" id="wr_name" required class="frm_input required" >
    <?php } ?>

    <?php if ($is_password) { ?>
		&nbsp;&nbsp;
        <label for="wr_password">PASSWORD<strong class="sound_only">필수</strong></label>
        <input type="password" name="wr_password" id="wr_password" <?php echo $password_required ?> class="frm_input <?php echo $password_required ?>" >
    <?php } ?>
	</dd>
	</dl>
	<?}?>
		 
	</div>

	<hr class="padding" />
	<div class="btn_confirm txt-center">
		<input type="submit" value="작성완료" id="btn_submit" accesskey="s" class="btn_submit ui-btn point">
		<a href="./board.php?bo_table=<?php echo $bo_table ?>" class="btn_cancel ui-btn">취소</a>
	</div>
	</form>

	<script>
	<?php if($write_min || $write_max) { ?>
	// 글자수 제한
	var char_min = parseInt(<?php echo $write_min; ?>); // 최소
	var char_max = parseInt(<?php echo $write_max; ?>); // 최대
	check_byte("wr_content", "char_count");

	$(function() {
		$("#wr_content").on("keyup", function() {
			check_byte("wr_content", "char_count");
		});
	});

	<?php } ?>
	function html_auto_br(obj)
	{
		if (obj.checked) {
			result = confirm("자동 줄바꿈을 하시겠습니까?\n\n자동 줄바꿈은 게시물 내용중 줄바뀐 곳을<br>태그로 변환하는 기능입니다.");
			if (result)
				obj.value = "html2";
			else
				obj.value = "html1";
		}
		else
			obj.value = "";
	}

	function fwrite_submit(f)
	{
		
		var subject = "";
		var content = "";
		$.ajax({
			url: g5_bbs_url+"/ajax.filter.php",
			type: "POST",
			data: {
				"subject": f.wr_subject.value,
				"content": f.wr_content.value
			},
			dataType: "json",
			async: false,
			cache: false,
			success: function(data, textStatus) {
				subject = data.subject;
				content = data.content;
			}
		});

		if (subject) {
			alert("제목에 금지단어('"+subject+"')가 포함되어있습니다");
			f.wr_subject.focus();
			return false;
		}

		if (content) {
			alert("내용에 금지단어('"+content+"')가 포함되어있습니다");
			if (typeof(ed_wr_content) != "undefined")
				ed_wr_content.returnFalse();
			else
				f.wr_content.focus();
			return false;
		}

		if (document.getElementById("char_count")) {
			if (char_min > 0 || char_max > 0) {
				var cnt = parseInt(check_byte("wr_content", "char_count"));
				if (char_min > 0 && char_min > cnt) {
					alert("내용은 "+char_min+"글자 이상 쓰셔야 합니다.");
					return false;
				}
				else if (char_max > 0 && char_max < cnt) {
					alert("내용은 "+char_max+"글자 이하로 쓰셔야 합니다.");
					return false;
				}
			}
		}
 

		document.getElementById("btn_submit").disabled = "disabled";

		return true;
	}	
	$('#set_secret').on('change', function() {
		var selection = $(this).val();
		if(selection=='protect') $('#set_protect').css('display','block');
		else {$('#set_protect').css('display','none'); $('#wr_protect').val('');}
	});  

	$(function(e){
		$(document).on('click', '.thumb_link', function(e){
			$('.div_thumb_upload').hide();
			$('.div_thumb_link').show();
			$('.btn_thumb').removeClass('active');
			$(this).addClass('active');
		});

		$(document).on('click', '.thumb_upload', function(e){
			$('.div_thumb_link').hide();
			$('.div_thumb_upload').show();
			$('.btn_thumb').removeClass('active');
			$(this).addClass('active');
		});

		$(document).on('change', 'input[name="wr_2"]', function(e){
			var pro_value = $(this).val();
			
			$('.div_pro').hide();
			$('.div_pro input').attr('required', false);
			$('.div_pro input').val('');
			$('.pro_' + pro_value).show();
			$('.pro_' + pro_value + ' input').attr('required', true);
		});
	});
	</script>
</section>
<!-- } 게시물 작성/수정 끝 -->