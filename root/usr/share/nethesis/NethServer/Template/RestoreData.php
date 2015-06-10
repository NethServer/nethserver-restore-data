<?php

$view->includeFile('NethServer/Css/proton/style.css');
$view->includeFile('NethServer/Css/nethserver-restore.css');

$view->includeFile('NethServer/Js/jstree.min.js');
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
  var url = window.location.href;

  $.ajax(url+"?base").done(function(data) {
      $("#jstree")
      .jstree({
        "core" : {
          "data" : {
              "url" : function (node) {
                  return node.id === "#" ? 
                  url+"?base" : 
                  url+"?start="+node.id;
              },
              "data" : function (node) {
                  return { "id" : node.text };
              }
          },
          "themes": {
            "name": "proton",
            "responsive": true
          },
          "check_callback" : true
        },
        "plugins" : [ "search" ],
      });
  });

  $("#jstree")
    .on("changed.jstree", function (e, data) {
      if(data && data.selected && data.selected.length) {
        $("#files").empty();
        var path = $("#jstree").jstree(true).get_path(data.selected[0]);

        if(path.length > 1)
          $.get(url+"?start=" + path.join("/"), function (d) {
            for (var key in d.children) {
              var file = d.children[key];
              $("#files").append("<li>"+file.text+"</li>");
            }
          });
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

  $("#restoreButton").click(function() {
    var n = $("#jstree").jstree("get_selected", true);
    var selectedNode = $("#jstree").jstree(true).get_selected();
    $("#pathRestore").html("");

    if(selectedNode.length == 0) {
      $(".par-string").css("color", "#D84A38");
      $(".par-string").css("font-weight", "bold");
      $(".par-string").css("margin-left", "-2px");
    } else {
      $(".par-string").removeAttr("style");
      var timeStamp = new Date().yyyymmdd();
       
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

          if(temp) {
            var destPathTimed = finalPath+"/restored_"+timeStamp;
            var posOrig = destPathTimed;
          }

          $("#loader").show();
          $.get(url+"?position="+posOrig+"&file="+finalPath)
          .done(function(d) {
            if(d == 0) {
              $("#loader").hide();
              var message = "'.$T('RestoreData_restore_message').' <p class=\'codeIn\'>"+posOrig+"</p>";
              if(selectedNode.length > 1) {
                message = "'.$T('RestoreData_restore_message').' <p class=\'codeIn\'>DIRECTORIES_PATH/restored_"+timeStamp+"</p>"
              }
              $("#pathRestore").html(message);
            }
          });
        }
      }
    }
  });
});
');

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
