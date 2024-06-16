<?php

require_once "../../inc/common.php";
require_once "../../inc/header.php";
require_once "../../inc/connection.php";

global $rpc;
if (!$rpc)
{
    Message::Fail("No RPC");
    require_once "../../inc/footer.php";
    return;
}
$unmuted = [];
$e_unmuted = [];
if (isset($_GET['unmute']))
{
    if ($_GET['unmute'] == "*")
    {
        $users = $rpc->query("mute.list")->list;
        foreach($users as $user)
        {
            if ($rpc->query("mute.remove", ["nick" => $user->name]))
                $unmuted[] = $user->name;
            else
                $e_unmuted[] = $user->name;
        }
    }
    else
    {
        if ($rpc->query("mute.remove", ["nick" => $_GET['unmute']]))
            $unmuted[] = $_GET['unmute'];
        else
            $e_unmuted[] = $_GET['unmute'];
    }
    if (!empty($unmuted))
        Message::Success("Unmuted ".count($unmuted)." users");
    if (!empty($e_unmuted))
        Message::Fail("Could not unmute ".count($e_unmuted)." users");
}
function get_mute_version() : float|NULL
{
    global $rpc;
    if (!$rpc)
        return NULL;
    $mods = $rpc->server()->module_list()->list;
    foreach ($mods as $m)
    {
        if ($m->name != "third/mute2" && $m->name != "third/mute")
            continue;
        
        return (float)$m->version;
    }
    return NULL;
}

$users = $rpc->query("mute.list");
if (get_mute_version() < 1.4)
    Message::Fail("Need to be using module third/mute version 1.4 or later for this plugin to work");
?>
<h2>Muted Users</h2>
A list of muted users (third/mute)
<form>
    <table class="mt-4 ml-3 table-sm table-primary table-striped">
        <thead>
            <th><button name='unmute' value='*' type='submit' class='mr-2 btn-sm btn-danger'>Clear All</button></th>
            <th>Nick</th>
            <th>Host/IP</th>
            <th>Security Groups</th>
        </thead>
        <tbody>
            <?php
                foreach($users->list as $user)
                {
                    $securitygroups = "";
                    foreach ($user->user->{"security-groups"} as $sg)
                    {
                        $securitygroups .= "<span class='mr-1 badge rounded-pill text-white bg-primary'>$sg</span>";
                    }
                    echo "<tr>
                        <td><button name='unmute' value='$user->id' type='submit' class='mr-2 btn-sm btn-danger'><b>Remove</b></button></td>
                        <td><a href=\"".get_config("base_url")."users/details.php?nick=".$user->id."\"><b>".$user->name."</b></a></td>
                        <td><a href=\"".get_config("base_url")."tools/ip-whois.php?ip=".$user->ip."\">".$user->ip."</a></td>
                        <td>$securitygroups</td>
                        </tr>";
                }
            ?>
        </tbody>
    </table>
</form>
<?php

require_once "../../inc/footer.php";
