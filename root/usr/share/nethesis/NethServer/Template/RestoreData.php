<?php

$view->includeFile('NethServer/Css/proton/style.css');
$view->includeFile('NethServer/Css/nethserver-restore.css');

$view->includeFile('NethServer/Js/jstree.min.js');

$resultTarget = $view->getClientEventTarget('result');
$startTarget = $view->getClientEventTarget('start');
$pathTarget = $view->getClientEventTarget('path');
$posTarget = $view->getClientEventTarget('position');

$url = $view->getModuleUrl();

$view->includeJavascript('
$(function () {

  Date.prototype.yyyymmdd = function() {
   var yyyy = this.getFullYear().toString();
   var mm = (this.getMonth()+1).toString();
   var dd  = this.getDate().toString();
   return yyyy + (mm[1]?mm:"0"+mm[0]) + (dd[1]?dd:"0"+dd[0]);
  };

  var to = false;
  var destPath = "/tmp/restored";
  var url = "'.$url.'";

  $(".' . $resultTarget . '").on("nethguiupdateview", function(e, viewvalue) {
    $(this).jstree({
         "core" : {
           "data" : viewvalue,
           "themes": {
             "name": "proton",
             "responsive": true
           },
           "check_callback" : true
        },
         "plugins" : [ "search" ],
       });
  });
  
  $(".' . $startTarget . '").on("nethguiupdateview", function(e, d) {
     for (var key in d.children) {
       var file = d.children[key];
       $("#files").append("<li>"+file.text+"</li>");
     }
  });

  $.Nethgui.Server.ajaxMessage({"url": url + ".json?base"});

  $("#jstree")
    .on("changed.jstree", function (e, data) {
      $("#pathList").html("");
      if(data && data.selected && data.selected.length) {
        $("#files").empty();
        var selectedNode = $("#jstree").jstree(true).get_selected();
        var destinationArray = [];

        for(var node in selectedNode) {
          var path = $("#jstree").jstree(true).get_path(selectedNode[node]);
          if(path) {
            var finalPath = path.join("/");
            if(finalPath.charAt(0) == "/") {
              if(finalPath.length !== 1) {
                finalPath = finalPath.substring(1);
              }
            }

            destinationArray.push(finalPath);
          }
        }
        for(it in destinationArray) {
          $("#pathList").append("<p>"+destinationArray[it]+"</p>");
        }
        // $("#pathList").append("<li/>");
        // $("#pathList").append(destinationArray.join("<li/>"));
        // $("#pathList").append("<li/>");

        if(path.length > 1)
          $.Nethgui.Server.ajaxMessage({"url": url + ".json?start=" + path.join("/")});
      }
    })
    .bind("select_node.jstree", function (e, data) {
      if(!data.event.ctrlKey)
        return data.instance.toggle_node(data.node);
    });

  $("#jstree_search").keyup(function () {
    if(to) { clearTimeout(to); }

    to = setTimeout(function () {
      var v = $("#jstree_search").val();
      $("#jstree").jstree(true).search(v);
    }, 250);

  });

  $(".Button.submit").click(function() {
    var n = $("#jstree").jstree("get_selected", true);
    var selectedNode = $("#jstree").jstree(true).get_selected();

    if(selectedNode.length == 0) {
      $(".par-string").css("color", "#D84A38");
      $(".par-string").css("font-weight", "bold");
      $(".par-string").css("margin-left", "-2px");
    } else {
      $(".par-string").removeAttr("style");

      var destinationArray = [];

      for(var node in selectedNode) {
        var path = $("#jstree").jstree(true).get_path(selectedNode[node]);
        if(path) {
          var finalPath = path.join("/");
          if(finalPath.charAt(0) == "/") {
            if(finalPath.length !== 1) {
              finalPath = finalPath.substring(1);
            }
          }

          var temp = $("#tempRadio").is(":checked");
          var posOrig = "/";
          var mess = posOrig;

          if(temp) {
            var posOrig = "tmp";
          }

          destinationArray.push(finalPath);
        }
      }
      $(".' . $pathTarget. '").val(destinationArray.join(" "));
      $(".' . $posTarget. '").val(posOrig);
    }
  });
});
');

$modulePath = $view->getSiteUrl();
$page = '<div id="wrap">
        '.$view->fieldset()->setAttribute('template', $T('RestoreData_file_restore')).'
		    <div id="nav">
		      <div class="codeIn" id="pathList">
          <p>'.$T('RestoreData_empty_restore').'</p>
          </div>
          '.$view->fieldset()->setAttribute('template', $T('RestoreData_mode_restore')).'
		      <div id="modeRestore">
		      	<div><input id="originalRadio" class="restoreInput" type="radio" name="destination" value="original" checked/>'.$T('RestoreData_original').'</div>
		      	<div><input id="tempRadio" class="restoreInput" type="radio" name="destination" value="temp"/>'.$T('RestoreData_temp').'</div>
		      </div>
          '.$view->button('RestoreData', $view::BUTTON_SUBMIT).'
        </div>
		      <div class="helpContainer">'.$view->buttonList($view::BUTTON_HELP).'</div>
		    <div id="sidebar">
          <p class="par-string" >'.$T('RestoreData_String_restore').'</p>
          <input class="TextInput" type="text" id="jstree_search" value="" placeholder="'.$T('RestoreData_PlaceHolder').'">
		      <div id="jstree" class="'. $resultTarget .'" role="main">
		      </div>
		    </div>

		    <div id="main">
		      <p class="description-right">'.$T('RestoreData_Folder_Label').'</p>

		      <div id="files_container">
		        <ul id="files" class="'. $startTarget .'"></ul>
		      </div>
		    </div>

		 </div>';

echo $view->header()->setAttribute('template', $T('RestoreData_Title'));
echo $view->hidden('path');
echo $view->hidden('position');
echo $page;
