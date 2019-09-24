<?php 

echo '<div class="unsupported-restore"><span class="fa fa-warning mg-right"></span>'.$T('restore_data_unsupported');
if ($view['cockpitUrl']) {
    echo $T('cockpit_available')." <a href='{$view['cockpitUrl']}'>{$view['cockpitUrl']}</a>";
} else {
    echo $T('install_cockpit')." <a href='{$view['scUrl']}'>".$T('PackageManager_Title')."</a>.";
} 
echo '</div>';

$view->includeCss("
    .mg-right {
        margin-right: 10px;
    }
    .unsupported-restore {
        padding: 20px;
        background-color: #FFB600;
        border-color: #FFB600;
    }
");
