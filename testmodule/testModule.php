<?php
if (!defined('_PS_VERSION_'))
  exit;
 
/**
 * Description of testModule
 */
class testModule extends Module {

  public function __construct()
  {
    $this->name = 'testModule';
    $this->tab = 'content_management';
    $this->version = '1.0.0';
    $this->author = 'Sebastien Deschamps';
    $this->need_instance = 0;
    $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_); 
    $this->bootstrap = true;
 
    parent::__construct();
 
    $this->displayName = $this->l('Sebastien Deschamps test module.');
    $this->description = $this->l('Module to display some components under the menu of prestashop.');
 
    $this->confirmUninstall = $this->l('Are you sure you want to uninstall?');
 
    if (!Configuration::get('TESTMODULE_NAME')) {
      $this->warning = $this->l('No name provided');
    }
  }

  public function install()
  {
    if (Shop::isFeatureActive()) {
            Shop::setContext(Shop::CONTEXT_ALL);
    }

    //define special hook for this module
    $sqlAddHook = 'INSERT INTO `'._DB_PREFIX_.'hook` (`name`, `title`, `description`, `position`, `live_edit`) VALUES '
            . '(\'displayTestModule\', \'Display testModule hook\', \'A hook to display the content of testmodule module\', 1, 0);';

    if (!parent::install() ||
      !Db::getInstance()->execute($sqlAddHook) ||
      !$this->registerHook('displayTestModule') ||
      !$this->registerHook('header') ||
      !Configuration::updateValue('TESTMODULE_NAME', 'testmodule')
    ) {
        return false;
    }
    else {
      return true;
    }
  }

  public function uninstall()
  {
    // uninstall module, module configuration, and module hook
    $hookId = Hook::getIdByName('displayTestModule');
    $hookHeaderId = Hook::getIdByName('header');    
    return parent::uninstall()
            && $this->unregisterHook((int)$hookId)
            && $this->unregisterHook((int)$hookHeaderId)
            && Db::getInstance()->Execute('DELETE FROM `'._DB_PREFIX_.'hook` WHERE `'._DB_PREFIX_.'hook`.`name`=displayTestModule;')
            && Configuration::deleteByName('TESTMODULE_NAME');
  }

  public function hookDisplayTestModule($params)
  {
    $this->context->smarty->assign(
      array(
        'testmodule_name' => Configuration::get('TESTMODULE_NAME')
      )
    );
    return $this->display(__FILE__, 'testmodule.tpl');
  }

  public function hookDisplayHeader()
  {
    $this->context->controller->addCSS($this->_path.'css/testmodule.css', 'all');
  }
}