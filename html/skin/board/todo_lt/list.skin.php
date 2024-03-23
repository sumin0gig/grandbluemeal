<?php
if (empty($_GET['dic_cate'])) {
    include_once 'dic_list.php';
} else {
    include_once 'dic_show.php';
}
?>