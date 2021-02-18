<?php
/**
 * Classe pour la découverte des objets
 */

use Sabinus\TuyaCloudApi\Device\Device;
use Sabinus\TuyaCloudApi\Device\DeviceFactory;


class SmartLifeDiscovery
{

    /**
     * Objet du device découvert
     * 
     * @param Device
     */
    private $device;

    /**
     * Objet eqLogic
     * 
     * @param SmartLife
     */
    private $eqLogic;

    /**
     * Si nouvel équipement créé
     * 
     * @param Boolean
     */
    private $isNew;



    /**
     * Constructeur
     * 
     * @param Device $device découvert
     */
    public function __construct(Device $device)
    {
        $this->device = $device;
        $this->isNew = false;
    }


    /**
     * Execute le traitement d'ajout de l'objet trouvé lors de la découverte
     */
    public function execute()
    {
        // Vérification de l'équipement
        if ( empty($this->device) || empty($this->device->getId()) || empty($this->device->getName()) || empty($this->device->getType()) ) {
            SmartLifeLog::info('DISCOVERY', 'Information manquante pour ajouter l\'équipement : '.$this->device);
            return;
        }

        // Si objet non reconnu
        if ( $this->isUnknow() ) {
            SmartLifeLog::info('DISCOVERY', 'Objet non pris en compte '.$this->device);
            return;
        }

        // Création de l'objet eqLogic
        $this->createEqLogic();

        // Affiche une info si nouvel objet découvert
        if ( $this->isNew ) {
            event::add('jeedom::alert', array(
                'level' => 'warning',
                'page' => 'SmartLife',
                'message' => __('Objet ajouté avec succès "'.$this->device->getName().'" de type "'.$this->device->getType().'"', __FILE__),
            ));
        }
    }


    /**
     * Si device connu par mon API
     * 
     * @return Boolean
     */
    private function isUnknow()
    {
        return ( $this->device->getType() == DeviceFactory::TUYA_UNKNOWN );
    }


    /**
     * Crée l'objet Jeedom de l'équipement trouvé sur le Cloud Tuya
     */
    private function createEqLogic()
    {
        SmartLifeLog::header('DISCOVERY', $this->device, 'Objet en cours d\'inclusion');

        // Chargement de la configuration de l'objet trouvé
        $configCmdDevice = new SmartLifeConfig($this->device);
        if ( ! $configCmdDevice->loadJSON() ) {
            SmartLifeLog::debug('DISCOVERY', $this->device, 'ERROR lors du chargement du fichier '.$this->fileConfig);
        }

        // Recherche si l'objet existe déjà
        $this->eqLogic = SmartLife::byLogicalId($this->device->getId(), 'SmartLife');

        // Créer le nouvel équipement s'il n'existe pas
		if ( !is_object($this->eqLogic) ) {
            $this->newSmartLifeEqLogic();
            SmartLifeLog::info('DISCOVERY', $this->device, '!!! NOUVEL objet trouvé !!!');
        }
        $this->saveEqLogic();

        // Création des commandes
        $this->createCommands($configCmdDevice->getCommands());

        SmartLifeLog::info('DISCOVERY', $this->device, 'Objet ajouté avec succès');
        return $this->isNew;
    }


    /**
     * Crée un nouveau eqLogic qui n'existait pas avant
     */
    private function newSmartLifeEqLogic()
    {
		$this->eqLogic = new SmartLife();
		$this->eqLogic->setEqType_name('SmartLife');
        $this->eqLogic->setLogicalId($this->device->getId());
        $this->eqLogic->setName($this->device->getName() . ' ' . $this->device->getId());
        $this->isNew = true;
    }


    /**
     * Sauvegarde l'eqLogic
     */
    public function saveEqLogic()
    {
        // Affecte la configuration du device
        $this->eqLogic->setConfiguration('tuya', null);
        $this->eqLogic->setConfiguration('device', null);
        $this->eqLogic->setConfiguration('tuyaID', $this->device->getId());
        $this->eqLogic->setConfiguration('tuyaType', $this->device->getType());
        $this->eqLogic->setConfiguration('tuyaName', $this->device->getName());
        $this->eqLogic->setConfiguration('tuyaData', serialize($this->device->getData()));

        // Désactive si l'objet n'est plus en ligne
        if (config::byKey('autoenable', 'SmartLife')) {
            if ($this->device->isOnline()) {
			    $this->eqLogic->setIsEnable(1);
                $this->eqLogic->setIsVisible(1);
            } else {
                $this->eqLogic->setIsEnable(0);
                $this->eqLogic->setIsVisible(0);
            }
        }
        
        // Sauvegarde
        //$this->eqLogic->setDevice2($this->device); // TODO
        $this->eqLogic->save(true);
    }


    /**
     * Crée les commandes de l'objet EqLogic de type SmartLife
     * 
     * @param Array $commands : Liste des commandes à créer
     */
    private function createCommands(array $commands)
    {
        $order = 0;
        foreach ($commands as $command) {
            $command['order'] = $order++;
            // Si pas de support pour cette commande, on ne la crée pas
            if ( ! $this->setCommandSpecific($command) ) continue;
            $this->eqLogic->addCommand($command, $this->device);
            SmartLifeLog::debug('DISCOVERY', $this->device, 'SET command  = '.$command['logicalId']);
        }
    }


    /**
     * Ajuste la configuration de la commande en fonction du type de l'objet
     *
     * @param Array $command : Configuration de la commande
     **/
    public function setCommandSpecific(array & $command)
    {
        switch ($this->device->getType()) {
            case DeviceFactory::TUYA_CLIMATE :
                if ( $command['logicalId'] == 'TEMPERATURE' || $command['logicalId'] == 'THERMOSTAT' ) {
                    // Affecte les valeurs min et max en fonction des valeurs du climatiseur
                    $command['configuration']['minValue'] = $this->device->getMinTemperature();
                    $command['configuration']['maxValue'] = $this->device->getMaxTemperature();
                }
                if ( $command['logicalId'] == 'TEMPERATURE' && ! $this->device->getSupportTemperature() ) return false;
                break;
            case DeviceFactory::TUYA_LIGHT :
                // Si pas de support de la couleur
                if ( $command['logicalId'] == 'COLORHUE' ) return false;
                if ( $command['logicalId'] == 'SetColor' && ! $this->device->getSupportColor() ) return false;
                // Si pas de support de la température
                if ( $command['logicalId'] == 'TEMPERATURE' && ! $this->device->getSupportTemperature() ) return false;
                if ( $command['logicalId'] == 'SetTemperature' && ! $this->device->getSupportTemperature() ) return false;
        }
        return true;
    }

}