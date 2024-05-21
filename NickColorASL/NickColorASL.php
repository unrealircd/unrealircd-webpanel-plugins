<?php

class NickColorASL
{
    public $name = "NickColorASL";
    public $author = "Madrix";
    public $version = "1.0";
    public $description = "Allows displaying the list of usernames with pink for girls, blue for boys, and black for others. It also displays the real name in the link's title attribute";
    public $email = "";

    function __construct()
    {
        Hook::func(HOOKTYPE_PRE_FOOTER, 'NickColorASL::pre_footer');
    }

    public static function pre_footer(&$pages)
    {

        $css = '<style>
        #data_list .boy {
            color: #0667ff;
        }
        #data_list .girl {
            color: #ff00ef;
        }
        </style>';;

?>
        <script>
            function Timer() {
                if (window.location.pathname.endsWith('users/') || window.location.pathname.endsWith('users/index.php')) {
                    //const table = document.getElementById('data_list');

                    <?php
                    require_once(UPATH . "/inc/connection.php");
                    global $rpc;
                    $users = $rpc->user()->getAll();

                    $code = "";
                    foreach ($users as $user) {
                        $user_name = $user->name;
                        // Check girls [FG]
                        // F = Femme (french)
                        // F = Fille (french)
                        // F = Female (english)
                        // G = Girl (english)
                        if (preg_match("/^[0-9]{2}[\/\s][FG]/i", $user->user->realname)) {
                            $code .= 'var links = document.querySelectorAll("#data_list tbody tr td.sorting_1 a");
                            links.forEach(function(link) {
                                if (link.textContent === "' . $user_name . '") {
                                    link.classList.add("girl");
                                    link.setAttribute("title", "' . $user->user->realname . '");
                                }
                            });';

                        // Check boys [HBM]
                        // H = Homme (french)
                        // M = Mec (french)
                        // B = Boy (english)
                        // M = Male (english)
                        } else if (preg_match("/^[0-9]{2}[\/\s][HBM]/i", $user->user->realname)) {
                            $code .= 'var links = document.querySelectorAll("#data_list tbody tr td.sorting_1 a");
                            links.forEach(function(link) {
                                if (link.textContent === "' . $user_name . '") {
                                    link.classList.add("boy");
                                    link.setAttribute("title", "' . $user->user->realname . '");
                                }
                            });';
                        } else {
                            $code .= 'var links = document.querySelectorAll("#data_list tbody tr td.sorting_1 a");
                            links.forEach(function(link) {
                                if (link.textContent === "' . $user_name . '") {
                                    link.setAttribute("title", "' . $user->user->realname . '");
                                }
                            });';  
                        }
                    }

                    echo $code;

                    ?>
                }
            }

            // UX issue: it checks every 1 second because otherwise it won't refresh when selecting by country.
           setInterval(() => {
                if (args.ajax) {
                    Timer()
                }
            }, 2000);
        </script>

        <?= $css; ?>
<?php
    }
}
?>