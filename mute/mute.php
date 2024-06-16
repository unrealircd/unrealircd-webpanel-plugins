<?php

/**
  @title Mute Users
  @author Valware
  @description View a list of users who are muted (requires third/mute)
  @contact valerie@valware.co.uk
  @version 1.0
  @tested 0.9
  @minver 0.9
  @maxver *
  @license GPLv3
  @icon https://static.thenounproject.com/png/10454-200.png
*/

class mute
{
	/* You must specify these here for internal use
 	 * All of these are mandatory or your plugin will not work.
 	*/
	public $name = "mute"; // Name of your plugin
	public $author = "Valware"; // Name or handle of your lovely self
	public $version = "1.0"; // Version of this plugin
	public $description = "View a list of users who are muted (requires third/mute)"; // Description of your beautiful plugin
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
		 * method (function) in this class (mute)
		*/
		Hook::func(HOOKTYPE_NAVBAR, 'mute::add_navbar'); 
	}

	/** This is the method (function) that we have hooked.
 	 * Now we can do things. Make sure to check the relevant hooks
    	 * for the right variables to use.
 	 * We use variable reference (&) on this hook
   	*/
	public static function add_navbar(&$pages)
	{
		$page_name = "Mutes";
		$page_link = "plugins/mute";
		$pages["Server Bans"][$page_name] = ["script" => $page_link, "no_irc_server_required" => true];
	}

}
