<!--
#
# Copyright (C) 2019 Nethesis S.r.l.
# http://www.nethesis.it - nethserver@nethesis.it
#
# This script is part of NethServer.
#
# NethServer is free software: you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation, either version 3 of the License,
# or any later version.
#
# NethServer is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
#
# You should have received a copy of the GNU General Public License
# along with NethServer.  If not, see COPYING.
#
-->

<template>
  <div id="app">
    <nav
      id="navbar-left"
      class="nav-pf-vertical nav-pf-vertical-with-sub-menus nav-pf-persistent-secondary panel-group"
    >
      <ul class="list-group panel">
        <li
          id="dashboard-item"
          v-bind:class="[getCurrentPath('dashboard') ? 'active' : '', 'list-group-item']"
        >
          <a href="#/dashboard">
            <span class="fa fa-cube"></span>
            <span class="list-group-item-value">{{$t('dashboard.app')}}</span>
          </a>
        </li>
        <li class="li-empty"></li>
        <li
          id="restore-item"
          v-bind:class="[getCurrentPath('restore') ? 'active' : '', 'list-group-item']"
        >
          <a href="#/restore">
            <span class="fa fa-refresh"></span>
            <span class="list-group-item-value">{{$t('restore.title')}}</span>
          </a>
        </li>
        <li class="li-empty"></li>
        <li
          id="about-item"
          v-bind:class="[getCurrentPath('about') ? 'active' : '', 'list-group-item']"
        >
          <a href="#/about">
            <span class="fa fa-info"></span>
            <span class="list-group-item-value">{{$t('about.title')}}</span>
          </a>
        </li>
      </ul>
    </nav>
    <div class="container-fluid container-cards-pf">
      <router-view />
    </div>
  </div>
</template>

<script>
export default {
  name: "App",
  watch: {
    $route: function(val) {
      localStorage.setItem("restore-data-path", val.path);
    }
  },
  mounted() {
    var path = localStorage.getItem("restore-data-path") || "/";
    this.$router.push(path);
  },
  methods: {
    getCurrentPath(route, offset) {
      if (offset) {
        return this.$route.path.split("/")[offset] === route;
      } else {
        return this.$route.path.split("/")[1] === route;
      }
    }
  }
};
</script>

<style>
.right {
  float: right;
}
.divider {
  border-bottom: 1px solid #d1d1d1;
}

.mg-left-5 {
  margin-left: 5px !important;
}
.mg-left-20 {
  margin-left: 20px !important;
}

.stats-container {
  padding-bottom: 0px !important;
  padding-left: 20px !important;
  padding-right: 20px !important;
  padding-top: 20px !important;
  border-width: initial !important;
  border-style: none !important;
  border-color: initial !important;
  -o-border-image: initial !important;
  border-image: initial !important;
}

.stats-text {
  margin-top: 10px !important;
  display: block;
}

.stats-description {
  float: left;
  line-height: 1;
}

.stats-count {
  font-size: 18px;
  font-weight: 300;
  float: left;
  line-height: 1;
}

@media only screen and (max-width: 768px) {
  .stats-count {
    margin-top: 10px;
  }
}

.row-stat {
  margin-left: 0px;
  margin-right: 0px;
}

.compact {
  margin-bottom: 0px !important;
}

.row-inline-block {
  display: inline-block;
  width: 100%;
}

.search-pf {
  width: 50%;
}

.list-view-pf .list-group-item:first-child {
  border-top: 1px solid transparent;
}

.list-group.list-view-pf {
  border-top: 0px;
}

.list-view-pf .list-group-item {
  border-top: 1px solid #ededec;
}

.span-right-margin {
  margin-right: 4px;
}

.span-left-margin {
  margin-left: 5px;
}

.margin-left-md {
  margin-left: 10px !important;
}

.floatleft {
  float: left;
}

.small-list {
  padding-top: 5px;
  padding-bottom: 5px;
}

.small-li {
  padding-top: 3px !important;
  padding-bottom: 3px !important;
}

.multi-line {
  display: unset;
  text-align: unset;
}

.adjust-line {
  line-height: 26px;
}

.no-mg-top {
  margin-top: 0px !important;
}
.no-mg-bottom {
  margin-bottom: 0px !important;
}

.mg-top-10 {
  margin-top: 10px !important;
}

.green {
  color: #3f9c35;
}

.red {
  color: #cc0000;
}

.gray {
  color: #72767b !important;
}

.blue {
  color: #0088ce !important;
}
</style>
