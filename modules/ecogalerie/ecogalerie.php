<?php

/**
 * 2010-2014 EcomiZ
 *
 *  @author    EcomiZ
 *  @copyright 2010-2014
 *  @version  Release: 1.1 $Revision: 1 $
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */
require_once _PS_MODULE_DIR_ . 'ecogalerie/models/eco_galerie.php';
require_once _PS_MODULE_DIR_ . 'ecogalerie/models/eco_galerieSlider.php'; //---------------------

class EcoGalerie extends Module {

    public function __construct() {

        $this->name = 'ecogalerie';
        $this->tab = 'administration'; // catégorie module
        $this->version = '1.1';
        $this->author = 'Mamen';
        $this->module_key = '9d664a39daaf347fd84d187fc34908e1';
        $this->displayName = $this->l('ecogalerie');
        $this->description = $this->l('Module ecogalerie par Mamen chez Ecomiz');

        parent::__construct();
    }

    public function install() {
        $this->createDb(); // création bdd
        $this->createDb2(); // création bdd 2 -------------------------------------
        $this->installBackOffice();
        Configuration::updateValue('ECO_PATH_ARTICLE', _PS_ROOT_DIR_ . '/modules/ecogalerie/img/');
        Configuration::updateValue('ECO_PRESSE_WIDTH_IMG', '259');
        Configuration::updateValue('ECO_PRESSE_HEIGHT_IMG', '354');
        Configuration::updateValue('ECO_PRESSE_TITLE', 'Galerie');
        Configuration::updateValue('ECO_PRESSE_DISPLAY_LEGEND', 1);
        Configuration::updateValue('ECO_PRESSE_FLOAT', 1);
        return parent::install() && $this->registerHook('displayHeader');
    }

    public function uninstall() {
        $this->uninstallModuleTab('AdminEcoGalerie');
        return parent::uninstall();
    }

    public function installBackOffice() {
        $id_lang_en = LanguageCore::getIdByIso('en');
        $id_lang_fr = LanguageCore::getIdByIso('fr');
        $id_root_tab = Tab::getIdFromClassName('AdminEcomiz');
        if (empty($id_root_tab)) {
            $this->installModuleTab('AdminEcomiz', array($id_lang_fr => 'Modules EcomiZ', $id_lang_en => 'EcomiZ module'), '0');
            $id_root_tab = Tab::getIdFromClassName('AdminEcomiz');
        }
        $this->installModuleTab('AdminEcoGalerie', array($id_lang_fr => 'Galerie', $id_lang_en => 'Gallery'), $id_root_tab);
        $this->installModuleTab('AdminEcoGalerieSlider', array($id_lang_fr => 'Galerie Défile', $id_lang_en => 'Gallery Slider'), $id_root_tab);
    }

    private function installModuleTab($tab_class, $tab_name, $id_tab_parent) {
        $tab = new Tab();
        $tab->name = $tab_name;
        $tab->class_name = $tab_class;
        $tab->module = $this->name;
        $tab->id_parent = (int) $id_tab_parent;
        if (!$tab->save())
            return false;
        return true;
    }

    private function uninstallModuleTab($tab_class) {
        $id_tab = Tab::getIdFromClassName($tab_class);
        if ($id_tab != 0) {
            $tab = new Tab($id_tab);
            $tab->delete();
            return true;
        }
        return false;
    }

