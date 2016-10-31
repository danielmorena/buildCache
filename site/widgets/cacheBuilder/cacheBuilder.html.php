<style>
#pages{display:-webkit-box;display:-webkit-flex;display:-ms-flexbox;display:flex;-webkit-flex-wrap:wrap;-ms-flex-wrap:wrap;flex-wrap:wrap;-webkit-box-pack:justify;-webkit-justify-content:space-between;-ms-flex-pack:justify;justify-content:space-between}
#pages .page{background-color:transparent;border:1px solid #ddd;margin-bottom:4px;width:calc(50% - 2px);position:relative;z-index:2}
#pages .page span{display:block;padding:3px;position:relative;width:100%}
#pages .page span .progress{background-color:#efefef;height:100%;position:absolute;top:0;left:0;width:0;z-index:-1;-webkit-transition:width .3s ease;transition:width .3s ease}
#pages .lang { border-bottom: 2px solid #000; font-weight: bold; margin: 15px 0 10px; width: 100%; }
#pages #rebuild{background-color:#efefef;border:2px solid #ddd;font-size:1.25rem;line-height:1.75rem;margin-top:10px;text-align:center;width:100%}
#pages #rebuild:hover{background-color:#ebebeb;cursor:pointer}
</style>

<div id="pages">
	<?php
	foreach(panel()->site()->languages() as $lang): ?>
		<div class="lang"><?=$lang->name()?></div>
		<?php // Start listing all the pages
		foreach(panel()->site()->children() as $page):
			// If the page is in the ignore list, skip it
			if (in_array($page, c::get("panel.widget.cacheBuilder.ignore"))) continue;
	?>

	<div class="page" data-name="<?=$page->slug($lang->code())?>" data-lang="<?=$lang->code()?>">
		<span class="title"><?=$page->title()?> <div class="progress"></div></span>
	</div>

	<?php
	if ($page->hasChildren()):
		foreach ($page->children() as $child):
			if (in_array($child, c::get("panel.widget.cacheBuilder.ignore"))) continue;
	?>

	<div class="page" data-name="<?=$child->slug($lang->code())?>" data-lang="<?=$lang->code()?>">
		<span class="title"><?=$child->title()?> <div class="progress"></div></span>
	</div>
	<?php
		endforeach;
	endif;
	?>

		<?php endforeach; ?>
	<?php endforeach; ?>

	<div id="rebuild">Rebuild Cache</div>
</div>

<script>
	// Crate array of all pages on site
	var pages = [
	<?php
	$site = panel()->site();
	foreach ($site->languages() as $lang):
		foreach($site->children() as $page):
			// If the page is in the ignore list, skip it
			if (in_array($page, c::get("panel.widget.cacheBuilder.ignore"))) continue;
			echo "{ url: '{$site->url()}/{$lang}/{$page->slug($lang->code())}', slug: '{$page->slug($lang->code())}', lang: '{$lang}'},";

			if ($page->hasChildren()):
				foreach ($page->children() as $child):
					echo "{ url: '{$site->url()}/{$lang}/{$page->slug($lang->code())}/{$child->slug($lang->code())}', slug: '{$child->slug($lang->code())}', lang: '{$lang}'},\n";
				endforeach;
			endif;
		endforeach;
		echo "\n";
	endforeach;
	?>
	];
	var i = 0;
	var rebuildButton = document.getElementById("rebuild");
	var progressBars = document.getElementsByClassName("progress");
	var x = 0;


	// Listen to click
	rebuildButton.addEventListener("click", function() {
		rebuildButton.style.backgroundColor = "#efefef";
		rebuildButton.style.borderColor = "#ddd";
		rebuildButton.style.color = "#000";
		// Reset progress bars to 0
		for(x = 0; x < progressBars.length; x++) { progressBars[x].style.width = "0%"; }
		i = 0;

		// Purges the cache
		var xmlhttp = new XMLHttpRequest();
		xmlhttp.open("POST", "<?=u("purgeCache")?>");
		xmlhttp.send();
		buildCache();
	}, false);

	var buildCache = function() {
		if (i < pages.length) {
			// Select progressbar by it's parent's [data-name]
			var pageProgress = document.querySelector("[data-name='"+ pages[i].slug +"'][data-lang='"+ pages[i].lang +"'] .progress");
			// Set progress bar to 75%;
			pageProgress.style.width = "75%";

			// Ready a new request
			var xmlhttp = new XMLHttpRequest();

			// POST makes sure it doesn't cache itself
			xmlhttp.open("POST", pages[i].url);
			xmlhttp.send();
			xmlhttp.onreadystatechange = function() {
				// Only fire when the POST call has returned
				if ((xmlhttp.readyState == 4) && (xmlhttp.status == 200)) {
					// Set progress bar to 100%;
					pageProgress.style.width = "100%";
					i++;

					// Proceed to next
					buildCache();
				}
			}
		}
		if (i == pages.length) {
			rebuildButton.style.backgroundColor = "#8dae28";
			rebuildButton.style.borderColor = "#8dae28";
			rebuildButton.style.color = "#fff";
		}
	}
</script>