<style>
#pages{display:-webkit-box;display:-webkit-flex;display:-ms-flexbox;display:flex;-webkit-flex-wrap:wrap;-ms-flex-wrap:wrap;flex-wrap:wrap;-webkit-box-pack:justify;-webkit-justify-content:space-between;-ms-flex-pack:justify;justify-content:space-between}
#pages .page{background-color:transparent;border:1px solid #ddd;margin-bottom:4px;width:calc(50% - 2px);position:relative;z-index:2}
#pages .rebuild{background-color:#efefef;border:2px solid #ddd;font-size:1.25rem;line-height:1.75rem;margin-top:10px;text-align:center;width:100%}
#pages .rebuild:hover{background-color:#ebebeb;cursor:pointer}
#pages .page span{display:block;padding:3px;position:relative;width:100%}
#pages .page span .progress{background-color:#efefef;height:100%;position:absolute;top:0;left:0;width:0;z-index:-1;-webkit-transition:width .3s ease;transition:width .3s ease}
</style>

<div id="pages">
	<?php
	// Start listing all the pages
	foreach(panel()->site()->index() as $key):
		// If the page is in the ignore list, skip it
		if (in_array($key, c::get("panel.widget.cacheBuilder.ignore"))) continue; ?>

	<div class="page" data-name="<?=$key?>">
		<span class="title"><?=$key->title()?> <div class="progress"></div></span>
	</div>

	<?php endforeach; ?>

	<div class="rebuild">Rebuild Cache</div>
</div>

<script src="<?=c::get("panel.widget.cacheBuilder.jquery")?>"></script>
<script>
$(document).ready(function() {

	// Crate array of all pages on site
	var pages = [
	<?php
	foreach(panel()->site()->index() as $page):
		// If the page is in the ignore list, skip it
		if (in_array($page, c::get("panel.widget.cacheBuilder.ignore"))) continue;
		echo "{ url: '{$page->url()}', slug: '{$page}'},";
	endforeach;
	?>
	];
	var i = 0;

	function buildCache() {
		if (i < pages.length) {
			$("[data-name='"+ pages[i].slug +"'] .progress").css("width", "75%");
			$.ajax({
				cache: false,
				url: pages[i].url,
				success: function() {
					$("[data-name='"+ pages[i].slug +"'] .progress").css("width", "100%");
					i++;
					buildCache();	
				}
			});
		}
	}

	$(".rebuild").click(function() {
		// Reset progress bars to 0
		$(".progress").css("width", "0%");
		i = 0;
		// Purges the cache
		$.get("<?=u("purgeCache")?>");
		// Builds the cache
		buildCache();
	});
});
</script>