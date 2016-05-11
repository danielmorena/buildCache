<?php

if (!c::get("panel.widget.cacheBuilder.ignore")) c::set("panel.widget.cacheBuilder.ignore", array("error"));
if (!c::get("panel.widget.cacheBuilder.jquery")) c::set("panel.widget.cacheBuilder.jquery", u("assets/js/libs/jquery.js"));

return array(
	"title" => "Cache Builder",
	"html" => function() {
		return tpl::load(__DIR__ . DS . "cacheBuilder.html.php");
	}
);