    public function getContent() {
        $output = null;

        if (Tools::isSubmit('submit' . $this->name)) {
            $width_img = Tools::getValue('ECO_PRESSE_WIDTH_IMG');
            $height_img = Tools::getValue('ECO_PRESSE_HEIGHT_IMG');
            $title = Tools::getValue('ECO_PRESSE_TITLE');
            $display_legend = Tools::getValue('ECO_PRESSE_DISPLAY_LEGEND');
            $float = Tools::getValue('ECO_PRESSE_FLOAT');

            if (!$width_img || empty($width_img) || !$height_img || empty($height_img) || !$title || empty($title))
                $output .= $this->displayError($this->l('Invalid Configuration value'));
            else {
                Configuration::updateValue('ECO_PRESSE_TITLE', $title);
                Configuration::updateValue('ECO_PRESSE_WIDTH_IMG', $width_img);
                Configuration::updateValue('ECO_PRESSE_HEIGHT_IMG', $height_img);
                Configuration::updateValue('ECO_PRESSE_DISPLAY_LEGEND', $display_legend);
                Configuration::updateValue('ECO_PRESSE_FLOAT', $float);

                $output .= $this->displayConfirmation($this->l('Settings updated'));
            }
        }

        // contact
        $form = '';
        $form .= '<br/>
			<fieldset>
			<legend>EcomiZ</legend>
			<p>
			' . $this->l('This module has been developped by') . '<strong><a href="http://www.ecomiz.com">  EcomiZ</a></strong><br />
			' . $this->l('Please report all bugs to') . '<strong><a  href="mailto:support@ecomiz.com">  support@ecomiz.com</a></strong>
			</p>
			</fieldset>';

        return $output . $this->displayForm() . $form;
    }

    public function displayForm() {
        // Get default Language
        $default_lang = (int) Configuration::get('PS_LANG_DEFAULT');

        // Init Fields form array
        $fields_form = array();
        $fields_form[0]['form'] = array(
            'legend' => array(
                'title' => $this->l('Settings'),
            ),
            'input' => array(
                array(
                    'type' => 'text',
                    'label' => $this->l('Title'),
                    'name' => 'ECO_PRESSE_TITLE',
                    'size' => 20,
                    'required' => true
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Small image width (px)'),
                    'name' => 'ECO_PRESSE_WIDTH_IMG',
                    'size' => 20,
                    'required' => true
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Small image height (px)'),
                    'name' => 'ECO_PRESSE_HEIGHT_IMG',
                    'size' => 20,
                    'required' => true
                ),
                array(
                    'type' => 'radio',
                    'label' => $this->l('Display legend'),
                    'name' => 'ECO_PRESSE_DISPLAY_LEGEND',
                    'class' => 't',
                    'size' => 20,
                    'required' => true,
                    'values' => array(
                        array(
                            'id' => 'active_on',
                            'value' => 1,
                            'label' => $this->l('Yes'),
                        ),
                        array(
                            'id' => 'active_off',
                            'value' => 0,
                            'label' => $this->l('No'),
                        ),
                    ),
                    'desc' => $this->l('Check "Yes" if you want to display legend')
                ),
                array(
                    'type' => 'radio',
                    'label' => $this->l('Display block vertical/inline'),
                    'name' => 'ECO_PRESSE_FLOAT',
                    'class' => 't',
                    'size' => 20,
                    'required' => true,
                    'values' => array(
                        array(
                            'id' => 'float_on',
                            'value' => 1,
                            'label' => $this->l('Inline'),
                        ),
                        array(
                            'id' => 'float_off',
                            'value' => 0,
                            'label' => $this->l('Vertical'),
                        ),
                    ),
                ),
            ),
            'submit' => array(
                'title' => $this->l('Save'),
                'class' => 'button'
            )
        );

        $helper = new HelperForm();

        // Module, token and currentIndex
        $helper->module = $this;
        $helper->name_controller = $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex . '&configure=' . $this->name;

        // Language
        $helper->default_form_language = $default_lang;
        $helper->allow_employee_form_lang = $default_lang;

        // Title and toolbar
        $helper->title = $this->displayName;
        $helper->show_toolbar = true;        // false -> remove toolbar
        $helper->toolbar_scroll = true;      // yes - > Toolbar is always visible on the top of the screen.
        $helper->submit_action = 'submit' . $this->name;
        $helper->toolbar_btn = array(
            'save' =>
            array(
                'desc' => $this->l('Save'),
                'href' => AdminController::$currentIndex . '&configure=' . $this->name . '&save' . $this->name .
                '&token=' . Tools::getAdminTokenLite('AdminModules'),
            ),
            'back' => array(
                'href' => AdminController::$currentIndex . '&token=' . Tools::getAdminTokenLite('AdminModules'),
                'desc' => $this->l('Back to list')
            )
        );

        // Load current value
        $helper->fields_value['ECO_PRESSE_WIDTH_IMG'] = Configuration::get('ECO_PRESSE_WIDTH_IMG');
        $helper->fields_value['ECO_PRESSE_HEIGHT_IMG'] = Configuration::get('ECO_PRESSE_HEIGHT_IMG');
        $helper->fields_value['ECO_PRESSE_TITLE'] = Configuration::get('ECO_PRESSE_TITLE');
        $helper->fields_value['ECO_PRESSE_DISPLAY_LEGEND'] = Configuration::get('ECO_PRESSE_DISPLAY_LEGEND');
        $helper->fields_value['ECO_PRESSE_FLOAT'] = Configuration::get('ECO_PRESSE_FLOAT');

        return $helper->generateForm($fields_form);
    }

