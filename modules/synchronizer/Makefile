# Makefile for ximCRON.c
# $Id: Makefile 267 2006-04-12 17:25:36Z aleiva $

CFLAGS=-O -Wall

.c:
	$(CC) $(CFLAGS) $@.c -o $@

ximCRON: ximCRON.c

install: ximCRON
	@echo You must be root to install
	chown root:www-data ximCRON
	chmod 4770 ximCRON

clean:
	@echo Cleaning...
	if [ -f ./ximCRON ]; then rm ximCRON; fi
