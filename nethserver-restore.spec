Summary: Restore data from NethServer backup
Name: nethserver-restore-data
Version: 1.3.1
Release: 1%{?dist}
License: GPL
URL: %{url_prefix}/%{name}
Source0: %{name}-%{version}.tar.gz
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

%install
rm -rf %{buildroot}
(cd root; find . -depth -print | cpio -dump %{buildroot})
mkdir -p %{buildroot}/%{_nseventsdir}/%{name}-update
install -v -m 644 -D %{name}.json %{buildroot}/usr/share/cockpit/nethserver/applications/%{name}.json
install -v -m 644 -D ui/public/logo.png %{buildroot}/usr/share/cockpit/%{name}/logo.png
install -v -m 644 -D ui/public/manifest.json %{buildroot}/usr/share/cockpit/%{name}/manifest.json
install -v -m 755 -D api/read %{buildroot}/usr/libexec/nethserver/api/%{name}/read
%{genfilelist} %{buildroot} > %{name}-%{version}-filelist


%post

%preun

%files -f %{name}-%{version}-filelist
%defattr(-,root,root)
%dir %{_nseventsdir}/%{name}-update
%config %attr (0440,root,root) %{_sysconfdir}/sudoers.d/40_nethserver_restore_data
%doc COPYING


%changelog
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

