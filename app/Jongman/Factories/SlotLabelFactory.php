<?php

namespace App\Jongman\Factories;

use App\Jongman\Reservations\ReservationItem;

class SlotLabelFactory
{
	/**
	 * @static
	 * @param ReservationItemView $reservation
	 * @return string
	 */
	public static function create(ReservationItem $reservation)
	{
		$f = new SlotLabelFactory();
		return $f->format($reservation);
	}

	/**
	 * @param ReservationItemView $reservation
	 * @return string
	 */
	public function format(ReservationItem $reservation)
	{
		$property = config('jongman.reservation.bar_display');

		$name = $this->getFullName($reservation);

		if ($property == 'title_or_user')
		{
			if (strlen($reservation->title))
			{
				return $reservation->title;
			}
			else
			{
				return $name;
			}
		}
		if ($property == 'title')
		{
			return $reservation->title;
		}
		if ($property == 'none' || empty($property))
		{
			return '';
		}
		if ($property == 'name' || $property == 'user')
		{
			return $name;
		}
		
		if ($property == 'user_and_title') {
			$property = '{name}@{title}';			
		}

		$label = $property;
		$label = str_replace('{name}', $name, $label);
		$label = str_replace('{title}', $reservation->title, $label);
		$label = str_replace('{description}', $reservation->description, $label);

		return $label;
	}

	protected function getFullName(ReservationItem $reservation)
	{
		$shouldHide = config('privateReservation', true);
        
		if ($shouldHide)
		{
			return __('jongman.private');
		}

		$name = $reservation->fullName;

		return $name;

	}
}
