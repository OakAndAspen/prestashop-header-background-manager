<?php

use PrestaShop\PrestaShop\Core\Module\WidgetInterface;

if (!defined('_PS_VERSION_')) {
    exit;
}

class HeaderBackgroundManager extends Module implements WidgetInterface
{
    const CONFIG_TEMPLATE = './views/templates/admin/config.tpl';
    const HEADER_TEMPLATE = './views/templates/hook/header.tpl';

    public function __construct()
    {
        $this->name = 'headerbackgroundmanager';
        $this->author = 'Irina Despot';
        $this->tab = 'administration';
        $this->version = '1.0.0';
        $this->bootstrap = true;

        parent::__construct();
        $this->displayName = 'Header Background Manager';
        $this->description = 'Allows you to change the image that\'s used for the header background.';
        $this->ps_versions_compliancy = ['min' => '1.7', 'max' => _PS_VERSION_];
    }

    // Display the configuration page
    public function getContent()
    {
        $result = null;

        if (Tools::isSubmit("imageFile")) {
            $result = $this->uploadImage();
        }

        $this->smarty->assign([
            "result" => $result,
            "form" => $this->renderForm()
        ]);
        return $this->display(__FILE__, self::CONFIG_TEMPLATE);
    }

    // Render the form
    private function renderForm()
    {
        $fields_form = [
            'form' => [
                'legend' => [
                    'title' => 'Télécharger une nouvelle image',
                    'icon' => 'icon-cogs'
                ],
                'input' => [
                    [
                        'type' => 'file',
                        'label' => 'Choisir une image',
                        'name' => 'imageFile',
                        'desc' => 'Choisir une image',
                        'lang' => true
                    ]
                ],
                'submit' => [
                    'title' => 'Mettre à jour'
                ]
            ]
        ];

        $helper = new HelperForm();
        $helper->module = $this;
        $helper->token = Tools::getAdminTokenLite('AdminModules');

        return $helper->generateForm([$fields_form]);
    }

    // Upload the image
    private function uploadImage()
    {
        $result = [
            "message" => "Le fichier a bien été téléchargé. Ne pas oublier de vider le cache du site pour que les modifications s'appliquent!",
            "messageType" => "success",
            "targetFile" => null
        ];

        $originalFileName = $_FILES["imageFile"]["name"];
        $fileType = pathinfo($originalFileName, PATHINFO_EXTENSION);

        // Check the file format
        if ($fileType != "jpg" && $fileType != "png" && $fileType != "jpeg") {
            $result["message"] = "Le format de l'image doit être JPG, JPEG ou PNG.";
            $result["messageType"] = "danger";
            return $result;
        }

        $targetDir = _PS_UPLOAD_DIR_;
        $newFileName = date("Y-m-d-H-i-s") . "_header-background." . $fileType;
        $targetFile = $targetDir . $newFileName;

        // Try to upload the file
        if (move_uploaded_file($_FILES["imageFile"]["tmp_name"], $targetFile)) {
            $result["targetFile"] = $targetFile;
            Configuration::updateValue("OAA_HEADER_BANNER", $newFileName);
        } else {
            $result["message"] = "Il y a eu une erreur pendant le téléchargement.";
            $result["messageType"] = "danger";
        }

        return $result;
    }

    // Affiche le style qui définit le lien de l'image
    public function renderWidget($hookName, array $configuration) {

        $this->smarty->assign([
            "filename" => Configuration::get("OAA_HEADER_BANNER")
        ]);

        return $this->display(__FILE__, self::HEADER_TEMPLATE);
    }

    public function getWidgetVariables($hookName, array $configuration) {

    }
}
