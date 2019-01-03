<?php
namespace app\common\service;

/**
 * Interface ApplicationServiceInterface
 * @package Common\Service
 * @copyright 2012-2019 Medkey
 */
interface ApplicationServiceInterface
{
    public function setProprietary($obj);
    public function getProprietary();
    public function isAllowed($privilege, $proprietary = null);
    public function getResourceId();
    public function aclAlias();
    public function getPrivileges();
}
