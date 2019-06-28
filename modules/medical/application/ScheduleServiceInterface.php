<?php
namespace app\modules\medical\application;

interface ScheduleServiceInterface
{
    public function getSchedule(string $date, array $specialityId = [], array $serviceIds = []);
}
