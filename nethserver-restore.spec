Summary: Restore data from NethServer backup
Name: nethserver-restore-data
Version: 0.0.1
Release: 1%{?dist}
License: GPL
URL: %{url_prefix}/%{name} 
Source0: %{name}-%{version}.tar.gz
BuildArch: noarch

Requires: nethserver-base, nethserver-backup-data

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
* Thu May 21 2015 Edoardo Spadoni <edoardo.spadoni@nethesis.it> - 1.0.0
- first release

