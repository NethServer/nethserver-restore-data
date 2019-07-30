<template>
  <div>
    <h2>{{$t('dashboard.title')}}</h2>

    <h3 class="adjust-title">{{ $t('dashboard.configured_backups') }}</h3>

    <div v-if="!view.isLoaded" class="spinner spinner-lg"></div>
    <div v-if="view.isLoaded && backups.length > 0" class="row">
      <div class="row row-stat">
        <div class="row-inline-block">
          <div
            v-for="(b,bk) in backups"
            :key="bk"
            class="stats-container col-xs-12 col-sm-12 col-md-12 col-lg-12"
          >
            <span class="card-pf-utilization-card-details-count stats-count col-xs-12 col-sm-6">
              <b class="col-xs-6 col-sm-6">{{b.id}}</b>
              <span>
                <span class="col-sm-2">{{extractData(b.id).props.VFSType || '-' | uppercase}}</span>
                <span class="col-sm-1">|</span>
                <span class="col-sm-2">{{extractData(b.id).props.type || '-' | capitalize}}</span>
              </span>
            </span>
            <span class="card-pf-utilization-card-details-count stats-count col-xs-12 col-sm-6">
              <span class="col-xs-3 col-sm-3">{{$t('dashboard.last_run')}}:</span>
              <b class="semi-bold col-xs-6 col-sm-6">{{b['last-run'] | dateFormat}}</b>
              <span class="col-sm-3">
                <span :class="['fa', b.result == 'success' ? 'fa-check green' : 'fa-times red']"></span>
              </span>
            </span>
          </div>
        </div>
      </div>
    </div>
    <div v-if="view.isLoaded && backups.length == 0" class="blank-slate-pf">
      <div class="blank-slate-pf-icon">
        <span class="fa fa-database"></span>
      </div>
      <h1>{{$t('dashboard.no_backups_found')}}</h1>
      <p>{{$t('dashboard.no_backups_found_desc')}}.</p>
    </div>
  </div>
</template>

<script>
var moment = require("moment");

export default {
  name: "Dashboard",
  mounted() {
    this.getBackups();
  },
  data() {
    return {
      view: {
        isLoaded: false
      },
      backups: [],
      configuration: {}
    };
  },
  methods: {
    getBackups() {
      var context = this;

      context.view.isLoaded = false;
      nethserver.exec(
        ["system-backup/read"],
        {
          action: "backup-info"
        },
        null,
        function(success) {
          try {
            success = JSON.parse(success);
          } catch (e) {
            console.error(e);
          }
          context.backups = success.status["backup-data"];
          context.configuration = success.configuration["backup-data"].backups;
          context.view.isLoaded = true;
        },
        function(error) {
          console.error(error);
          context.view.isLoaded = true;
        }
      );
    },
    extractData(name) {
      var res = this.configuration.filter(function(b) {
        return b.name == name;
      })[0];

      return res;
    }
  }
};
</script>


<style>
.adjust-title {
  margin-bottom: -5px !important;
}

.semi-bold {
  font-weight: 600 !important;
}
</style>
