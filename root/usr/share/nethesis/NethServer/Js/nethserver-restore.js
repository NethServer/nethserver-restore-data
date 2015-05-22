$(function () {
  var to = false;
  var destPath = "/tmp/restored";
  var url = window.location.href;

  $.ajax(url+'?base').done(function(data) {
      $('#jstree')
      .jstree({
        "core" : {
          "data" : {
              "url" : function (node) {
                  return node.id === '#' ? 
                  url+'?base' : 
                  url+'?start='+node.id;
              },
              "data" : function (node) {
                  return { 'id' : node.text };
              }
          },
          'themes': {
            'name': 'proton',
            'responsive': true
          },
          "check_callback" : true
        },
        "plugins" : [ 'search' ],
      });
  });

  $('#jstree')
    .on('changed.jstree', function (e, data) {
      if(data && data.selected && data.selected.length) {
        $('#files').empty();
        var path = $('#jstree').jstree(true).get_path(data.selected[0]);

        if(path.length > 1)
          $.get(url+'?start=' + path.join('/'), function (d) {
            for (var key in d.children) {
              var file = d.children[key];
              $('#files').append("<li>"+file.text+"</li>");
            }
          });
      }
    })
    .bind("select_node.jstree", function (e, data) {
      if(!data.event.ctrlKey)
        return data.instance.toggle_node(data.node);
    });

  $('#jstree_search').keyup(function () {
    if(to) { clearTimeout(to); }

    to = setTimeout(function () {
      var v = $('#jstree_search').val();
      $('#jstree').jstree(true).search(v);
    }, 250);

  });

  $("#restoreButton").click(function() {
    var n = $("#jstree").jstree("get_selected", true);
    var selectedNode = $('#jstree').jstree(true).get_selected();
    $('#pathRestore').html("");

    if(selectedNode.length == 0)
      $('#pathRestore').html("Select a folder to restore");
    else {

       var timeStamp = Date.now().toString();
       
       for(var node in selectedNode) {
        var path = $('#jstree').jstree(true).get_path(selectedNode[node]);
        if(path) {
          var finalPath = path.join("/");
          if(finalPath.charAt(0) == "/") {
            if(finalPath.length !== 1) {
              finalPath = finalPath.substring(1);
            }
          }

          var temp = $('#tempRadio').is(':checked');
          var posOrig = "/";

          if(temp) {
            var destPathTimed = destPath+"_"+timeStamp;
            var posOrig = destPathTimed;
          }

          console.log(posOrig);

          $('#loader').show();
          $.get(url+'?position='+posOrig+'&file='+finalPath)
          .done(function(d) {
            if(d == 0) {
              $('#loader').hide();
              $('#pathRestore').html("Restored in <p class='codeIn'>"+posOrig+"</p>");
            }
          });
        }
      }
    }
  });
});