# !/bin/bash

# folders
templates="data/tmp/templates_c/"
es="data/tmp/js/es_ES/"
en="data/tmp/js/en_US/"

# templates
if [ -d $templates ] && [ "$(ls $templates)" ]; then
   sudo rm -r $templates*
fi

# es
if [ -d $es ] && [ "$(ls $es)" ]; then
   sudo rm -r $es*
fi

# en
if [ -d $en ] && [ "$(ls $en)" ]; then
   sudo rm -r $en*
fi
