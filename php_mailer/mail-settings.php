<?php
require_once "../../inc/common.php";
require_once "../../inc/header.php";

if (!current_user_can(PERMISSION_MANAGE_PLUGINS))
{
    echo "<h4>Access denied</h4>";
    require_once "../../inc/footer.php";
    die();
}

?>


<h4>Mail Settings</h4>

<form method="post" action="mail-settings.php?id=<?php echo $edit_user->id; ?>" autocomplete="off" enctype="multipart/form-data">


<br>
<button type="submit" name="update_user" class="btn btn-primary">Save Changes</button><br>
</form>


<?php
require_once "../../inc/footer.php";
