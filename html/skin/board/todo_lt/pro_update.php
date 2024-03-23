<?php
include_once('./_common.php');



$wr = get_write($write_table, $wr_id);

if (isset($_POST['wr_content'])) {
	$wr_content = trim($_POST['wr_content']);
    $wr_content = preg_replace("#[\\\]+$#", "", $wr_content);
}

$wr_6 = 0;

if ($wr_2 == 1) :
	if ($wr_3 >= 100) :
		$wr_6 = 1;
	endif;
elseif ($wr_2 == 2) :
	if ( (($wr_4/$wr_5)*100) >= 100 ) :
		$wr_6 = 1;
	endif;
endif;

if ($member['mb_id']) {
    
    $sql = " update {$write_table}
                    set wr_content = '{$wr_content}',
                        wr_2 = '{$wr_2}',
                        wr_3 = '{$wr_3}',
                        wr_4 = '{$wr_4}',
                        wr_5 = '{$wr_5}',
                        wr_6 = '{$wr_6}'
                where wr_id = '{$wr['wr_id']}' ";

    sql_query($sql);
}

goto_url(G5_HTTP_BBS_URL.'/board.php?bo_table='.$bo_table);
?>

