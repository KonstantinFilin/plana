#!/bin/bash

chown -R ksf:www-data ./storage;
find ./storage/ -type d -exec chmod 775 {} \;
find ./storage/ -type f -exec chmod 664 {} \;


