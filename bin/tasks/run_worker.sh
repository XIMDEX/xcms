#!/usr/bin/env bash

# cada worker estará en ejecución un máx de 5 minutos

while true
do
    echo "STARTED NEW WORKER" ;
	./worker.php
	# sleep 1
done

# timeout 5s ./worker.php

