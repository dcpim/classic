#!/bin/bash
# Renew all LetsEncrypt certificates

set -e

sudo /usr/bin/certbot renew

echo "DONE!"

