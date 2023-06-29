<?php

namespace App\Jongman\Contracts;

use App\Jongman\Contracts\LayoutFactoryInterface;

interface ScheduleRepositoryInterface
{
	public function loadById($scheduleId);
	
	public function getAll();
	
	public function getLayout($scheduleId, LayoutFactoryInterface $layoutFactory);
}