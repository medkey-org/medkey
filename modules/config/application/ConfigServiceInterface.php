<?php
namespace app\modules\config\application;

interface ConfigServiceInterface
{
    public function getAllSettings();
    public function saveSettings($form);
    public function getApplicationTitle();
}
