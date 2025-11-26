#!/bin/bash

#
# -------------------------------------------------------------------------
# Jamf plugin for GLPI
# -------------------------------------------------------------------------
#
# LICENSE
#
# This file is part of Jamf.
#
# Jamf is free software; you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation; either version 2 of the License, or
# (at your option) any later version.
#
# Jamf is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
#
# You should have received a copy of the GNU General Public License
# along with Jamf. If not, see <http://www.gnu.org/licenses/>.
# -------------------------------------------------------------------------
# @copyright Copyright (C) 2014-2023 by Teclib'.
# @license   GPLv2 https://www.gnu.org/licenses/gpl-2.0.html
# @link      https://github.com/pluginsGLPI/jamf
# -------------------------------------------------------------------------
#

# Clean existing file
rm -f locales/jamf.pot && touch locales/jamf.pot

# Append locales from PHP
xgettext `find ./ajax ./config ./front ./inc ./tests ./*.php -type f -name "*.php"` -o locales/jamf.pot -L PHP --add-comments=TRANS --from-code=UTF-8 --force-po --join-existing \
    --keyword=_n:1,2 --keyword=__s --keyword=__ --keyword=_x:1c,2 --keyword=_sx:1c,2 --keyword=_nx:1c,2,3 --keyword=_sn:1,2

# Append locales from JavaScript
xgettext js/*.js -o locales/jamf.pot -L JavaScript --add-comments=TRANS --from-code=UTF-8 --force-po --join-existing \
    --keyword=_n:1,2 --keyword=__ --keyword=_x:1c,2 --keyword=_nx:1c,2,3 \
    --keyword=i18n._n:1,2 --keyword=i18n.__ --keyword=i18n._p:1c,2 \
    --keyword=i18n.ngettext:1,2 --keyword=i18n.gettext --keyword=i18n.pgettext:1c,2

#Update main language
LANG=C msginit --no-translator -i locales/jamf.pot -l en_GB -o locales/en_GB.po