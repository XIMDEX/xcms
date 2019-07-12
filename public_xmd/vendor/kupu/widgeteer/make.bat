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

:target_dist
    %X%%XSLTPROC% %XSLTPROC_PARAMS% -o ..\common\kupu-widgeteer.html %XSL_FILE% dist.kupu 
    goto :eof

:target_
:target_all
    call :target_clean
    call :target_dist
    goto :eof

:target_clean
    SET FILES=..\common\kupu-widgeteer.html
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
