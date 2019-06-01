<?php
namespace app\modules\config\application;

use app\common\data\ActiveDataProvider;
use app\common\service\ApplicationService;
use app\common\service\exception\AccessApplicationServiceException;
use app\modules\config\ConfigModule;
use app\modules\config\models\orm\DocumentTemplate;

/**
 * Class DocumentService
 * @package Module\Config
 * @copyright 2012-2019 Medkey
 */
class DocumentService extends ApplicationService implements DocumentServiceInterface
{
    public function getTemplateList()
    {
        if (!$this->isAllowed('getTemplateList')) {
            throw new AccessApplicationServiceException(ConfigModule::t('document', 'Access to document template list is restricted'));
        }
        $query = DocumentTemplate::find();
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    public function getTemplate()
    {

    }

    public function createTemplate()
    {

    }

    public function updateTemplate()
    {

    }

    public function renderDocument()
    {

    }

    /**
     * @inheritdoc
     */
    public function getPrivileges()
    {
        return [
            'getTemplateList' => ConfigModule::t('document', 'Get document template list'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function aclAlias()
    {
        return ConfigModule::t('document', 'Document');
    }
}
