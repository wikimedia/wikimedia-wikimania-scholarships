<?php
/**
 * @section LICENSE
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along
 * with this program; if not, write to the Free Software Foundation, Inc.,
 * 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.
 * http://www.gnu.org/copyleft/gpl.html
 *
 * @file
 */
require_once __DIR__ . '/../vendor/autoload.php';

// Twelve-Factor App configuration
// populate $_ENV from .env if present
if (is_readable(__DIR__ . '/../.env')) {
  $lines = explode("\n", file_get_contents(__DIR__ . '/../.env'));

  foreach ($lines as $line) {
    // variable extraction logic lifted from
    // https://github.com/bkeepers/dotenv
    if (preg_match('/\A(?:export\s+)?(\w+)(?:=|: ?)(.*)\z/', $line, $group)) {
      $key = $group[1];
      $val = $group[2];
      if (preg_match('/\A\'(.*)\'\z/', $val, $group)) {
        $val = $group[1];
      } else if (preg_match('/\A"(.*)"\z/', $val, $group)) {
        $val = $group[1];
      }
      // store in super global
      $_ENV[$key] = $val;
      // also store in process env vars
      putenv("{$key}={$val}");
    }
  } //end foreach line
} //end if .env
