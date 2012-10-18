@echo off
if cmdextversion 2 goto :cmdok
echo Sorry, this batch file requires a more recent version of Windows.
goto :eof

:cmdok
setlocal
setlocal enabledelayedexpansion

call :searchpath xsltproc.exe
if errorlevel 1 goto :eof
set XSLTPROC=%RES%

set NOOP=
set DEBUG=
set TRACE=
:getopt
if /I "%1" EQU "/n" set NOOP=1 && shift /1 && goto :getopt
if /I "%1" EQU "-n" set NOOP=1 && shift /1 && goto :getopt
if /I "%1" EQU "/d" set DEBUG=1 && shift /1 && goto :getopt
if /I "%1" EQU "-d" set DEBUG=1 && shift /1 && goto :getopt
if /I "%1" EQU "/t" set TRACE=1 && shift /1 && goto :getopt
if /I "%1" EQU "-t" set TRACE=1 && shift /1 && goto :getopt

if DEFINED NOOP (
  SET X=echo+
) ELSE (
  SET X=
)

set XSL_DEBUG=--param debug true^^^(^^^)
set XSLTPROC_PARAMS=--nonet --novalid --xinclude
set XSL_FILE=make.xsl
if DEFINED DEBUG set XSLTPROC_PARAMS=%XSLTPROC_PARAMS% %XSL_DEBUG%
if DEFINED TRACE set XSLTPROC_PARAMS=%XSLTPROC_PARAMS% --load-trace
set TARGET_OK=
set TARGETS=
for /F "delims=:_ tokens=1,2" %%L in (%~sf0) DO (
    if "%%L" EQU "target" (
        set TARGETS=!TARGETS! %%M
        if /I "%%M" EQU "%1" set TARGET_OK=yes
    )
)
if "%TARGET_OK%"=="" goto :usage
goto :target_%1
:usage
echo Usage: make [-n][-d][-t] target
echo where target is one of %TARGETS%
echo -n (or /n) Display but don't execute commands
echo -d (or /d) Include XML comments in output files.
echo -t (or /t) Show XML files as they are loaded
goto :eof

:target_kupu.html
    %X%%XSLTPROC% %XSLTPROC_PARAMS% -o common\kupu.html %XSL_FILE% dist.kupu 
    goto :eof

:target_zope2macros
    %X%%XSLTPROC% %XSLTPROC_PARAMS% -o common\kupumacros.html %XSL_FILE% dist-zope2.kupu
    goto :eof

:target_kupuform.html:
    %X%%XSLTPROC% %XSLTPROC_PARAMS% -o common\kupuform.html %XSL_FILE% dist-form.kupu
    goto :eof

:target_kupumulti.html:
    %X%%XSLTPROC% %XSLTPROC_PARAMS% -o common\kupumulti.html %XSL_FILE% dist-multi.kupu
    goto :eof

:target_kupucnf.html:
    %X%%XSLTPROC% %XSLTPROC_PARAMS% -o common\kupucnf.html %XSL_FILE% dist-cnf.kupu
    goto :eof

:target_plonemacros
    %X%%XSLTPROC% %XSLTPROC_PARAMS% -o plone\kupu_plone_layer\kupu_wysiwyg_support.html %XSL_FILE% dist-plone.kupu
    goto :eof

:target_silvamacros
    %X%%XSLTPROC% %XSLTPROC_PARAMS% -o silva\kupumacros.html %XSL_FILE% dist-silva.kupu
    goto :eof

:target_lenyamacros
    %X%%XSLTPROC% %XSLTPROC_PARAMS% -o apache-lenya\kupu\kupumacros.html %XSL_FILE% dist-apache-lenya.kupu
    goto :eof

:target_
:target_all
    call :target_clean
    call :target_kupu.html
    call :target_kupuform.html
    call :target_kupumulti.html
    call :target_zope2macros
    call :target_plonemacros
    call :target_silvamacros
    call :target_lenyamacros
    call :target_kupucnf.html
    goto :eof

:target_clean
    SET FILES=common\kupumacros.html common\kupu.html common\kupuform.html
    SET FILES=%FILES% plone\kupu_plone_layer\kupu_wysiwyg_support.html silva\kupumacros.html
    SET FILES=%FILES% apache-lenya\kupu\kupumacros.html    
    SET FILES=%FILES% common\kupumulti.html common\kupucnf.html
    for %%F in (%FILES%) DO (
        IF EXIST %%F ( %X%echo del %%F && %X%del %%F )
    )
    goto :eof

:searchpath
    REM Search the path for the specified file. Also, for added
    REM friendliness, we extend the path with a few other 'potential'
    REM directories.
    SET PATHX=%PATH%;C:\libxslt;c:\Program Files\libxml\util
    set RES=%~s$PATHX:1
    if not errorlevel 1 goto :eof
    echo File %1 was not found in the PATH environment
    goto :eof
