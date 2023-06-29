<?php

namespace App\Jongman\Schedules;

use App\Jongman\Contracts\ScheduleInterface;

class Schedule implements ScheduleInterface
{
	protected $_id;
	protected $_name;
	protected $_isDefault;
	protected $_weekdayStart;
	protected $_daysVisible;
	protected $_timezone;
	protected $_layoutId;
	protected $_isCalendarSubscriptionAllowed = false;
	protected $_publicId;
	protected $_adminGroupId;

	const TODAY = 100;

	public function __construct(
			$id,
			$name,
			$isDefault,
			$weekdayStart,
			$daysVisible,
			$timezone = null,
			$layoutId = null)
	{
		$this->_id = $id;
		$this->_name = $name;
		$this->_isDefault = $isDefault;
		$this->_weekdayStart = $weekdayStart;
		$this->_daysVisible = $daysVisible;
		$this->_timezone = $timezone;
		$this->_layoutId = $layoutId;
	}

	public function getId()
	{
		return $this->_id;
	}

	public function setId($value)
	{
		$this->_id = $value;
	}

	public function getName()
	{
		return $this->_name;
	}

	public function setName($value)
	{
		$this->_name = $value;
	}

	public function getIsDefault()
	{
		return $this->_isDefault;
	}

	public function setIsDefault($value)
	{
		$this->_isDefault = $value;
	}

	public function getWeekdayStart()
	{
		return $this->_weekdayStart;
	}

	public function setWeekdayStart($value)
	{
		$this->_weekdayStart = $value;
	}

	public function getDaysVisible()
	{
		return $this->_daysVisible;
	}

	public function setDaysVisible($value)
	{
		$this->_daysVisible = $value;
	}

	public function getTimezone()
	{
		return $this->_timezone;
	}

	public function getLayoutId()
	{
		return $this->_layoutId;
	}

	public function setTimezone($timezone)
	{
		$this->_timezone = $timezone;
	}

	protected function setIsCalendarSubscriptionAllowed($isAllowed)
	{
		$this->_isCalendarSubscriptionAllowed = $isAllowed;
	}

	public function getIsCalendarSubscriptionAllowed()
	{
		return (bool)$this->_isCalendarSubscriptionAllowed;
	}

	protected function setPublicId($publicId)
	{
		$this->_publicId = $publicId;
	}

	public function getPublicId()
	{
		return $this->_publicId;
	}

	public function enableSubscription()
	{
		$this->setIsCalendarSubscriptionAllowed(true);
		if (empty($this->_publicId))
		{
			$this->setPublicId(uniqid());
		}
	}

	public function disableSubscription()
	{
		$this->setIsCalendarSubscriptionAllowed(false);
	}

	/**
	 * @param int|null $adminGroupId
	 */
	public function setAdminGroupId($adminGroupId)
	{
		$this->_adminGroupId = $adminGroupId;
	}

	/**
	 * @return int|null
	 */
	public function getAdminGroupId()
	{
		return $this->_adminGroupId;
	}

	/**
	 * @return bool
	 */
	public function hasAdminGroup()
	{
		return !empty($this->_adminGroupId);
	}

	/**
	 * @static
	 * @return Schedule
	 */
	public static function null()
	{
		return new Schedule(null, null, false, null, null);
	}

	/**
	 * @static
	 * @param object $row
	 * @return Schedule
	 */
	public static function fromRow($row)
	{
		$schedule = new Schedule(
				$row->id,
				$row->name,
				$row->default,
				$row->weekday_start,
				$row->days_visbile,
				$row->timezone,
				$row->schedule_layout_id
			);
		
		if (isset($row->allow_calendar_subscription)) $schedule->withSubscription($row->allow_calendar_subscription);
		if (isset($row->slug)) $schedule->withPublicId($row->slug);
		if (isset($row->admin_group_id)) $schedule->setAdminGroupId($row->admin_group_id);

		return $schedule;
	}

	/**
	 * @param bool $allowSubscription
	 * @internal
	 */
	public function withSubscription($allowSubscription)
	{
		$this->setIsCalendarSubscriptionAllowed($allowSubscription);
	}

	/**
	 * @param string $publicId
	 * @internal
	 */
	public function withPublicId($publicId)
	{
		$this->setPublicId($publicId);
	}

	public function getSubscriptionUrl()
	{
		return ''; //new calendarSubscriptionUrl(null, $this->getPublicId(), null);
	}
}

