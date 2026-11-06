#!/bin/bash
set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "$0")" && pwd)"
echo "This script no longer moves application files or overwrites public_html."
echo "Upload the project to ~/timcare-dev, configure ~/timcare-dev/.env, then run:"
echo "  cd ~/timcare-dev && bash deploy-cpanel-subdirectory.sh"
