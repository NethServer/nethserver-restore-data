<?php
/*
 * Copyright (C) 2013 Nethesis S.r.l.
 * http://www.nethesis.it - support@nethesis.it
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

    // Declare all parameters
    public function initialize()
    {
        parent::initialize();
    }

    public function prepareView(\Nethgui\View\ViewInterface $view) {
        $db_dir = "/var/cache/duc/duc.db";
        $start_index = "/";
        $tree = array();

        header('Content-type: application/json; charset: utf-8');

        if($this->getRequest()->hasParameter('base')) {
            $cmd = "/usr/bin/duc xml --min-size=1 --exclude-files --database=$db_dir $start_index";
            $root = array( 'text' => $start_index, 'children' => array() );
            $xml_string = shell_exec($cmd);
            $xml = simplexml_load_string($xml_string);
            $tree = $this->walk_dir($xml, $root);
            echo json_encode($tree);
            exit();
        }

        if($this->getRequest()->hasParameter('start')) {
            $start = $this->getRequest()->getParameter('start');
            $cmd = "/usr/bin/duc xml --min-size=1 --database=$db_dir $start";
            $root = array( 'text' => $start, 'children' => array() );
            $xml_string = shell_exec($cmd);
            $xml = simplexml_load_string($xml_string);
            $tree = $this->walk_dir($xml, $root);
            echo json_encode($tree);
            exit();
        }

        if($this->getRequest()->hasParameter('position') && $this->getRequest()->hasParameter('file')) {
            $position = $this->getRequest()->getParameter('position');
            $file = $this->getRequest()->getParameter('file');
            $cmd = "/usr/bin/sudo /sbin/e-smith/restore-file $position $file";
            $result = $this->getPlatform()->exec($cmd)->getExitCode();
            echo json_encode($result);
            exit();
        }

        parent::prepareView($view);
    }

    private function walk_dir($dir, &$root) {
        foreach ($dir->ent as $ent) {
            $name = (string)$ent['name'];
            if ( !$ent->count() ) {
                $root['children'][] = array( 'text' => $name );
            } else {
                $node = array( 'text' => $name, 'children' => array());
                $root['children'][] = $this->walk_dir($ent, $node);
            }
        }
        return $root;
    }
}
