Summary: Restore data from NethServer backup
Name: nethserver-restore-data
Version: 1.0.0
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
rm -rf $RPM_BUILD_ROOT
(cd root; find . -depth -print | cpio -dump $RPM_BUILD_ROOT)
%{genfilelist} $RPM_BUILD_ROOT > %{name}-%{version}-filelist
echo "%doc COPYING" >> %{name}-%{version}-filelist

%post

%preun

%files -f %{name}-%{version}-filelist
%defattr(-,root,root)

%changelog
* Mon Jul 06 2015 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 1.0.0-1
- Backup data: web interface for restore - Enhancement #2773 [NethServer]

* Thu May 21 2015 Edoardo Spadoni <edoardo.spadoni@nethesis.it> - 1.0.0
- first release

