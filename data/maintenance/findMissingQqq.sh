#!/usr/bin/env bash

diff -uw <(jq -S 'keys' < ../i18n/en.json) <(jq -S 'keys' < ../i18n/qqq.json)
