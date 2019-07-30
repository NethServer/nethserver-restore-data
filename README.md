# nethserver-restore-data

> This module provides an easily UI to search and restore files from backups

## Cockpit API

### read

#### action: `list-backups`
##### input
```bash
echo '{"action":"list-backups"}' | /usr/libexec/nethserver/api/nethserver-restore-data/read
```
##### output
```json
{
  "backups": {
    "test": {
      "engine": "rsync",
      "dates": [
        1564135604,
        1564189501,
        1564275901,
        1564362301,
        1564385733,
        1564386803,
        1564448701
      ],
      "destination": "nfs"
    },
    "qwerty": {
      "engine": "duplicity",
      "dates": [
        1564136512,
        1564189501,
        1564275901,
        1564362301,
        1564448701
      ],
      "destination": "nfs"
    },
    ...
  }
}

```

#### action: `list-logs`
##### input
```bash
echo '{"action":"list-logs"}' | /usr/libexec/nethserver/api/nethserver-restore-data/read
```
##### output
```json
{
  "logs": [
    "/var/log/backup/backup-qwerty-201907250105.log",
    "/var/log/backup/backup-qwerty-201907260105.log",
    "/var/log/backup/backup-test-201907250105.log",
    "/var/log/backup/backup-test-201907260105.log",
    ...
  ]
}
```

### execute
#### action: `list-files`
##### input
```bash
echo '{"action":"list-files", "string":"<your_search_string>", "backup":"<your_backup_name>", "mode":"<file|mail|advanced>", "date":1564137036}' | /usr/libexec/nethserver/api/nethserver-restore-data/execute
```
##### output (tree-view of file list)
```json
{
  "": {
    "var": {
      "lib": {
        "nethserver": {
          "backup": {
            "restore": {
              "test-1564135604.gz": {}
            }
          }
        },
        "collectd": {
          "rrd": {
            "test.nethesis.it": {
              "df-mnt-backup-test": {
                "df_complex-used.rrd": {},
                "df_complex-reserved.rrd": {},
                "df_complex-free.rrd": {}
              },
              "df-mnt-backup-restore-data-test": {
                "df_complex-used.rrd": {},
                "df_complex-reserved.rrd": {},
                "df_complex-free.rrd": {}
              }
            }
          }
        }
      }
    }
  }
}
```

#### axtion: `restore-files`
##### input
```bash
echo '{
    "action":"restore-files",
    "files":[
        "/var/lib/collectd/rrd/test.nethesis.it/df-mnt-backup-test/df_complex-used.rrd",
        "/var/lib/collectd/rrd/test.nethesis.it/df-mnt-backup-test/df_complex-reserved.rrd",
        "/var/lib/collectd/rrd/test.nethesis.it/df-mnt-backup-test/df_complex-free.rrd"
    ],
    "backup":"test",
    "date":1564137036,
    "override": <false|true> -> define if restored files will be overwritten or no
}' | /usr/libexec/nethserver/api/nethserver-restore-data/execute | jq
```

##### output
```json
With "override": "false"
{
  "files": [
    {
      "restored": "/var/lib/collectd/rrd/ns76.edo.nethesis.it/df-mnt-backup-test/restore-20190730-080333-df_complex-used.rrd",
      "original": "/var/lib/collectd/rrd/ns76.edo.nethesis.it/df-mnt-backup-test/df_complex-used.rrd"
    },
    {
      "restored": "/var/lib/collectd/rrd/ns76.edo.nethesis.it/df-mnt-backup-test/restore-20190730-080336-df_complex-reserved.rrd",
      "original": "/var/lib/collectd/rrd/ns76.edo.nethesis.it/df-mnt-backup-test/df_complex-reserved.rrd"
    },
    {
      "restored": "/var/lib/collectd/rrd/ns76.edo.nethesis.it/df-mnt-backup-test/restore-20190730-080340-df_complex-free.rrd",
      "original": "/var/lib/collectd/rrd/ns76.edo.nethesis.it/df-mnt-backup-test/df_complex-free.rrd"
    }
  ]
}
```