    public function createDb() {
        $prefix = _DB_PREFIX_;
        $sql = "
			CREATE TABLE IF NOT EXISTS `${prefix}eco_galerie` (
				`id_article` int(11) NOT NULL AUTO_INCREMENT,
				`name_article` VARCHAR(255) NOT NULL,
				`desc_article` VARCHAR(255) NOT NULL,
				`date_article` DATE NOT NULL,
				`path_article` VARCHAR(255) NOT NULL,
				`path_mini` VARCHAR(255) NOT NULL,
				PRIMARY KEY (`id_article`)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;
				";
        if (Db::getInstance()->Execute($sql))
            return true;
    }

// Fonction pour mon slider --------------------------------------------------------------------------------------
    public function createDb2() {
        $prefix = _DB_PREFIX_;
        $sql = "
			CREATE TABLE IF NOT EXISTS `${prefix}eco_galerieslider` (
				`id_article` int(11) NOT NULL AUTO_INCREMENT,
				`name_article` VARCHAR(255) NOT NULL,
				`desc_article` VARCHAR(255) NOT NULL,
				`date_article` DATE NOT NULL,
				`path_article` VARCHAR(255) NOT NULL,
				`path_mini` VARCHAR(255) NOT NULL,
				PRIMARY KEY (`id_article`)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;
				";
        if (Db::getInstance()->Execute($sql))
            return true;
    }

// Fin de fonction slider ------------------------------------------------------------------------------------------

    public function hookDisplayFooter($params) {
        $params;
        $this->context->smarty->assign(
                array(
                    'eco_galerie_link' => $this->context->link->getModuleLink('eco_galerie', 'display'),
                    'eco_galerieSlider_link' => $this->context->link->getModuleLink('eco_galerie', 'displaySlider'),
                )
        );
        return $this->display(__FILE__, 'eco_galerie.tpl');
    }

    public function hookDisplayHeader($params) {
        $params;
        $context = Context::getContext();
        if (isset($context->controller->module) && isset($context->controller->module->name) && $context->controller->module->name == 'ecogalerie') {
            $this->context->controller->addjqueryPlugin('bxslider');

            $this->context->controller->addJS($this->_path . 'js/eco_galerie.js');
            $this->context->controller->addJS($this->_path . 'js/homeslider.js');
            $this->context->controller->addjqueryPlugin('fancybox');

            $this->context->controller->addCSS($this->_path . 'css/multiblock.css', 'all');
            $this->context->controller->addCSS($this->_path . 'css/homeslider.css', 'all');

//            Tools::addCSS(_MODULE_DIR_ . '/ecogalerie/css/multiblock.css', 'all');
//            Tools::addCSS(_MODULE_DIR_ . '/ecogalerie/css/homeslider.css', 'all');
        }
    }

}
