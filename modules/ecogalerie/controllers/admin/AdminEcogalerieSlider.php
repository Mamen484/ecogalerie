<?php

/**
 * 2010-2014 EcomiZ
 *
 *  @author    EcomiZ
 *  @copyright 2010-2014
 *  @version  Release: 1.1 $Revision: 1 $
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */
class AdminEcoGalerieSliderController extends ModuleAdminController {

    public function __construct() {
        $this->bootstrap = true;
        $this->table = 'eco_galerieslider';
        $this->className = 'EcoGalerieSliderModel';
        $this->identifier = 'id_article';
        $this->lang = false;
        $this->required_database = true;
        $this->addRowAction('edit');
        $this->addRowAction('delete');
        $this->context = Context::getContext();

        parent::__construct(); /* affichage du tableau bo */
        $this->fields_list = array(
            'id_article' => array(
                'title' => 'ID',
                'align' => 'center',
                'width' => 25
            ),
            'name_article' => array(
                'title' => 'Titre de l\'article',
                'align' => 'center',
                'width' => 100,
            ),
            'desc_article' => array(
                'title' => 'Description',
                'align' => 'center',
                'width' => 100,
            ),
            'date_article' => array(
                'title' => 'Date de l\'article',
                'align' => 'center',
                'width' => 100,
                'id' => 'datepicker',
            ),
            'path_mini' => array(
                'title' => 'Image',
                'align' => 'center',
                'width' => 100,
                'havingFilter' => true,
                'callback' => 'getStickerLogo',
            )
        );
    }

    public function getStickerLogo($a) /* affichage de l'image du sticker */ {
        $html = '<img src="' . $a . '" style="width:100px; height:140px;" />';
        return $html;
    }

    public function renderForm() {
        $this->fields_form = array(
            'legend' => array(
                'title' => $this->module->l('article'),
                'image' => __PS_BASE_URI__ . 'modules/ecogalerie/logo.gif'
            ),
            'input' => array(
                array(
                    'type' => 'text',
                    'label' => $this->module->l('Titre de l\'article'),
                    'name' => 'name_article',
                    'class' => 'ac_input',
                    'size' => 30,
                    'maxlength' => 32,
                    'required' => true
                ),
                array(
                    'type' => 'textarea',
                    'label' => $this->module->l('Description'),
                    'name' => 'desc_article',
                    'class' => 'ac_input',
                    'size' => 30,
                    'rows' => 5,
                    'cols' => 40,
                    'required' => true
                ),
                array(
                    'type' => 'date',
                    'label' => $this->module->l('Date'),
                    'name' => 'date_article',
                    'size' => 30,
                    'hint' => 'AAAA-MM-JJ',
                    'required' => true
                ),
                array(
                    'type' => 'file',
                    'label' => $this->module->l('Image'),
                    'name' => 'path_article',
                    'required' => true,
                    'display_image' => true
                ),
            ),
            'submit' => array(
                'title' => $this->module->l('Save'),
                'name' => 'submitArticle'
            )
        );

        $this->fields_value = array('date_article' => date('Y-m-d'));

        return parent::renderForm();
    }

    public function processSave() {
        if (Tools::isSubmit('submitArticle') && (Tools::getValue('id_article'))) {
            $article = new EcoGalerieSliderModel(Tools::getValue('id_article'));
            $article->id_article = Tools::getValue('id_article');
            $infos_article = EcoGalerieSliderModel::getArticleInfos($article->id_article);

            $article->path_article = $infos_article[0]['path_article'];
            $article->path_mini = $infos_article[0]['path_mini'];
            $article->name_article = Tools::getValue('name_article');
            $article->desc_article = Tools::getValue('desc_article');
            $article->date_article = Tools::getValue('date_article');
            if (!empty($_FILES['path_article']['name'])) {
                $infos_image_article = pathinfo($_FILES['path_article']['name']);
                $extension = $infos_image_article['extension'];
                $name_image = $infos_image_article['filename'];
                $article->path_article = Configuration::get('ECO_PATH_ARTICLE') . $name_image . '-' . $article->date_article . '.' . $extension;
                copy($_FILES['path_article']['tmp_name'], $article->path_article);
                ImageManager::resize($article->path_article, Configuration::get('ECO_PATH_ARTICLE') . $name_image . '-' . $article->date_article . '-miniature.' . $extension, Configuration::get('ECO_PRESSE_WIDTH_IMG'), Configuration::get('ECO_PRESSE_HEIGHT_IMG'));
                $article->path_mini = __PS_BASE_URI__ . '/modules/ecogalerie/img/' . $name_image . '-' . $article->date_article . '-miniature.' . $extension;
                $article->path_article = __PS_BASE_URI__ . '/modules/ecogalerie/img/' . $name_image . '-' . $article->date_article . '.' . $extension;
            }
            EcoGalerieSliderModel::updateArticle($article->name_article, $article->desc_article, $article->date_article, $article->path_article, $article->path_mini, $article->id_article);
        }
        if (Tools::isSubmit('submitArticle') && !(Tools::getValue('id_article'))) {
            $article = new EcoGalerieSliderModel();
            $article->name_article = Tools::getValue('name_article');
            $article->desc_article = Tools::getValue('desc_article');
            $article->date_article = Tools::getValue('date_article');

            $infos_image_article = pathinfo($_FILES['path_article']['name']);
            $extension = $infos_image_article['extension'];
            $name_image = $infos_image_article['filename'];
            $article->path_article = Configuration::get('ECO_PATH_ARTICLE') . $name_image . '-' . $article->date_article . '.' . $extension;
            $extension_valide = array('jpg', 'JPG', 'png', 'PNG');
            if (($_FILES['path_article']['size'] > 99999999999) || (!in_array($extension, $extension_valide)))
                return $this->displayWarning($this->l('image .jpg uniquement et < 2 mo'));
            else {
                $name_image = $infos_image_article['filename'];
                copy($_FILES['path_article']['tmp_name'], $article->path_article);
                /* if(!copy($_FILES['path_article']['tmp_name'], $article->path_article))
                  {
                  $errors= error_get_last();
                  ppp($errors['type']);
                  ppp($errors['message']);
                  exit;
                  } */
                ImageManager::resize($article->path_article, Configuration::get('ECO_PATH_ARTICLE') . $name_image . '-' . $article->date_article . '-miniature.' . $extension, Configuration::get('ECO_PRESSE_WIDTH_IMG'), Configuration::get('ECO_PRESSE_HEIGHT_IMG'));
                $article->path_mini = __PS_BASE_URI__ . '/modules/ecogalerie/img/' . $name_image . '-' . $article->date_article . '-miniature.' . $extension;
                $article->path_article = __PS_BASE_URI__ . '/modules/ecogalerie/img/' . $name_image . '-' . $article->date_article . '.' . $extension;
                $article->save();
                $article->id_article = Db::getInstance()->Insert_ID();
            }
        }
    }

}
