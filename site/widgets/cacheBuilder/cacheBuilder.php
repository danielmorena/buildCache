<?php

if (!c::get("panel.widget.cacheBuilder.ignore")) c::set("panel.widget.cacheBuilder.ignore", array("error"));
if (!c::get("panel.widget.cacheBuilder.allowed")) c::set("panel.widget.cacheBuilder.allowed", array("admin"));
if (!c::get("panel.widget.cacheBuilder.allowed.type")) c::set("panel.widget.cacheBuilder.allowed.type", "role");

switch (c::get("panel.widget.cacheBuilder.allowed.type")) {
	case "role":
		$allowedType = panel()->user()->role();
		break;
	case "username":
		$allowedType = panel()->user()->username();
		break;
	case "email":
		$allowedType = panel()->user()->email();
		break;
	default:
		$allowedType = panel()->user()->role();
		break;
}

if(in_array($allowedType, c::get("panel.widget.cacheBuilder.allowed"))):
	return array(
		"title" => "Cache Builder",
		"html" => function() {
			return tpl::load(__DIR__ . DS . "cacheBuilder.html.php");
		}
	);
else:
	return false;
endif;