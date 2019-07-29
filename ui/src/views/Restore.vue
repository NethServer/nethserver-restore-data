<template>
  <div>
    <h2>{{$t('restore.title')}}</h2>
    <div v-if="!view.isLoaded" class="spinner spinner-lg"></div>
    <div v-if="view.isLoaded">
      <form
        class="form-horizontal"
        v-on:submit.prevent="(choosedString.length < 4 || choosedDate.length == 0 ) ? undefined : search()"
      >
        <div class="form-group">
          <label
            class="col-sm-2 control-label"
            for="textInput-modal-markup"
          >{{$t('restore.choose_backup')}}</label>
          <div class="col-sm-5">
            <select
              :disabled="!choosedBackup || view.isSearching || view.isRestoring"
              class="form-control"
              v-model="choosedBackup"
              @change="updateDate()"
            >
              <option
                v-for="(a, ak) in backups"
                :key="ak"
                :value="ak"
              >{{ak}} | {{a.destination | uppercase}} - {{a.engine}}</option>
            </select>
          </div>
        </div>
        <div class="form-group">
          <label
            class="col-sm-2 control-label"
            for="textInput-modal-markup"
          >{{$t('restore.choose_date')}}</label>
          <div class="col-sm-5">
            <select
              :disabled="!choosedDate || view.isSearching || view.isRestoring"
              class="form-control"
              v-model="choosedDate"
            >
              <option
                v-for="(d, dk) in backups[choosedBackup] && backups[choosedBackup].dates"
                :key="dk"
                :value="d"
              >{{d | dateFormat}}</option>
            </select>
          </div>
        </div>
        <div class="form-group">
          <label
            class="col-sm-2 control-label"
            for="textInput-modal-markup"
          >{{$t('restore.choose_mode')}}</label>
          <div class="col-sm-5">
            <select
              :disabled="!choosedMode || view.isSearching || view.isRestoring"
              class="form-control"
              v-model="choosedMode"
            >
              <option value="file">{{$t('restore.files_directories')}}</option>
              <option value="mail">{{$t('restore.mails')}}</option>
              <option value="advanced">{{$t('restore.advanced')}}</option>
            </select>
          </div>
        </div>
        <div class="form-group">
          <label
            class="col-sm-2 control-label"
            for="textInput-modal-markup"
          >{{$t('restore.'+choosedMode+'_text')}}</label>
          <div class="col-sm-5">
            <div class="search-pf-input-group">
              <label for="search1" class="sr-only">{{$t('restore.search')}}</label>
              <input
                :disabled="view.isSearching || view.isRestoring"
                type="search"
                class="form-control"
                :placeholder="$t('restore.'+choosedMode+'_placeholder')+'...'"
                v-model="choosedString"
              />
            </div>
          </div>
        </div>
        <div class="form-group">
          <label class="col-sm-2 control-label" for="textInput-modal-markup"></label>
          <div class="col-sm-1">
            <button
              :disabled="(choosedString.length < 4 || choosedDate.length == 0 ) || view.isSearching || view.isRestoring"
              class="btn btn-primary"
              type="submit"
            >{{$t('restore.search')}}</button>
          </div>
        </div>
      </form>

      <h3>{{$t('restore.selected')}}</h3>
      <pre v-show="!view.errorResults">{{selectedFiles.length > 0 ? selectedFiles.join('\n') : $t('restore.nothing_selected')}}</pre>
      <pre v-show="restoredFiles.length > 0">{{restoredFiles}}</pre>
      <form v-show="!view.errorResults" class="form-horizontal" v-on:submit.prevent="restore()">
        <div class="form-group">
          <label class="col-sm-2 control-label" for="textInput-modal-markup">
            {{$t('restore.override_restore')}}
            <doc-info
              :placement="'top'"
              :title="$t('restore.override_restore')"
              :chapter="'override_restore'"
              :inline="true"
            ></doc-info>
          </label>
          <div class="col-sm-1">
            <input class="form-control" type="checkbox" v-model="choosedOverride" />
          </div>
        </div>
        <div class="form-group">
          <label class="col-sm-2 control-label" for="textInput-modal-markup"></label>
          <div class="col-sm-2">
            <button :disabled="view.isRestoring" type="submit" class="btn btn-primary">
              <i18n path="restore.restore_files" tag="span">{{ selectedCount }}</i18n>
            </button>
          </div>
          <div class="col-sm-1">
            <div v-if="view.isRestoring" class="spinner"></div>
          </div>
        </div>
      </form>

      <h3>{{$t('restore.results')}}</h3>
      <div v-if="view.isSearching" class="spinner"></div>
      <div
        class="blank-slate-pf"
        v-if="!view.isSearching && treeData.length == 0 && !view.errorResults"
      >
        <div class="blank-slate-pf-icon">
          <span class="fa fa-list"></span>
        </div>
        <h1>{{$t('restore.no_results')}}</h1>
        <p>{{$t('restore.no_results_text')}}</p>
      </div>
      <div v-show="view.errorResults && !view.isSearching" class="alert alert-warning">
        <span class="pficon pficon-warning-triangle-o"></span>
        <strong class="mg-right">{{$t('restore.warning')}}!</strong>
        <i18n path="restore.too_many_results" tag="span">
          <b>{{ view.errorResultsCount | thousandsSeparator }}</b>
        </i18n>
      </div>
      <tree
        v-show="!view.isSearching && treeData.length > 0"
        ref="tree"
        :data="treeData"
        :options="treeOptions"
        @node:checked="selectCount"
        @node:unchecked="selectCount"
      />
    </div>
  </div>
