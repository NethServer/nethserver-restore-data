Summary: Restore data from NethServer backup
Name: nethserver-restore-data
Version: 2.0.7
Release: 1%{?dist}
License: GPL
URL: %{url_prefix}/%{name}
Source0: %{name}-%{version}.tar.gz
Source1: %{name}.tar.gz
BuildArch: noarch

Requires: nethserver-base, nethserver-backup-data
Requires: duc

BuildRequires: perl
BuildRequires: nethserver-devtools

%description
Simply web interface for restore data from backup

%prep
%setup

%build
%{makedocs}
perl createlinks
sed -i 's/_RELEASE_/%{version}/' %{name}.json

%install
rm -rf %{buildroot}
(cd root; find . -depth -print | cpio -dump %{buildroot})

mkdir -p %{buildroot}/usr/share/cockpit/%{name}/
mkdir -p %{buildroot}/usr/share/cockpit/nethserver/applications/
mkdir -p %{buildroot}/usr/libexec/nethserver/api/%{name}/
tar xvf %{SOURCE1} -C %{buildroot}/usr/share/cockpit/%{name}/
cp -a %{name}.json %{buildroot}/usr/share/cockpit/nethserver/applications/
cp -a api/* %{buildroot}/usr/libexec/nethserver/api/%{name}/

%{genfilelist} %{buildroot} --file /etc/sudoers.d/50_nsapi_nethserver_restore_data 'attr(0440,root,root)' > %{name}-%{version}-filelist


%post
/usr/bin/rm -rf /var/cache/restore/ || exit 0 # cleanup older duc index files

%preun

%files -f %{name}-%{version}-filelist
%defattr(-,root,root)
%config %attr (0440,root,root) %{_sysconfdir}/sudoers.d/40_nethserver_restore_data
%doc COPYING


%changelog
* Wed Sep 08 2021 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 2.0.7-1
- Restore data: unable to restore file if the path contains a single quote - Bug NethServer/dev#6552

* Wed Jan 15 2020 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 2.0.6-1
- Cockpit: restore-data does not work for old backups - Bug Nethserver/dev#6022

* Wed Jan 08 2020 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 2.0.5-1
- Cockpit: change package Dashboard page title - NethServer/dev#6004

* Thu Dec 05 2019 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 2.0.4-1
- Restore of a whole directory - Bug Nethserver/dev#5975

* Thu Nov 21 2019 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 2.0.3-1
- Restore filenames with special characters - Bug NethServer/dev#5913

* Tue Nov 12 2019 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 2.0.2-1
- Restore-data: module does not warn if the backup-data module is disabled. - Bug Nethserver/dev#5896

* Mon Oct 28 2019 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 2.0.1-1
- Email noise from backup - Bug Nethserver/dev#5875

* Tue Oct 01 2019 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 2.0.0-1
- New NethServer 7.7.1908 defaults - NethServer/dev#5831
- Sudoers based authorizations for Cockpit UI - NethServer/dev#5805

* Tue Sep 03 2019 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 1.4.2-1
- Cockpit. List correct application version - Nethserver/dev#5819

* Fri Aug 30 2019 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 1.4.1-1
- Backup-clean-files: integer expression expected - Bug Nethserver/dev#5814

* Mon Aug 26 2019 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 1.4.0-1
- Backup data restore Cockpit UI - NethServer/dev#5796

* Tue Jul 09 2019 Davide Principi <davide.principi@nethesis.it> - 1.3.1-1
- Cockpit legacy apps implementation - NethServer/dev#5782

* Wed Jan 30 2019 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 1.3.0-1
- Remove single backup data - NethServer/dev#5691

* Wed Jan 09 2019 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 1.2.6-1
- restore-data interface shows failed backups reference - Bug NethServer/dev#5685

* Thu Sep 06 2018 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 1.2.5-1
- Backup-data: multiple schedule and backends - NethServer/dev#5538

* Wed May 16 2018 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 1.2.4-1
- Restore data: can't restore files from Web UI - Bug NethServer/dev#5494

* Wed Jan 25 2017 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 1.2.3-1
- Rename module "Restore files"
- Minor UI tweaks

* Thu Oct 06 2016 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 1.2.1-1
- Bad event for expand-template in restore data - Bug NethServer/dev#5118

* Mon Sep 26 2016 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 1.2.0-1
- restore-data interface: select from which backup file restore - NethServer/dev#5108

* Thu Jul 21 2016 Stefano Fancello <stefano.fancello@nethesis.it> - 1.1.1-1
- Web UI: missing labels - Bug NethServer/dev#5061

* Thu Jul 07 2016 Stefano Fancello <stefano.fancello@nethesis.it> - 1.1.0-1
- First NS7 release

* Mon Jul 06 2015 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 1.0.0-1
- Backup data: web interface for restore - Enhancement #2773 [NethServer]

* Thu May 21 2015 Edoardo Spadoni <edoardo.spadoni@nethesis.it> - 1.0.0
- first release

