#!/bin/bash
rdfproc %1 parse %2
rdfproc %1 print > %3
