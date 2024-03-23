<?php 
include_once('./_common.php');

$write = sql_fetch("select * from {$write_table} where wr_id='{$wr_id}'" );

?>

<?//=print_r2($write); ?>

<section id="progress_edit">
    <form name="fwrite" id="fwrite" action="<?=$board_skin_url; ?>/pro_update.php" onsubmit="return fwrite_submit(this);" method="post" enctype="multipart/form-data" autocomplete="off">
        <input type="hidden" name="bo_table" value="<?php echo $bo_table ?>">
        <input type="hidden" name="wr_id" value="<?php echo $wr_id ?>">
        <div class="board-write">
            <dl>
                <dt><span class="required">진행도 타입</span></dt>
                <dd>
                    <input type="radio" id="progress" name="wr_2" value="1"<?=($write['wr_2'] == 1 || !$write['wr_2']) ? ' checked="checked"' : '' ?> />
                    <label for="progress">퍼센트</label>
                    <input type="radio" id="progress2" name="wr_2" value="2"<?=($write['wr_2'] == 2) ? ' checked="checked"' : '' ?> />
                    <label for="progress2">목표수치</label>
                    <div class="div_pro pro_1"<?=($write['wr_2'] == 2) ? ' style="display:none;"' : ''; ?>>
                        <input type="number" value="<?=$write['wr_3']; ?>" class="frm_input" name="wr_3" placeholder="달성도" max="100"<?=($write['wr_2'] == 2) ? '' : ' required'; ?> /> %
                    </div>
                    <div class="div_pro pro_2"<?=($write['wr_2'] == 2) ? '' : ' style="display:none;"'; ?>>
                        <input type="number" value="<?=$write['wr_4']; ?>" class="frm_input" name="wr_4" placeholder="달성도" /> / <input type="number" value="<?=$write['wr_5']; ?>" name="wr_5" min="1" class="frm_input" placeholder="목표치" />
                    </div>	
					<input type="hidden" name="wr_6" name="wr_6" value="<?=$write['wr_6']; ?>" />		
                </dd>
            </dl>
            <dl>
                <dt><label for="wr_subject">한줄메모</label></dt>
                <dd><input type="text" value="<?=$write['wr_content'] ?>" id="wr_content" name="wr_content" class="frm_input full" /></dd>
            </dl>
            <div class="btn_confirm txt-center">
                <input type="submit" value="작성완료" id="btn_submit" accesskey="s" class="btn_submit ui-btn point">
                <a href="javascript:void(0)" onclick="$('.pro_edit').empty();$('.pro_edit').removeClass('on');" class="btn_cancel ui-btn">취소</a>
            </div>
        </div>
    </form>
</section>

<script>
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

    $(function(e){
        $(document).on('change', 'input[name="wr_2"]', function(){
			var pro_value = $(this).val();
			
			$('.div_pro').hide();
			$('.div_pro input').attr('required', false);
            $('.div_pro input').val('');
			$('.pro_' + pro_value).show();
			$('.pro_' + pro_value + ' input').attr('required', true);
		});
    })
</script>