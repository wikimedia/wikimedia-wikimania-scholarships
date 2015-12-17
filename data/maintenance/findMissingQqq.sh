#!/usr/bin/env bash
# Find en.json messages that are missing descriptions in the qqq.json file

set -e

builtin hash jq &>/dev/null || {
  echo "${BASH_SOURCE[0]} requires 'jq'" 1>&2
  exit 1
}

CURRENT_DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
I18N="$( cd "$CURRENT_DIR/.." && pwd )"
EN="${I18N}/i18n/en.json"
QQQ="${I18N}/i18n/qqq.json"

echo "--- ${EN#$PWD/}"
echo "+++ ${QQQ#$PWD/}"
diff -uw <(jq -S 'keys' < "${EN}") <(jq -S 'keys' < "${QQQ}") |
    grep -Ev '^[-+]{3} ' ||
    echo "Keys are in sync."
