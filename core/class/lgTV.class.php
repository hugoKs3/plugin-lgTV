<?php

/* This file is part of Jeedom.
*
* Jeedom is free software: you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation, either version 3 of the License, or
* (at your option) any later version.
*
* Jeedom is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
* GNU General Public License for more details.
*
* You should have received a copy of the GNU General Public License
* along with Jeedom. If not, see <http://www.gnu.org/licenses/>.
*/

/* * ***************************Includes********************************* */
require_once dirname(__FILE__) . '/../../../../core/php/core.inc.php';
include_file('core', 'lgTV', 'config', 'lgTV');
class lgTV extends eqLogic {
    /*     * *************************Attributs****************************** */


    /*     * ***********************Methode static*************************** */


    /*     * *********************Methode d'instance************************* */

    public function preUpdate() {
        
		if ($this->getConfiguration('addr') == '') {
            throw new Exception(__('L\'adresse IP ne peut etre vide. Vous pouvez la trouver dans les paramètres de votre TV ou de votre routeur (box).',__FILE__));
        }
		if ($this->getConfiguration('key') == '') {
            throw new Exception(__('La clé d\'appairage ne peut etre vide. Si vous ne la connaissez pas mettez 0 et suivez les étapes indiquées',__FILE__));
        }
		
    }
	
	public function getGroups() {
       return array('basic', 'numero', 'magneto', 'couleur', 'divers');
    }
	
	public function commandByName($name) {
        global $listCmdlgTV;
        
        foreach ($listCmdlgTV as $cmd) {
           if ($cmd['name'] == $name)
            return $cmd;
        }
        
        return null;
    }
	
	 public function addCommand($cmd) {
       if (cmd::byEqLogicIdCmdName($this->getId(), $cmd['name']))
            return;
            
       if ($cmd) {
            $lgTVCmd = new lgTVCmd();
            $lgTVCmd->setName(__($cmd['name'], __FILE__));
            $lgTVCmd->setEqLogic_id($this->id);
		    $lgTVCmd->setConfiguration('request', $cmd['configuration']['request']);
		    $lgTVCmd->setConfiguration('parameters', $cmd['configuration']['parameters']);
		    $lgTVCmd->setConfiguration('group', $cmd['group']);
            $lgTVCmd->setType($cmd['type']);
            $lgTVCmd->setSubType($cmd['subType']);
			if ($cmd['icon'] != '')
				$lgTVCmd->setDisplay('icon', '<i class=" '.$cmd['icon'].'"></i>');
		    $lgTVCmd->save();
       }
    }
    
    public function addCommandByName($name, $cmd_name) {
       if ($cmd = $this->commandByName($name)) {
			$this->addCommand($cmd);
       }
    }

    public function removeCommand($name) {
        if (($cmd = cmd::byEqLogicIdCmdName($this->getId(), $name)))
			$cmd->remove();
    }
    
    public function addCommands($groupname) {
        global $listCmdlgTV;
        
        foreach ($listCmdlgTV as $cmd) {
           if ($cmd['group'] == $groupname)
				$this->addCommand($cmd);
        }        
    }
    
    public function removeCommands($groupname) {
        global $listCmdlgTV;
        
        foreach ($listCmdlgTV as $cmd) {
           if ($cmd['group'] == $groupname)
				$this->removeCommand($cmd['name']);
        }
    }
	
	
    public function preSave() {
		if (!$this->getId())
          return;
		  
		if ($this->getConfiguration('has_divers') == 1) {
			$this->addCommands('divers');
        } else {
            $this->removeCommands('divers');
        }
		 if ($this->getConfiguration('has_basic') == 1) {
			$this->addCommands('basic');
        } else {
            $this->removeCommands('basic');
        }
		
        if ($this->getConfiguration('has_num') == 1) {
			$this->addCommands('numero');
        } else {
            $this->removeCommands('numero');
        }
		
		if ($this->getConfiguration('has_magneto') == 1) {
			$this->addCommands('magneto');
        } else {
            $this->removeCommands('magneto');
        }
		
		if ($this->getConfiguration('has_color') == 1) {
			$this->addCommands('couleur');
        } else {
            $this->removeCommands('couleur');
        }
		
    }
	
    public function postSave() {
	}
    

	public function postInsert() {
	   
    
    }
	
