<?
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가
include_once(G5_LIB_PATH.'/thumbnail.lib.php');
include_once('./_common.php');
add_stylesheet('<link rel="stylesheet" href="'.$board_skin_url.'/style.css">', 0);
add_stylesheet('<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.8.2/css/all.min.css" />', 0);
?>

<?php
$sst = $_GET['sst'] ?? "dic_show";
$sod = $_GET['sod'] ?? "DESC";
$dic_cate = $_GET['dic_cate'];
$ca_name = $_GET['ca_name'];

switch ($dic_cate) {
  case '낚시':
      $show_cate = 'it_use_fishing';
      break;
  case '탐색':
      $show_cate = 'it_use_search';
      break;
  default:
      $show_cate = 'it_use_reward';
      break;
}

// ---- count 가져오기
if($ca_name == "레시피") {
  $sql = "SELECT count(*) as cnt FROM {$g5['recepi_table']} 
  WHERE re_use = 1 AND re_dic_cate = '{$dic_cate}'";
  
} else {
  $sql = "SELECT count(*) as cnt FROM {$g5['item_table']}
  WHERE it_use = 'Y' AND $show_cate = 1";

}

$row = sql_fetch($sql);
$total_count = $row['cnt'];

$rows = $config['cf_page_rows'];
$total_page  = ceil($total_count / $rows);  // 전체 페이지 계산
if ($page < 1) $page = 1; // 페이지가 없으면 첫 페이지 (1 페이지)
$from_record = ($page - 1) * $rows; // 시작 열을 구함


// ---- 아이템 가져오기
if($ca_name == "레시피") {
  $query_result = sql_query("SELECT it_id, re_item_order, dic_show FROM {$g5['recepi_table']} 
  WHERE re_use = 1 AND re_dic_cate = '{$dic_cate}'
  ORDER BY {$sst} {$sod}
  limit {$from_record}, {$rows} ");
    
} else {
  $query_result = sql_query("SELECT it_id, dic_show FROM {$g5['item_table']}
  WHERE it_use = 'Y' AND $show_cate = 1
  ORDER BY {$sst} {$sod}
  limit {$from_record}, {$rows} ");

}

?>

<a href="./board.php?bo_table=<?=$bo_table?>"> <h2>< 목록</h2> </a>

<ul class="list_dic">
<?php 
  if (!is_null($query_result) && mysqli_num_rows($query_result) > 0):
    while ($row = mysqli_fetch_assoc($query_result)): 
      $re_item = explode("||", $row['re_item_order']);
?>
      <li>
        <div class="item_info">
          <div class="img"> <img src='<?= $row['dic_show'] == true  ? get_item_img($row['it_id']) : 'wer.png' ?>' style='width: 30px;' /> </div>
          <h4> <?= $row['dic_show'] == true  ? get_item_name($row['it_id']) : "???" ?> </h4>
        </div>
        <? if($ca_name == "레시피") { ?>
          <div class="item_recipe"> 
            <div>
              <?php if($row['dic_show'] == true){
                foreach ($re_item as $item) {
                  echo "<div>";
                  $img_url =  $row['dic_show'] == true ? get_item_img($item) : 'wer.png' ;
                  if ($img_url) {
                    echo "<img src='" . $img_url . "' style='width: 30px;' />";
                  }
                  echo get_item_name($item);
                  echo "</div>";
                }
              }else{ ?>
                <div> ??? </div>
                <div> ??? </div>
                <div> ??? </div>
              <? }
              ?>
            </div> 
          </div>
        <? } ?>
        <div>
          <?=  $row['dic_show'] == true  ? get_item_desc($row['it_id']) : "???" ?>
        </div>

      </li>
    <?php 
      endwhile;
      else:
    ?>
      <div> 조건에 부합하는 메모가 없습니다. </div>
<?php 
  endif;
?>
</ul>

<?php echo get_paging( $rows, $page, $total_page, "{$_SERVER['PHP_SELF']}?bo_table=$bo_table&amp;ca_name=$ca_name&amp;dic_cate=$dic_cate&amp;page="); ?>
