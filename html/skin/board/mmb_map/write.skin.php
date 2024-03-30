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

/*if(!$character['ch_id']) {
?>
	<div class='error'>
	<? if(!$is_member) { ?>
		작성권한이 없습니다.<br />
		계정로그인을 해주시길 바랍니다.

		<div class="btn-group txt-center">
			<a href="<?=G5_BBS_URL?>/login.php" class="ui-btn">로그인</a>
			<a href="./board.php?bo_table=<?=$bo_table?>" class="ui-btn">목록으로</a>
		</div>
	<? } else { ?>
		대표캐릭터가 설정되지 않았습니다.<br />
		마이페이지에서 대표 캐릭터를 선택해 주시길 바랍니다.

		<div class="btn-group txt-center">
			<a href="<?=G5_URL?>/mypage" class="ui-btn">마이페이지</a>
			<a href="./board.php?bo_table=<?=$bo_table?>" class="ui-btn">목록으로</a>
		</div>
	<? } ?>
	</div>
<?
	$is_error = true;
}*/

if(!$is_error) { 

	if($character['ch_id']) { 
		// 사용가능 아이템 검색
		$temp_sql = "select it.it_id, it.it_name, inven.in_id from {$g5['inventory_table']} inven, {$g5['item_table']} it where it.it_id = inven.it_id and it.it_use_mmb_able = '1' and inven.ch_id = '{$character['ch_id']}'";
		$mmb_item_result = sql_query($temp_sql);
		$mmb_item = array();
		for($i = 0; $row = sql_fetch_array($mmb_item_result); $i++) {
			$mmb_item[$i] = $row;
		}
	}

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

	$image_url = $board_skin_url."/img/no_image.png";
	if($w == 'u') { 
		if($write['wr_type'] == 'URL') {
			$image_url = $write['wr_url'];
			$img_data = "width : ".$write['wr_width']."px / height : ".$write['wr_height']."px";
		} else if($file[0]['file']) { 
			$image_url = $file[0]['path']."/".$file[0]['file'];
			$img_data = "width : ".$file[0]['wr_width']."px / height : ".$file[0]['wr_height']."px";
		}

		if($write['wr_subject'] == "--|UPLOADING|--")	{
			$write['wr_subject'] = $character['ch_name'];
			if(!$write['wr_subject']) $write['wr_subject'] = 'GUEST';
		}

	} else { 
		$write['wr_subject'] = $character['ch_name'];
		if(!$write['wr_subject']) $write['wr_subject'] = 'GUEST';
	}

	$temp_sql = "select ch_thumb, mb_id, ch_id, ch_name from {$g5['character_table']} where ch_state = '승인' and ch_type != 'test' and ch_id != '{$character['ch_id']}' order by ch_type asc, ch_name asc";
	$re_ch_result = sql_query($temp_sql);
	$re_ch = array();
	for($i = 0; $row = sql_fetch_array($re_ch_result); $i++) {
		$re_ch[$i] = $row;
	}

	?>



	<div id="load_log_board">
		<section id="bo_w" class="mmb-board">
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
			<input type="hidden" name="wr_subject" value="<?=$write['wr_subject']?>" />
			<input type="hidden" name="wr_width" id="wr_width" value="<?php echo $write['wr_width']; ?>">
			<input type="hidden" name="wr_height" id="wr_height" value="<?php echo $write['wr_height']; ?>">
			<?php echo $option_hidden; ?>
			
			<!-- LOG 등록 부분 -->
			<dl style='display: none;'>
				<dt>
					<select name="wr_type" onchange="fn_log_type(this.value);">
						<? $write['wr_type'] = "URL" ?>
						<option value="URL" selected>URL</option>
					</select>
				</dt>
				<dd>

					<div id="add_URL">
						<input type="text" name="wr_url" value="none img" title="이미지 링크 없음" id="wr_url"/>
					</div>
				</dd>
			</dl>
			
			<?php if ($is_category) { ?>
				<ul id="board_category">
					<?php echo $category_option ?>
				</ul>
			<?php } ?>

			<? if(!$write['wr_log'] && $character['ch_state']=='승인') { ?>
				<div id="board_action" class="inner">
					<dl style='display: none;'>
						<dt>
							<label for="action"><i class="icon act"></i>Action</label>
						</dt>
						<dd>
							<select name="action" id="action">
							<?
							/******************************************************
								위치이동 커맨드 추가
							******************************************************/
							if($config['cf_use_map']) { ?>
								<option value="MAP">위치이동</option>
							<? } 
							/******************************************************
								위치이동 커맨드 추가 종료
							******************************************************/
							?>
							</select>
						</dd>
					</dl>

					<? 
					/******************************************************
								위치이동 커맨드 추가
					******************************************************/	
						if($config['cf_use_map']) { 
					?>
						<input type="hidden" name="re_ma_id" id="re_ma_id"/>
					<? }
					/******************************************************
								위치이동 커맨드 추가 종료
					******************************************************/	
					?>

				</div>
			<? } ?>

				<div class="inner">

				<? if(!$write['wr_item_log'] && $character['ch_state']=='승인' && count($mmb_item) > 0) { ?>
					<dl>
						<dt>
							<label for="use_item"><i class="icon item"></i>Item</label>
						</dt>
						<dd>
							<select name="use_item">
								<option value="">사용할 아이템 선택</option>
							<?
								for($i=0; $i < count($mmb_item); $i++) { ?>
								<option value="<?=$mmb_item[$i]['in_id']?>"><?=$mmb_item[$i]['it_name']?></option>
							<? } ?>
							</select>
						</dd>
					</dl>
				<? } ?>
					<!-- 일반 커맨드 -->
					<?
						/******************************************************
						* :: 주사위의 경우, 한번 굴린 데이터가 남아 있을 시 수정 불가
						* :: 이때, 다른 카테고리의 선택을 할 수 없다.
						*******************************************************/
					?>
					
					<?php if ($is_name) { ?>
					<dl>
						<dt>
							<label for="wr_name">이름</label>
						</dt>
						<dd>
							<input type="text" name="wr_name" value="<?php echo $name ?>" id="wr_name" required class="frm_input required" size="10" maxlength="20">
						</dd>
					</dl>
					<?php } ?>

					<?php if ($is_password) { ?>
					<dl>
						<dt>
							<label for="wr_password">비밀번호</label>
						</dt>
						<dd>
							<input type="password" name="wr_password" id="wr_password" value="<?=$_COOKIE['MMB_PW']?>" class="frm_input" maxlength="20">
						</dd>
					</dl>
					<?php } ?>
					
				</div>
			
			<hr class="padding small" />

			<div class="comments">
			<p class="ui-btn help">해시태그 : #해시태그내용 / 로그링크 : @로그번호 / 멤버알람 : [[닉네임]]</p>
				<?php if($write_min || $write_max) { ?>
				<!-- 최소/최대 글자 수 사용 시 -->
				<p id="char_count_desc">이 게시판은 최소 <strong><?php echo $write_min; ?></strong>글자 이상, 최대 <strong><?php echo $write_max; ?></strong>글자 이하까지 글을 쓰실 수 있습니다.</p>
				<?php } ?>
				<?php echo $editor_html; // 에디터 사용시는 에디터로, 아니면 textarea 로 노출 ?>
				<?php if($write_min || $write_max) { ?>
				<!-- 최소/최대 글자 수 사용 시 -->
				<div id="char_count_wrap"><span id="char_count"></span>글자</div>
				<?php } ?>
			</div>
			
			<?
			/******************************************************
				위치이동 커맨드 추가 : 지도 표시 부분. 불필요할 시 제거
			******************************************************/	
			if($config['cf_use_map']) {
				include_once($board_skin_path.'/list.top.map.skin.php');
			}
			/******************************************************
				위치이동 커맨드 추가 종료
			******************************************************/	
			?>

			<hr class="padding" />

			<div class="txt-center">
				<button type="submit" style="display:none;" id="btn_submit" accesskey="s" class="ui-btn">COMMENT</button>
				<button type="button" onclick="location.href='./board.php?bo_table=<?=$bo_table?>';" class="ui-btn">취소</button>
			</div>
			</form>

			<hr class="padding" />
			<hr class="padding" />
			<hr class="padding" />
		</section>
	<!-- } 게시물 작성/수정 끝 -->
	</div>

