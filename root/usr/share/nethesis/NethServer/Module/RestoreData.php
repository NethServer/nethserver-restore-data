<?php
/*
 * Copyright (C) 2019 Nethesis S.r.l.
 * http://www.nethesis.it
 *
 * This script is part of NethServer.
 *
 * NethServer is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License,
 * or any later version.
 *
 * NethServer is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with NethServer.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace NethServer\Module;
use Nethgui\System\PlatformInterface as Validate;

class RestoreData extends \Nethgui\Controller\AbstractController
{

    protected function initializeAttributes(\Nethgui\Module\ModuleAttributesInterface $base)
    {
        return \Nethgui\Module\SimpleModuleAttributesProvider::extendModuleAttributes($base, 'Configuration', 80);
    }

    public function prepareView(\Nethgui\View\ViewInterface $view) {
        parent::prepareView($view);
        if (file_exists("/etc/e-smith/db/configuration/defaults/cockpit.socket")) {
                $view['cockpitUrl'] = "https://". $_SERVER['SERVER_NAME'] . ":9090/nethserver#/applications/nethserver-restore-data";
        } else {
                $view['cockpitUrl'] = '';
        }
        $view['scUrl'] = $view->getModuleUrl('/PackageManager');
    }

}
