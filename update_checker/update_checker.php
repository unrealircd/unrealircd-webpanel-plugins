<?php
/*
  --======-- Example Plugin --=======--

  Below are the credentials needed.
  You must specify them in this format.
  The only things that are not mandatory
  are icons and screenshots. the rest
  are mandatory or your plugin will not
  be accepted.

*/
/**
  @title Example Plugin
  @author Madrix
  @description Enables searching for updates on the GitHub repository and updating.
  @contact 
  @version 1.0
  @tested 0.9
  @minver 0.9
  @maxver *
  @license GPLv3
  @icon https://github.com/unrealircd/unrealircd-webpanel-plugins/blob/main/example_plugin/screenshots/example_icon.png?raw=true
  @screenshot https://github.com/unrealircd/unrealircd-webpanel-plugins/blob/main/example_plugin/screenshots/example_plugin2.jpg?raw=true
  @screenshot https://github.com/unrealircd/unrealircd-webpanel-plugins/blob/main/example_plugin/screenshots/example_plugin.jpg?raw=true
*/

class update_checker
{
	/* You must specify these here for internal use
 	 * All of these are mandatory or your plugin will not work.
 	*/
	public $name = "Update Checker"; // Name of your plugin
	public $author = "Madrix"; // Name or handle of your lovely self
	public $version = "1.0"; // Version of this plugin
	public $description = "Enables searching for updates on the GitHub repository and updating."; // Description of your beautiful plugin
	public $email = ""; // An email people can contact you with in case of problems

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
		 * method (function) in this class (example_plugin)
		*/
		Hook::func(HOOKTYPE_NAVBAR, 'update_checker::add_navbar'); 
	}

	/** This is the method (function) that we have hooked.
 	 * Now we can do things. Make sure to check the relevant hooks
    	 * for the right variables to use.
 	 * We use variable reference (&) on this hook
   	*/
	public static function add_navbar(&$pages)
	{
		$page_name = "Update Checker";
		$page_link = "plugins/update_checker/UpdateChecker.php";
		$pages[$page_name] = ["script" => $page_link, "no_irc_server_required" => true];
	}
}

