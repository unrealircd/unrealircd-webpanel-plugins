<?php
require_once "../../inc/common.php";

echo updateWithGitStashAndPull();
echo "<br><br>";
echo updateWithComposer();

function updateWithGitStashAndPull()
{

    $result = "";

    exec('git stash && git pull --force 2>&1', $output, $return_var);
    foreach ($output as $line) {
        $result .= $line . "<br>";
    }
    $result .= "Code of return : " . $return_var;

    return $result;
}

function updateWithComposer()
{
    $directory = UPATH . '/plugins/update_checker';
    // Changer le répertoire de travail vers celui contenant le fichier composer.json
    chdir($directory);

    $output = shell_exec('composer install');

    return "<pre>$output</pre>";
}
