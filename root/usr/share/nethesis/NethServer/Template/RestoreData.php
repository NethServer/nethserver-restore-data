<?php

$view->includeFile('NethServer/Css/proton/style.css');
$view->includeFile('NethServer/Css/nethserver-restore.css');

$view->includeFile('NethServer/Js/jstree.min.js');
$view->includeFile('NethServer/Js/nethserver-restore.js');

$modulePath = $view->getSiteUrl();
$page = '<div id="wrap">
		    <div id="nav">
		      <input class="TextInput" type="text" id="jstree_search" value="" placeholder="'.$T('RestoreData_PlaceHolder').'">
		      <div class="Button submit" id="restoreButton">'.$T('RestoreData_Button').'</div>
		      <img id="loader" src="'.$modulePath.'/css/img/throbber.gif">
		      <p id="pathRestore"></p>
		      <div id="modeRestore">
		      	<div><input id="originalRadio" class="restoreInput" type="radio" name="destination" value="original" checked/>'.$T('RestoreData_original').'</div>
		      	<div><input id="tempRadio" class="restoreInput" type="radio" name="destination" value="temp"/>'.$T('RestoreData_temp').'</div>
		      </div></div>
		      '.$view->buttonList($view::BUTTON_HELP).'
		    <div id="sidebar">
		      <p class="par-string" >'.$T('RestoreData_String_restore').'</p>
		      <div id="jstree" role="main">
		      </div>
		    </div>

		    <div id="main">
		      <p class="description-right">'.$T('RestoreData_Folder_Label').'</p>

		      <div id="files_container">
		        <ul id="files"></ul>
		      </div>
		    </div>

		 </div>';

echo $view->header()->setAttribute('template', $T('RestoreData_Title'));
echo $page;
