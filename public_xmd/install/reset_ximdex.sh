rm ./conf/_STATUSFILE
rm ./conf/install-params.conf.php
rm ./conf/install-modules.php

# rm ./logs/*.log
cp /dev/null ./logs/actions.log
cp /dev/null ./logs/preview.log
cp /dev/null ./logs/publication.log
cp /dev/null ./logs/scheduler.log
cp /dev/null ./logs/xmd.log
cp /dev/null ./logs/xslt.log

rm -rf ./data/cache/pipelines/*
rm -rf ./data/files/*
rm -rf ./data/nodes/*
rm -rf ./data/previos/*
rm -rf ./data/sync/serverframes/*
rm ./data/tmp/*
rm -rf ./data/tmp/js/*
rm -rf ./data/tmp/templates_c/*
rm -rf ./data/tmp/uploaded_files/
rm ./data/.x*
rm ./data/.X*

rm -rf ./.data

rm ./tests/_output/*
rm ./tests/_support/_generated/*
