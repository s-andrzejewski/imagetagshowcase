<?php
/**
* 2007-2023 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2023 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_PS_VERSION_')) {
    exit;
}

class ImageTagShowcase extends Module
{
    protected $config_form = false;

    public function __construct()
    {
        $this->name = 'imagetagshowcase';
        $this->tab = 'front_office_features';
        $this->version = '1.0.1';
        $this->author = 'Szymon Andrzejewski';
        $this->need_instance = 0;

        /**
         * Set $this->bootstrap to true if your module is compliant with bootstrap (PrestaShop 1.6)
         */
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('ImageTag Showcase');
        $this->description = $this->l('A module which allows you to tag products on a photo.');

        $this->confirmUninstall = $this->l('Are you sure you want to uninstall this module?');

        $this->ps_versions_compliancy = array('min' => '1.7', 'max' => _PS_VERSION_);
    }

    /**
     * Don't forget to create update methods if needed:
     * http://doc.prestashop.com/display/PS16/Enabling+the+Auto-Update
     */
    public function install()
    {
        Configuration::updateValue('IMAGETAGSHOWCASE_HEADER', null);
        Configuration::updateValue('IMAGETAGSHOWCASE_DESCRIPTION', null);
        Configuration::updateValue('IMAGETAGSHOWCASE_IMAGE', null);
        Configuration::updateValue('IMAGETAGSHOWCASE_POINT_BTN', null);

        return parent::install() &&
            $this->registerHook('header') &&
            $this->registerHook('displayBackOfficeHeader') &&
            $this->registerHook('displayHome');
    }

    public function uninstall()
    {
        Configuration::deleteByName('IMAGETAGSHOWCASE_HEADER');
        Configuration::deleteByName('IMAGETAGSHOWCASE_DESCRIPTION');
        Configuration::deleteByName('IMAGETAGSHOWCASE_IMAGE');
        Configuration::deleteByName('IMAGETAGSHOWCASE_POINT_BTN');

        return parent::uninstall();
    }

    /**
     * Load the configuration form
     */
    public function getContent()
    {
        /**
         * If values have been submitted in the form, process.
         */
        
        // $output = null;

        if (((bool)Tools::isSubmit('submitImageTagShowcase')) == true) {
            $this->postProcess();
            // Handle form submission, update configuration values
            // $output .= $this->displayConfirmation($this->l('Settings updated'));
        }

        $this->context->smarty->assign('module_dir', $this->_path);

        $output = $this->context->smarty->fetch($this->local_path.'views/templates/admin/configure.tpl');

        return $output.$this->renderForm();
    }

    /**
     * Create the form that will be displayed in the configuration of your module.
     */
    protected function renderForm()
    {
        $helper = new HelperForm();

        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);

        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitImageTagShowcase';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
            .'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');

        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFormValues(), /* Add values for your inputs */
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        );

        return $helper->generateForm(array($this->getConfigForm()));
    }

    /**
     * Create the structure of your form.
     */
    protected function getConfigForm()
    {
        $preview_image_url = Configuration::get('IMAGETAGSHOWCASE_IMAGE', null);
        $preview_image = '<div class="col-lg-6"><img src="' . $preview_image_url . '" class="img-thumbnail" width="400"></div>';

        return array(
            'form' => array(
                'legend' => array(
                'title' => $this->l('Settings'),
                'icon' => 'icon-cogs',
                ),
                'input' => array(
                    array(
                        'col' => 2,
                        'type' => 'text',
                        'prefix' => '<i class="fa-solid fa-heading"></i>',
                        'desc' => $this->l('Enter a heading text for the message.'),
                        'name' => 'IMAGETAGSHOWCASE_HEADER',
                        'label' => $this->l('Heading'),
                    ),
                    array(
                        'row' => 5,
                        'type' => 'textarea',
                        'prefix' => '<i class="fa-solid fa-pen"></i>',
                        'desc' => $this->l('Enter banner\'s content.'),
                        'name' => 'IMAGETAGSHOWCASE_DESCRIPTION',
                        'label' => $this->l('Description'),
                        'autoload_rte' => true,
                    ),
                    array(
                        'type' => 'file',
                        'label' => $this->l('Upload image'),
                        'name' => 'IMAGETAGSHOWCASE_IMAGE',
                        'desc' => $this->l('Upload an image for the custom banner.'),
                        'display_image' => true,
                        'image' => $preview_image
                    ),
                ),
                'buttons' => array(
                    array(
                        //'href' => '//url',            // If this is set, the button will be an <a> tag
                        'js'   => 'drawNewPoint()',     // Javascript to execute on click
                        //'class' => '',                  // CSS class to add
                        'type' => 'button',             // Button type
                        'id'   => 'draw-new-point-btn',
                        'name' => 'IMAGETAGSHOWCASE_POINT_BTN',           // If not defined, this will take the value of "submitOptions{$table}"
                        'icon' => 'icon-foo',           // Icon to show, if any
                        'title' => $this->l('Draw a new point on an image'),
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                ),
            ),
        );
    }

    /**
     * Set values for the inputs.
     */
    protected function getConfigFormValues()
    {
        return array(
            'IMAGETAGSHOWCASE_HEADER' => Configuration::get('IMAGETAGSHOWCASE_HEADER', null),
            'IMAGETAGSHOWCASE_DESCRIPTION' => Configuration::get('IMAGETAGSHOWCASE_DESCRIPTION', null),
            'IMAGETAGSHOWCASE_IMAGE' => Configuration::get('IMAGETAGSHOWCASE_IMAGE', null),
            'IMAGETAGSHOWCASE_POINT_BTN' => Configuration::get('IMAGETAGSHOWCASE_POINT_BTN', null),
        );
    }

    /**
     * Save form data.
     */
    protected function postProcess()
    {
        $form_values = $this->getConfigFormValues();
    
        foreach (array_keys($form_values) as $key) {
            if ($key === 'IMAGETAGSHOWCASE_IMAGE') {
                // Handle image upload and save the file path
                if (!empty($_FILES[$key]['name'])) {
                    $image_path = $this->uploadImage($_FILES[$key]);
                    Configuration::updateValue($key, $image_path);
                }
            } else {
                Configuration::updateValue($key, Tools::getValue($key));
            }
        }
    }

    private function uploadImage($file)
    {
        // Define the target directory where images will be uploaded
        $upload_dir = _PS_MODULE_DIR_ . $this->name . '/views/img/';
    
        // Generate a unique filename for the uploaded image
        $file_name = uniqid() . '_' . $file['name'];
    
        // Set the full path to the uploaded image
        $target_file = $upload_dir . $file_name;
    
        // Check if the file was uploaded successfully
        if (move_uploaded_file($file['tmp_name'], $target_file)) {
            return $this->_path . 'views/img/' . $file_name;
        }
    
        return null;
    }

    /**
    * Add the CSS & JavaScript files you want to be loaded in the BO.
    */
    public function hookDisplayBackOfficeHeader()
    {
        if (Tools::getValue('configure') == $this->name) {
            $this->context->controller->addJS($this->_path.'views/js/back.js');
            $this->context->controller->addCSS($this->_path.'views/css/back.css');
        }
    }

    /**
     * Add the CSS & JavaScript files you want to be added on the FO.
     */
    public function hookHeader()
    {
        $this->context->controller->addJS($this->_path.'/views/js/front.js');
        $this->context->controller->addCSS($this->_path.'/views/css/front.css');
    }

    public function hookDisplayHome()
    {
        /* Place your code here. */
    }
}
