<?php
/**
  @title Cursed
  @author Valware
  @description This plugin is cursed.
  @contact valerie@valware.co.uk
  @version 1.0
  @tested 0.9
  @minver 0.9
  @maxver *
  @license GPLv3
  @icon https://i.guim.co.uk/img/media/a1b7129c950433c9919f5670c92ef83aa1c682d9/55_344_1971_1183/master/1971.jpg?width=1200&height=900&quality=85&auto=format&fit=crop&s=88ba2531f114b9b58b9cb2d8e723abe1
*/

class cursed
{
	/* You must specify these here for internal use
 	 * All of these are mandatory or your plugin will not work.
 	*/
	public $name = "cursed"; // Name of your plugin
	public $author = "Valware"; // Name or handle of your lovely self
	public $version = "1.0"; // Version of this plugin
	public $description = "Adds cursed stuff"; // Description of your beautiful plugin
	public $email = "v.a.pond@outlook.com"; // An email people can contact you with in case of problems

	/** This is run on plugin load. You can add hooks and initialize whatever databases
 	 * that you think you might need.
   	*/
	function __construct()
	{
		
		Hook::func(HOOKTYPE_PRE_HEADER, 'cursed::do_cursed_shit'); 
	}

	public static function do_cursed_shit(&$pages)
	{
		?>
		<style>
			.emoji {
				position:absolute;
				pointer-events: none;
				background: #f00;
				animation: animate 1s linear infinite;
			}
			@keyframes animate {
				0%
				{
					translate: 0 0;
					opacity: 0;
				}
				10%
				{
					opacity: 1;
				}
				30%
				{
					translate: 0 100px;
					opacity: 1;
				}
				90%
				{
					opacity: 1;
				}
				100%
				{
					translate: 0 80px;
					opacity: 0;
				}
			}
		</style>
		<script>

			let images = ["😀", "😃", "😄", "😁", "😆", "😅", "😂", "🤣", "🥲", "😊", "😇", "🙂", "🙃", "😉", "😌", "😍", "🥰", "😘", "😗", "😙", "😚", "😋", "😛", "😝", "😜", "🤪", "🤨", "🧐", "🤓", "😎", "🥸", "🤩", "🥳", "😏", "😒", "😞", "😔", "😟", "😕", "🙁"];

			document.addEventListener('mousemove', function(e) {
				let body = document.querySelector("body");
				let emoji = document.createElement("span");
				emoji.classList.add("emoji");

				let x = e.offsetX;
				let y = e.offsetY;

				emoji.style.left = x + "px";
				emoji.style.top = y + "px";

				let icon = images[Math.floor(Math.random() * images.length)];
				emoji.innerText = icon;

				let size = Math.random() * 50;
				emoji.style.fontSize = 5 + size + "px";

				let max = 0;
				let min = 200;
				let randomValue = Math.floor((Math.random() * ((max + 1) - min)) + min);

				emoji.style.transform = 'translateX(' + randomValue + 'px)';

				body.appendChild(emoji);

				setTimeout(function(){
					emoji.remove();
				}, 1000);
			});

		</script>
		<?php
	}
}

