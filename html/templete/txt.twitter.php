<? if($config['cf_twitter']) { ?>
<div class="twitter theme-box" style="opacity: 1; padding: 15px 10px; height: 310px;">
	<div class="twitter-timeline" style="width: 100%; max-width: 100%; ">
		<div id="twitter-widget-0">
		</div>
		
		<iframe id="twitter-iframe" frameborder="0" allowtransparency="true" allowfullscreen="true" class="" style="position: static; visibility: visible;  height: 300px; display: block; flex-grow: 1;" title="Twitter Timeline"
		src="https://syndication.twitter.com/srv/timeline-profile/screen-name/<?=$config['cf_twitter']?>?dnt=false&amp;frame=false&amp;hideBorder=true&amp;hideFooter=true&amp;hideHeader=true&amp;showHeader=false&amp;lang=ko&amp;maxHeight=310px&amp;showReplies=false&amp;transparent=true"></iframe>


		<script async="" src="https://platform.twitter.com/widgets.js" charset="utf-8"></script>
		
		<!-- <a class="twitter-timeline" data-lang="ko" allowtransparency="true" allowfullscreen="true"
		href="https://twitter.com/<?=$config['cf_twitter']?>?ref_src=twsrc%5Etfw">
			트위터 로딩중...
		</a> -->
	</div>
</div>


<? } ?>
