<?php

/**
  @title Live Map
  @author Valware
  @description View a world map showing how many connections you have from each country
  @contact valerie@valware.co.uk
  @version 1.0
  @tested 0.9
  @minver 0.9
  @maxver *
  @license GPLv3
  @icon https://cdn-icons-png.flaticon.com/512/235/235861.png
  @screenshot https://i.ibb.co/0qXDNHv/image.png
*/

class live_map
{
	/* You must specify these here for internal use
 	 * All of these are mandatory or your plugin will not work.
 	*/
	public $name = "live_map"; // Name of your plugin
	public $author = "Valware"; // Name or handle of your lovely self
	public $version = "1.0"; // Version of this plugin
	public $description = "View a list of users who are live_mapd (requires third/live_map)"; // Description of your beautiful plugin
	public $email = "v.a.pond@outlook.com"; // An email people can contact you with in case of problems

	/** This is run on plugin load. You can add hooks and initialize whatever databases
 	 * that you think you might need.
   	*/
	function __construct()
	{
		/**The hook for the navigation bar.
  		 * The first argument is the HOOKTYPE we're using. In this case
     		 * we're using NAVBAR to add our page to the navigation bar.
		 * For a full list of hooks, see this page:
		 * https://github.com/unrealircd/unrealircd-webpanel/blob/main/Classes/class-hook.php
		 *
     		 * The second argument references the function you want to include
		 * in your hooked function. In this example we are referencing a
		 * method (function) in this class (live_map)
		*/
		Hook::func(HOOKTYPE_NAVBAR, 'live_map::add_navbar'); 
	}

	/** This is the method (function) that we have hooked.
 	 * Now we can do things. Make sure to check the relevant hooks
    	 * for the right variables to use.
 	 * We use variable reference (&) on this hook
   	*/
	public static function add_navbar(&$pages)
	{
		$page_name = "Live Map";
		$page_link = "plugins/live_map";
		$pages["Tools"][$page_name] = ["script" => $page_link, "no_irc_server_required" => false];
	}
}
