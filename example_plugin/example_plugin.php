<?php

/**
  @title Example Plugin
  @author Valware
  @description This plugin adds an example page and does nothing special by itself. Have a nice day!
  @contact valerie@valware.co.uk
  @version 1.0
  @tested 0.9
  @minver 0.9
  @maxver *
  @license GPLv3
  @icon https://github.com/unrealircd/unrealircd-webpanel-plugins/blob/main/example_plugin/screenshots/example_icon.png?raw=true
  @screenshot screenshots/example_plugin.png
  @screenshot screenshots/example_plugin2.png
*/

class example_plugin
{
	public $name = "Example plugin";
	public $author = "Valware";
	public $version = "1.0";
	public $description = "An example plugin to show how to make stuff";
	public $email = "v.a.pond@outlook.com";

	function __construct()
	{
		Hook::func(HOOKTYPE_NAVBAR, 'example_plugin::add_navbar'); 
	}

	public static function add_navbar(&$pages)
	{
		$page_name = "Example";
		$page_link = "plugins/example_plugin/example.php";
		$pages[$page_name] = $page_link;
	}
}

