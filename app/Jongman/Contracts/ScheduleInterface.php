<?php

namespace App\Jongman\Contracts;

interface ScheduleInterface
{
	public function getId();
	public function getName();
	public function getIsDefault();
	public function getWeekdayStart();
	public function getDaysVisible();
	public function getTimezone();
	public function getLayoutId();
	public function getIsCalendarSubscriptionAllowed();
	public function getPublicId();
	public function getAdminGroupId();
}