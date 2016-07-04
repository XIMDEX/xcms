#!/usr/bin/env bash

# envia un ping a los workers que les obliga a cerrarse (para refrescar la conexión a la base de datos)

/usr/local/bin/q-put xbuk '{"function": "xbuk::ping" , "user_data" : [] }'


