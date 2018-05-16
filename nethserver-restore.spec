Summary: Restore data from NethServer backup
Name: nethserver-restore-data
Version: 1.2.4
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
%{genfilelist} %{buildroot} > %{name}-%{version}-filelist


%post

%preun

%files -f %{name}-%{version}-filelist
%defattr(-,root,root)
%dir %{_nseventsdir}/%{name}-update
%config %attr (0440,root,root) %{_sysconfdir}/sudoers.d/40_nethserver_restore_data
%doc COPYING


%changelog
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

