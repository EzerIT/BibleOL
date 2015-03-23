Summary:	Free Hebrew scalable fonts
Name:		culmus-fonts
Version:	0.130
Release:	1
Vendor:		Culmus Project

# This is intended to solve the upgrading problems introduced by 
# the version number "culmus-0.71" : since 71>8, this package
# would have had a lower version number
#
# To solve this I will use the epoch header. It is a slight abuse,
# and therefore I keep the epoch as 1, in case anybody needs
# the epoch for other uses (e.g:incorporating culmus as a part of a 
# larger package.
#
# Note that the version number will now be epoch:version-release, 
# instead of simply version-release

# Aug-15-2003 Maxim Iorsh:
# By request from RH, release numbers will be 90, 100, 110, etc.
# Minor releases could be added between them: 91, 92...
Epoch:		1

%define     type1_fonts_dir  %{_datadir}/fonts/he/Type1
%define     ttf_fonts_dir  %{_datadir}/fonts/he/TrueType
%define     doc_dir  %{_datadir}/doc/culmus-%{version}
%define     fcconfig_dir /etc/fonts/conf.d

Source0:	http://downloads.sourceforge.net/sourceforge/culmus/culmus-%{version}.tar.gz

License:	GPLv2
Group:		System/Fonts
URL:		http://culmus.sourceforge.net/
BuildRoot:	%_tmppath/%name-%version-%release-root
BuildArch:	noarch
# BuildRequires:	XFree86
# SuSE doesn't have chkfontpath. Strange.
# Prereq:		chkfontpath

%description
Added new TrueType font "Shofar" by Yoram Gnat.

15 Hebrew font families. Contain ASCII glyphs from various sources.
Those families provide a basic set of a serif (Frank Ruehl), sans serif
(Nachlieli) and monospaced (Miriam Mono) fonts. Also included Miriam,
Drugulin, Aharoni, David, Hadasim etc. Cantillation marks support is available
in Keter YG and Shofar.

Install the culmus-fonts package if you need a set of Hebrew fonts.

%prep
%setup -n culmus-%{version}

%install
/usr/X11R6/bin/xftcache . || touch XftCache

rm -rf $RPM_BUILD_ROOT

install -m 0755 -d $RPM_BUILD_ROOT%{type1_fonts_dir}
install -m 0755 -d $RPM_BUILD_ROOT%{ttf_fonts_dir}
install -m 0755 -d $RPM_BUILD_ROOT%{fcconfig_dir}
install -m 0644 -p *.afm $RPM_BUILD_ROOT%{type1_fonts_dir}
install -m 0644 -p *.pfa $RPM_BUILD_ROOT%{type1_fonts_dir}
install -m 0644 -p *.ttf $RPM_BUILD_ROOT%{ttf_fonts_dir}
install -m 0644 fonts.scale-type1 $RPM_BUILD_ROOT%{type1_fonts_dir}/fonts.scale
install -m 0644 fonts.scale-ttf $RPM_BUILD_ROOT%{ttf_fonts_dir}/fonts.scale
install -m 0644 XftCache $RPM_BUILD_ROOT%{type1_fonts_dir}/
install -m 0644 XftCache $RPM_BUILD_ROOT%{ttf_fonts_dir}/
install -m 0644 culmus.conf $RPM_BUILD_ROOT%{fcconfig_dir}/39-culmus.conf

mkfontdir $RPM_BUILD_ROOT%{type1_fonts_dir}
mkfontdir $RPM_BUILD_ROOT%{ttf_fonts_dir}

%post
# if chkfontpath exists, execute it
if [ -x %{_sbindir}/chkfontpath ]; then
        %{_sbindir}/chkfontpath -q -a %{type1_fonts_dir}
	%{_sbindir}/chkfontpath -q -a %{ttf_fonts_dir}
fi
# avoid making fc-cache a requirement
if which fc-cache >&/dev/null; then
  fc-cache
fi

%postun
if [ "$1" = "0" ]; then
# if chkfontpath exists, execute it
	if [ -x %{_sbindir}/chkfontpath ]; then
                %{_sbindir}/chkfontpath -q -r %{type1_fonts_dir}
		%{_sbindir}/chkfontpath -q -r %{ttf_fonts_dir}
	fi
fi

%clean
rm -rf $RPM_BUILD_ROOT

%files
%defattr(0644,root,root,0755)
%dir %{type1_fonts_dir}
%dir %{ttf_fonts_dir}
%doc CHANGES LICENSE LICENSE-BITSTREAM GNU-GPL
%config(noreplace) %{type1_fonts_dir}/fonts.dir
%config(noreplace) %{type1_fonts_dir}/fonts.scale
%config(noreplace) %{ttf_fonts_dir}/fonts.dir
%config(noreplace) %{ttf_fonts_dir}/fonts.scale
%config(noreplace) %{fcconfig_dir}/39-culmus.conf
%{type1_fonts_dir}/*.afm
%{type1_fonts_dir}/*.pfa
%{ttf_fonts_dir}/*.ttf
%{type1_fonts_dir}/XftCache
%{ttf_fonts_dir}/XftCache

%changelog
* Wed Nov 17 2010 Maxim Iorsh <iorsh@users.sourceforge.net> 0.120-1
- removed local.conf, moved culmus.conf, cleaned

* Sat Jul 17 2008 Maxim Iorsh <iorsh@math.technion.ac.il> 0.102-1
- added TrueType directory

* Sat Jun 12 2004 Maxim Iorsh <iorsh@math.technion.ac.il> 0.100-1
- /etc/fonts/local.conf is installed, if didn't exist previously

* Wed Jan 28 2004 Maxim Iorsh <iorsh@math.technion.ac.il> 0.93-1
- added custom configuration file /etc/fonts/culmus.conf
- made chkfontpath non-mandatory (SuSE 9 doesn't have it)

* Wed Sep 03 2003 Maxim Iorsh <iorsh@math.technion.ac.il> 0.90-2
- fixed issue about printing from Open Office

* Fri Aug 15 2003 Maxim Iorsh <iorsh@math.technion.ac.il> 0.90-1
- major release
- version numbers changed by request from O. Taylor (RH)

* Sun Mar 30 2003 Tzafrir Cohen <tzafrir@technion.ac.il> 0.8-2
- Fixed filename of tar source
- made fc-cache non-mandatory (to work on RH7.3 systems)
- used the macro doc in files list
- add an epoch to allow upgrades from culmus-0.71-1

* Thu Mar 20 2003 Maxim Iorsh <iorsh@math.technion.ac.il> 0.8-1
- Call fc-cache from %%post

* Thu Aug 29 2002 Tzafrir Cohen <tzafrir@technion.ac.il> 0.5-1
- created spec for version 0.5, based on URW package
