<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

if(defined('G5_THEME_PATH')) {
	require_once(G5_THEME_PATH.'/head.php');
	return;
}


include_once(G5_PATH.'/head.sub.php');
include_once(G5_LIB_PATH.'/latest.lib.php');
include_once(G5_LIB_PATH.'/outlogin.lib.php');
include_once(G5_LIB_PATH.'/poll.lib.php');
include_once(G5_LIB_PATH.'/visit.lib.php');
include_once(G5_LIB_PATH.'/connect.lib.php');
include_once(G5_LIB_PATH.'/popular.lib.php');
add_stylesheet('<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.8.2/css/all.min.css" />', 0);

/*********** Logo Data ************/
$logo = get_logo('pc');
$m_logo = get_logo('mo');

$logo_data = "";
if($logo)		$logo_data .= "<img src='".$logo."' ";
if($m_logo)		$logo_data .= "class='only-pc' style='max-height: 100px;' /><img src='".$m_logo."' class='not-pc'";
if($logo_data)	$logo_data.= " />";
/*********************************/

?>

<!-- 헤더 영역 -->
<style>
	.menu_btn{
		position: fixed;
		transform: translateX(50%);
		background-size: contain;
		width: 15vh;
		height: 15vh;
		left: calc(50% - 600px - 3vh );
		display: block;
		cursor: pointer;
		transition: .2s;
	}
	@keyframes shake {
		0% { transform: translateX(50%) rotate(-5deg); }
		50% { transform: translateX(50%) rotate(5deg); }
		100% { transform: translateX(50%) rotate(-5deg); }
	}
	.menu_btn:hover{
		animation: shake 0.4s infinite; /* 호버 시 애니메이션 작동 */
	}
</style>

<header id="header" style="z-index:9;">
		<!-- 로고 영역 : PC 로고 / 모바일 로고 동시 출력 - 디자인 사용을 체크하지 않을 시, 제대로 출력되지 않을 수 있습니다. -->
		<!-- 관리자 기능을 사용하지 않고 로고를 넣고 싶을 시, < ? = $ log_data ? > 항목을 제거 하고 <img> 태그를 넣으세요. -->

		<!-- 모바일 모드에서 메뉴를 열고 닫기 할 수 있는 버튼 -->
		<a href="#gnb" id="gnb_control_box">
			<img src="<?=G5_IMG_URL?>/ico_menu_control_pannel.png" alt="메뉴열고닫기" />
		</a>
		<script>
		$('#gnb_control_box').on('click', function() {
			$('body').toggleClass('open-gnb');
			return false;
		});
		</script>
		<!-- 모바일 메뉴 열고 닫기 버튼 종료 -->

		<div id="gnb">
			<?
			$menu_content = get_site_content('site_menu');
			if($menu_content) { 
				echo $menu_content;
			} else { 
			?>
			
			<ul class="h_menu_ul">
				
				<li onClick="location.href='<?=G5_URL?>'" >
				<a class="menu_img"> <i class="fas fa-home"></i> Main</a>
				</li>

				<li onClick="location.href='<?=G5_URL?>/member'">
				<a class="menu_img" > <i class="fas fa-heart"></i> MEMBER</a>
				</li>

				<li onClick="location.href='<?=G5_URL?>/bbs/board.php?bo_table=mmb'">
				<a class="menu_img" > <i class="fas fa-clipboard"></i> MAP</a>
				</li>

				<li onClick="location.href='<?=G5_URL?>/bbs/board.php?bo_table=todo'">
				<a class="menu_img" > <i class="fas fa-clipboard"></i> RECIPI BOOK</a>
				</li>

				<li onClick="location.href='<?=G5_URL?>/bbs/board.php?bo_table=cook'">
				<a class="menu_img" > <i class="fas fa-clipboard"></i> COOK </a>
				</li>

				<li onClick="location.href='<?=G5_URL?>/shop/?br=음식'">
				<a class="menu_img" > <i class="fas fa-clipboard"></i> SHOP</a>
				</li>


				<li class="menu_btn only-pc" style="top:10%; background-image: url(https://i.imgur.com/srE9e2x.png);"
					onClick="location.href='<?=G5_URL?>/member'"></li>
				<li class="menu_btn only-pc" style="top:20%; background-image: url(https://i.imgur.com/3OgKwLQ.png); "
					onClick="location.href='<?=G5_URL?>/bbs/board.php?bo_table=todo'"></li>

				</ul>
				<? include(G5_PATH."/templete/txt.bgm.php"); ?>

			<?php } ?>

		</div>
</header>
<!-- // 헤더 영역 -->

<section id="body">
	<div class="fix-layout">
