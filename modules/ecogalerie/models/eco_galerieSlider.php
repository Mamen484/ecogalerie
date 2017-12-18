<?php

/**
 * 2010-2014 EcomiZ
 *
 *  @author    EcomiZ
 *  @copyright 2010-2014
 *  @version  Release: 1.1 $Revision: 1 $
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */
class EcoGalerieSliderModel extends ObjectModel {

    public $id_article;
    public $name_article;
    public $path_article;
    public $date_article;
    public $desc_article;
    public static $definition = array(
        'table' => 'eco_galerieslider',
        'primary' => 'id_article',
        'multilang' => false,
        'fields' => array(
            'id_article' => array(
                'type' => ObjectModel::TYPE_INT
            ),
            'name_article' => array(
                'type' => ObjectModel::TYPE_STRING,
                'required' => true
            ),
            'desc_article' => array(
                'type' => ObjectModel::TYPE_STRING,
                'required' => true
            ),
            'date_article' => array(
                'type' => ObjectModel::TYPE_DATE,
                'required' => true
            ),
            'path_article' => array(
                'type' => ObjectModel::TYPE_STRING,
                'required' => true
            ),
            'path_mini' => array(
                'type' => ObjectModel::TYPE_STRING,
                'required' => true
            ),
        )
    );

    public static function getArticleInfos($id_article) {
        $sql = '
					SELECT ep.*
					FROM ' . _DB_PREFIX_ . 'eco_galerieslider ep
					WHERE ep.id_article = ' . pSQL($id_article);
        return Db::getInstance()->ExecuteS($sql);
    }

    public static function updateArticle($titre, $desc, $date, $path, $path_mini, $id_article) {
        Db::getInstance()->Execute('UPDATE ' . _DB_PREFIX_ . 'eco_galerieslider
				SET name_article = "' . $titre . '",
					desc_article = "' . $desc . '",
					date_article = "' . $date . '",
					path_article = "' . $path . '",
					path_mini = "' . $path_mini . '"
				WHERE id_article = ' . (int) pSQL($id_article));
    }

}
