#!/bin/bash
set -e

cd /var/www/html

echo "Running project setup via Composer script..."
composer run setup
