<?php
namespace app\modules\medical\application;

interface ScheduleServiceInterface
{
    public function getSchedule(string $date, string $specialityId = null, string $serviceId = null);
}