</template>

<script>
var moment = require("moment");

export default {
  name: "Restore",
  mounted() {
    this.getBackups();
  },
  data() {
    return {
      view: {
        isLoaded: true,
        isSearching: false,
        isRestoring: false,
        errorResults: false,
        errorResultsCount: 0
      },
      backups: [],
      choosedBackup: null,
      choosedDate: null,
      choosedMode: "file",
      choosedString: "",
      choosedOverride: false,
      treeData: [],
      treeOptions: {
        checkbox: true,
        paddingLeft: 21
      },
      selectedCount: 0,
      selectedFiles: [],
      restoredFiles: ""
    };
  },
  methods: {
    updateDate() {
      this.choosedDate = this.backups[this.choosedBackup].dates[0];
    },
    getBackups() {
      var context = this;

      nethserver.exec(
        ["nethserver-restore-data/read"],
        {
          action: "list-backups"
        },
        null,
        function(success) {
          try {
            success = JSON.parse(success);
          } catch (e) {
            console.error(e);
          }
          context.backups = success.backups;
          context.choosedBackup = Object.keys(context.backups)[0];
          context.choosedDate = context.backups[context.choosedBackup].dates[0];
        },
        function(error) {
          console.error(error);
        }
      );
    },
    search() {
      var context = this;

      context.view.isSearching = true;
      context.restoredFiles = "";
      nethserver.exec(
        ["nethserver-restore-data/execute"],
        {
          action: "list-files",
          string: context.choosedString,
          backup: context.choosedBackup,
          mode: context.choosedMode,
          date: context.choosedDate
        },
        null,
        function(success) {
          context.view.isSearching = false;
          var results = JSON.parse(success);

          context.treeData = [];
          context.selectedCount = 0;
          context.selectedFiles = [];

          if (results.error) {
            context.$refs.tree.setModel(context.treeData);

            context.view.errorResults = true;
            context.view.errorResultsCount = results.results;
          } else {
            context.view.errorResults = false;

            if (Object.keys(results).length > 0) {
              context.treeData = [
                {
                  text: "/",
                  state: { expanded: true },
                  children: context.extractTree(results)[0].children
                }
              ];
              context.$refs.tree.setModel(context.treeData);
              context.selectedCount = context.$refs.tree.selected().length;
            }
          }
        },
        function(error) {
          console.error(error);
          context.view.isSearching = false;
          context.view.errorResults = true;

          context.treeData = [];
          context.selectedCount = 0;
          context.selectedFiles = [];
          context.$refs.tree.setModel(context.treeData);
        }
      );
    },
    extractTree(root) {
      var context = this;
      return Object.keys(root).map(function(i, index) {
        var original = i;
        var children = context.extractTree(Object.values(root)[index]);

        // get index of search pattern
        var ik = i.toLowerCase().indexOf(context.choosedString.toLowerCase());

        // insert highlight class inthe middle of the word
        if (ik >= 0 && children.length == 0) {
          i =
            i.substring(0, ik) +
            "<b class='highlight'>" +
            i.substring(ik, ik + context.choosedString.toLowerCase().length) +
            "</b>" +
            i.substring(ik + context.choosedString.toLowerCase().length);
        }

        // return tree with highlight match
        return {
          text: i,
          data: { original: original },
          state: {
            expanded: true,
            checked: false,
            selected: false
          },
          children: children
        };
      });
    },
    selectCount() {
      var nodes = this.$refs.tree.checked();

      var restorable = [];
      for (var n in nodes) {
        if (nodes[n].children && nodes[n].children.length == 0) {
          restorable.push(nodes[n]);
        }
      }

      function builder(node, arr) {
        arr.push(node.data.original);
        if (node.parent) {
          builder(node.parent, arr);
        }
        return arr;
      }

      // build the final path
      var files = restorable.map(function(r) {
        var arr = [];
        return builder(r, arr)
          .reverse()
          .join("/")
          .trim();
      });

      this.selectedCount = restorable.length;
      this.selectedFiles = files;
    },
    restore(files) {
      var context = this;

      // notification
      nethserver.notifications.success = context.$i18n.t(
        "restore.files_restored_ok"
      );
      nethserver.notifications.error = context.$i18n.t(
        "restore.files_restored_error"
      );

      // update values
      context.view.isRestoring = true;
      context.restoredFiles = "";
      nethserver.exec(
        ["nethserver-restore-data/execute"],
        {
          action: "restore-files",
          files: this.selectedFiles,
          backup: this.choosedBackup,
          date: this.choosedDate,
          override: this.choosedOverride
        },
        null,
        function(success) {
          context.view.isRestoring = false;

          context.restoredFiles = context.$t("restore.restored_list") + ":\n";
          var restored = JSON.parse(success);
          restored.files.map(function(r) {
            if (!context.choosedOverride) {
              context.restoredFiles += r.original + " -> " + r.restored + "\n";
            } else {
              context.restoredFiles +=
                r.original + " (" + context.$t("restore.overwritten") + ")\n";
            }
          });
        },
        function(error, data) {
          context.view.isRestoring = false;
          context.restoredFiles = context.$t("restore.error_restored_list");
          console.error(error, data);
        }
      );
    }
  }
};
</script>

<style>
.adjust-top-loader {
  margin-top: -4px;
}

.palette {
  color: white;
  padding: 5px;
  margin: 5px;
}

.mg-left {
  margin-left: 15px;
}
.mg-right {
  margin-right: 5px;
}

.green {
  color: #3f9c35;
}

.red {
  color: #cc0000;
}

.no-mg-bottom {
  margin-bottom: 0px;
}

.tree-checkbox {
  width: 20px !important;
  height: 20px !important;
}
.tree-arrow {
  height: 20px !important;
}
.tree-arrow.has-child {
  width: 25px !important;
}
.tree-checkbox.checked:after {
  left: 6px !important;
  top: 1px !important;
  height: 10px !important;
  width: 4px !important;
}
.tree-content {
  padding-top: 0px !important;
  padding-bottom: 0px !important;
}
.tree-anchor {
  padding: 0px 5px !important;
}

.highlight {
  background: #ec7a08 !important;
  padding: 2px !important;
  color: white !important;
  border-radius: 2px !important;
}

pre {
  margin: 1em 0px !important;
}
</style>
