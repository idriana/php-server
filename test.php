<?php
session_start();
if (isset($_SESSION['test']))
{
    $_SESSION['test'] = $_SESSION['test'] + 1;
}
else
{
    $_SESSION['test'] = 1;
}
echo 'Session value = '.$_SESSION['test'].'<br /><a href="">Refresh</a>';
