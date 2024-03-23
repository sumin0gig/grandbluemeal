<ul class="theme-box" id="inventory_area">

  <?
    $re_result = sql_query("select * from {$g5['inventory_table']} inven, {$g5['item_table']} it where inven.ch_id = '{$character['ch_id']}' and it.it_use_recepi = 1 and inven.it_id = it.it_id  ORDER BY inven.it_name ASC");
    if(sql_fetch_array($re_result) == 0 ){
  ?>
    소지하고 있는 요리 재료가 없습니다.
  <?
    } else {
      foreach ($re_result as $re_row) {
  ?>
    <li value="<?=$re_row['in_id']?>" onclick="make_arr(this,'<?=$re_row['it_name']?>')">
      <div>
        <img src="<?=$re_row['it_img']?>"/>
        <span><?=$re_row['it_name']?></span>
      </div>
    </li>
  <? } }?>

    </ul>

<style>
  ul#inventory_area > li{
    display: inline-block;
    position: relative;
    text-align: center;
    margin-bottom: 10px;
    font-size: 11px;
    cursor: pointer;
    border-radius: 7px;
  }
  ul#inventory_area > li > div{
    display: flex;
    align-items: center;
    flex-direction: column;
    position: relative;
    margin: 0 0.5rem;
  }
  .selected{
    background-color: rgba(255,255,255,.3);
  }
  .disabled{
    background-color: rgba(0,0,0,.3);
    filter: brightness(0.7);
    cursor: default!important;
  }
</style>

<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가
add_stylesheet('<link rel="stylesheet" href="'.$board_skin_url.'/style.css">', 0);

$is_error = false;
$option = '';
$option_hidden = '';

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
	<section id="bo_w" class="mmb-board">
    <!-- 게시물 작성/수정 시작 { -->
    <form name="fwrite" id="fwrite" action="http://grandbluemeal.dothome.co.kr/bbs/cook_update.php" onsubmit="return fwrite_submit(this);" method="post" enctype="multipart/form-data" autocomplete="off">
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
			<input type="hidden" name="wr_subject" value="subject" />
			<input type="hidden" name="wr_width" id="wr_width" value="<?php echo $write['wr_width']; ?>">
			<input type="hidden" name="wr_height" id="wr_height" value="<?php echo $write['wr_height']; ?>">
			<?php echo $option_hidden; ?>
      
      <!-- LOG 등록 부분 -->
      <dl class='hidden'>
        <dt>
          <input name="wr_type" value="URL"/>
        </dt>

        <dd>
          <div id="add_URL">
            <input type="text" name="wr_url" value="cook" title="이미지 링크 없음" id="wr_url"/>
          </div>
        </dd>
        
      </dl>
      
      <div class="theme-box hidden">
      <?php if ($is_category) { ?>
        <ul id="board_category">
          <?php echo $category_option ?>
        </ul>
      <?php } ?>

      <? if(!$write['wr_log'] && $character['ch_state']=='승인') { ?>
        <div id="board_action" class="inner">

          <input id="action" name="action" value="H" class='hidden'/>

          <div id="action_H">
          <?
          // 조합 커멘드 관련 입력
          ?>
            <dl>
              <dt>ITEM 1</dt>
              <dd>
                <input name="make_1" id="make_1" class="make-imtem">
              </dd>
            </dl>
            <dl>
              <dt>ITEM 2</dt>
              <dd>
                <input name="make_2" id="make_2" class="make-imtem">
              </dd>
            </dl>
            <dl>
              <dt>ITEM 3</dt>
              <dd>
                <input name="make_3" id="make_3" class="make-imtem" >
              </dd>
            </dl>
          </div>
          
        </div>
      <? } else { ?>
        <div> 대표 캐릭터를 승인 처리 된 캐릭터로 변경해주십시오. </div>
      <? }	?>

        
      </div>
      
      <hr class="padding small" />

      <div class="comments hidden">
        <textarea id="wr_content" name="wr_content" class="hidden" maxlength="65536" style="width:100%;height:auto"></textarea>        
        <div id="char_count_wrap"><span id="char_count">글자</span></div>
      </div>
      
      <hr class="padding" />

      <div class="txt-center">
        <button type="submit" id="btn_submit" accesskey="s" class="ui-btn">만들기</button>
        <button type="button" onclick="location.href='./cook.php?bo_table=<?=$bo_table?>';" class="ui-btn">새로고침</button>
      </div>
    </form>

  </section>
</div>

<script>
  let arr = []
  let item_name_hash = {}

  let filteredObject = {};
  const items = document.querySelectorAll("ul#inventory_area > li");
 
  const make_arr = (e,item_name) => {
    let val = e.getAttribute('value')
    item_name = "「 " + item_name + " 」"

    if (arr.includes( val )) {
      // 이미 클릭 되어 있으면 제거


      items.forEach( i =>  i.classList.remove('disabled') );

      arr = arr.filter( i => i != val);

      delete item_name_hash[val];
      e.classList.remove('selected');

    } else if(arr.length < 3){
      // 들어와 있는 값이 아니고 length가 3 이하면 추가

      arr.push( val );
      e.classList.add('selected');
      item_name_hash[val] = item_name;

      if(arr.length >= 3){
        items.forEach( i =>{
          if(i.classList.contains('selected') != true){
            i.classList.add('disabled')
          }
        });
      }

    }

    $("#make_1").val(arr[0])    
    $("#make_2").val(arr[1])    
    $("#make_3").val(arr[2])

    $('#wr_content').html( Object.values(item_name_hash).join(" ") + "을(를) 사용했다." );
  }

  function fwrite_submit(f){

    if (f.wr_content.value == ""){
      alert("요리 재료를 선택해 주세요.");
      return false
    }

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

    document.getElementById("btn_submit").disabled = "disabled";
    return true;
  }

</script>

<? } ?>