	public function toHtml($_version = 'dashboard') {
		if ($this->getIsEnable() != 1) {
            return '';
        }
		if (!$this->hasRight('r')) {
			return '';
		}
        $_version = jeedom::versionAlias($_version);
		$replace = array(
			'#id#' => $this->getId(),
			'#info#' => (isset($info)) ? $info : '',
			'#name#' => ($this->getIsEnable()) ? $this->getName() : '<del>' . $this->getName() . '</del>',
			'#eqLink#' => $this->getLinkToConfiguration(),
			'#action#' => (isset($action)) ? $action : '',
			'#background_color#' => $this->getBackgroundColor($_version),
		);
		
		// Charger les template de groupe
        $groups_template = array();
        $group_names = $this->getGroups();
		foreach ($group_names as $group) {
            $groups_template[$group] = getTemplate('core', $_version, $group, 'lgTV');
            $replace['#group_'.$group.'#'] = '';
        }
		
		// Afficher les commandes dans les bonnes templates
        // html_groups: permet de gérer le #cmd# dans la template.
        $html_groups = array();
        if ($this->getIsEnable()) {
            foreach ($this->getCmd() as $cmd) {
                $cmd_html = ' ';
                $group    = $cmd->getConfiguration('group');
                if ($cmd->getIsVisible()) {
				
					if ($cmd->getType() == 'info') {
						log::add('lgTV','debug','cmd = info');
						$cmd_html = $cmd->toHtml();
					} else {
						$cmd_template = getTemplate('core', $_version, $group.'_cmd', 'lgTV');        
						$cmd_replace = array(
							'#id#' => $cmd->getId(),
							'#name#' => ($cmd->getDisplay('icon') != '') ? $cmd->getDisplay('icon') : $cmd->getName(),
							'#oriname#' => $cmd->getName(),
							'#theme#' => $this->getConfiguration('theme'),
						);
						
						// Construction du HTML pour #cmd#
						$cmd_html = template_replace($cmd_replace, $cmd_template);
					}
                    if (isset($html_groups[$group]))
					{
						$html_groups[$group]++;
						$html_groups[$group] .= $cmd_html;
					} else {
						$html_groups[$group] = $cmd_html; 
					}    
                } 
                $cmd_replace = array(
                    '#'.strtolower($cmd->getName()).'#' => $cmd_html,
                    );
                $groups_template[$group] = template_replace($cmd_replace, $groups_template[$group]);
            }
        }
        
        // Remplacer #group_xxx de la template globale
        $replace['#cmd'] = "";
        $keys = array_keys($html_groups);
		foreach ($html_groups as $group => $html_cmd) {      
            $group_template =  $groups_template[$group]; 
            $group_replace = array(
                '#cmd#' => $html_cmd,
            );
            $replace['#group_'.$group.'#'] .= template_replace($group_replace, $group_template);
        }
		$parameters = $this->getDisplay('parameters');
        if (is_array($parameters)) {
            foreach ($parameters as $key => $value) {
                $replace['#' . $key . '#'] = $value;
            }
        }
	
        return template_replace($replace, getTemplate('core', $_version, 'eqLogic', 'lgTV'));
    }
	
	public static function event() {
		$cmd =  lgTVCmd::byId(init('id'));
	   
		if (!is_object($cmd)) {
			throw new Exception('Commande ID virtuel inconnu : ' . init('id'));
		}
	   
		$value = init('value');
       
		if ($cmd->getEqLogic()->getEqType_name() != 'lgTV') {
			throw new Exception(__('La cible de la commande lgTV n\'est pas un équipement de type lgTV', __FILE__));
		}
		   
		$cmd->event($value);
	   
		$cmd->setConfiguration('valeur', $value);
		log::add('lgTV','debug','set:'.$cmd->getName().' to '. $value);
		$cmd->save();
		
   }
   
}

class lgTVCmd extends cmd {
    /*     * *************************Attributs****************************** */


    /*     * ***********************Methode static*************************** */


    /*     * *********************Methode d'instance************************* */

    public function preSave() {
        if ($this->getConfiguration('request') == '') {
            throw new Exception(__('La requete ne peut etre vide',__FILE__));
		}
		
		
    }

    public function execute($_options = null) {
    	$lgTV = $this->getEqLogic();
        $lg_path = realpath(dirname(__FILE__) . '/../../3rdparty');
		$tvip = $lgTV->getConfiguration('addr');
    	$key = $lgTV->getConfiguration('key');
		$volnum = $lgTV->getConfiguration('volnum');
		if ($this->type == 'action') {
				$type=$this->getConfiguration('type');
				$command=$this->getConfiguration('parameters');
				$commande= $command . ' ' . $tvip . ' ' . $key;
				shell_exec('/usr/bin/python ' . $lg_path . '/lg.py ' .$commande);
				if ($command=='24' or $command=='25') {
					for ($i = 1; $i <= $volnum-1; $i++) {
						shell_exec('/usr/bin/python ' . $lg_path . '/lg.py ' .$commande);
					}
				}
		}
    }
		


    /*     * **********************Getteur Setteur*************************** */
}
?>