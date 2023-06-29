<?php

namespace App\Jongman\Contracts;

interface LayoutFactoryInterface
{
	/**
	 * @return ScheduleLayoutInterface
	 */
	public function createLayout();
}