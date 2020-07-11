#!/usr/bin/env bash
sleep 10;
/var/www/brana/bin/console messenger:consume -vv >&1;