<script>
	<?php if($write_min || $write_max) { ?>
	// 글자수 제한
	var char_min = parseInt(<?php echo $write_min; ?>); // 최소
	var char_max = parseInt(<?php echo $write_max; ?>); // 최대
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
		<?php echo $editor_js; // 에디터 사용시 자바스크립트에서 내용을 폼필드로 넣어주며 내용이 입력되었는지 검사함   ?>

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

<? if($w == '') { ?>
		if(f.wr_type.value == 'UPLOAD') {
			if(document.getElementById('wr_file').value == '') { 
				alert("업로드할 로그를 등록해 주시길 바랍니다.");
				return false;
			}
		} else if(f.wr_type.value == 'URL') { 
			if(document.getElementById('wr_url').value == '') { 
				alert("등록할 로그 URL을 입력해 주시길 바랍니다.");
				return false;
			}
		}
<? } ?>
		document.getElementById("btn_submit").disabled = "disabled";
		return true;
	}


	$('.view_image_area').on('change', function() {
		var image = $(this).val();
		var type = $(this).attr('type');

		if(type == 'file') {
			$('#wr_homepage').val('');
			previewImage(this,'view_image');
		} else {
			$('#wr_file').replaceWith( $('#wr_file').clone(true) );

			checkImage(image, complete, '', 'view_image');
		}
	});

	function reset_image(previewId) {
		var preview = document.getElementById(previewId);
		var prevImg = document.getElementById("prev_" + previewId); //이전에 미리보기가 있다면 삭제
		if (prevImg) {
			preview.removeChild(prevImg);
		}

		$('#wr_width').val('');
		$('#wr_height').val('');

		$('#view_image > span').text("");
	}

	function previewImage(targetObj, previewId) {
		var preview = document.getElementById(previewId); //div id   
		var ua = window.navigator.userAgent;
		var files = targetObj.files;

		$('#view_image_loading').show();

		reset_image(previewId);

		for ( var i = 0; i < files.length; i++) {

			var file = files[i];

			var imageType = /image.*/; //이미지 파일일경우만.. 뿌려준다.
			if (!file.type.match(imageType)) {
				continue;
			}

			var img = document.createElement("img");
			img.id = "prev_" + previewId;
			img.classList.add("obj");
			img.file = file;

			if (window.FileReader) { // FireFox, Chrome, Opera 확인.
				var reader = new FileReader();
				reader.onloadend = (function(aImg) {
					return function(e) {
						aImg.src = e.target.result;
						complete('S', aImg.width, aImg.height);
						$('#view_image_loading').hide();
						preview.appendChild(img);
					};
				})(img);
				reader.readAsDataURL(file);
			} else { // safari is not supported FileReader
				//alert('not supported FileReader');
				if (!document.getElementById("sfr_preview_error_"
						+ previewId)) {
					var info = document.createElement("p");
					info.id = "sfr_preview_error_" + previewId;
					info.innerHTML = "not supported FileReader";
					preview.insertBefore(info, null);
				}
			}
		}
		
		if(i > 0) { 
			
			//preview.style.background="none";
		} else {
			complete('F');
		}
	}


	function checkImage(url, callback, timeout, previewId) {
		timeout = timeout || 5000;
		
		$('#view_image_loading').show();

		var timedOut = false, timer;
		var img = new Image();
		var preview = document.getElementById(previewId);

		reset_image(previewId);

		img.onerror = img.onabort = function() {
			if (!timedOut) {
				clearTimeout(timer);
				callback("F");
			}
		};
		img.onload = function() {
			if (!timedOut) {
				clearTimeout(timer);
				img.id = "prev_" + previewId;
				img.classList.add("obj");
				callback("S", img.width, img.height);
				preview.appendChild(img);
				$('#view_image_loading').hide();
			}
		};
		img.src = url;

		timer = setTimeout(function() {
			timedOut = true;
			callback("F");
		}, timeout); 
	}

	function complete(message, w, h) {
		if(message == 'S') { 
			$('#wr_width').val(w);
			$('#wr_height').val(h);
			$('#view_image > span').text("width : " + w + "px / height : " + h + "px");
		} else { 
			$('#view_image > span').text("");
		}
	}

	function fn_log_type(type) { 
		$('#add_'+type).siblings().hide();
		$('#add_'+type).show();

		$('#wr_url').val('');
		$('#wr_file').replaceWith( $('#wr_file').clone(true) );

		reset_image('view_image');
	}
</script>

<script>
$('#action').on('change', function() {
	var view_idx = $(this).val();
	$('.comment-data').removeClass('on');
	$('#action_' + view_idx).addClass('on');
});

$('.change-thumb').on('change', function() {
	var select_item = $(this).find('option:selected');

	var thumb = select_item.data('thumb');

	if(typeof(thumb) != "undefined") {
		// 썸네일이 있는 경우
		$(this).closest('.has-thumb').find('.ui-thumb').empty().append("<img src='"+thumb+"' alt='' />");
	} else { 
		$(this).closest('.has-thumb').find('.ui-thumb').empty();
	}
});


$('#fwrite select').change(function() {
	$('#fwrite select').find("option").attr('disabled', false);
	$('#fwrite select').each(function() {
		if($(this).val()) { 
			$('#fwrite select').not(this).find("option[value="+ $(this).val() + "]").attr('disabled', true);
		}
	});
});


</script>

<? } ?>