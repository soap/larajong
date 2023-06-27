<?php

namespace App\Jongman;

use App\Jongman\Contracts\LayoutScheduleInterface;
use App\Jongman\Contracts\ScheduleReservationListInterface;
use App\Jongman\Helpers\LayoutIndexCache;
use App\Jongman\Reservations\ReservationListItem;
use App\Jongman\Reservations\ReservationSlotEmpty;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class ScheduleReservationList implements ScheduleReservationListInterface
{
    /**
     * @var array|ReservationListItem[]
     */
    private $_items;

    /**
     * @var ScheduleLayoutInterface
     */
    private $_layout;

    /**
     * @var Carbon
     */
    private $_layoutDateStart;

    /**
     * @var Carbon
     */
    private $_layoutDateEnd;

    /**
     * @var array|SchedulePeriod[]
     */
    private $_layoutItems;

    private $_itemsByStartTime = [];

    /**
     * @var array|SchedulePeriod[]
     */
    private $_layoutByStartTime = [];

    /**
     * @var array|int[]
     */
    private $_layoutIndexByEndTime = [];

    /**
     * @var Time
     */
    private $_midnight;

    /**
     * @var string
     */
    private $_destinationTimezone;

    /**
     * @var Carbon
     */
    private $_firstLayoutTime;

    /**
     * @param  array|ReservationListItem[]  $items
     * @param  ScheduleLayoutInterface  $layout
     * @param  bool  $hideBlockedPeriods
     */
    public function __construct($items, LayoutScheduleInterface $layout, Carbon $layoutDate, $hideBlockedPeriods = false)
    {
        $this->_items = $items;
        $this->_layout = $layout;
        $this->_destinationTimezone = $this->_layout->timezone();
        $this->_layoutDateStart = $layoutDate->toTimezone($this->_destinationTimezone)->getDate();
        $this->_layoutDateEnd = $this->_layoutDateStart->addDays(1);
        $this->_layoutItems = $this->_layout->getLayout($layoutDate, $hideBlockedPeriods); //daily layout
        $this->_midnight = new Time(0, 0, 0, $this->_destinationTimezone);

        $this->indexLayout();
        $this->indexItems();
    }

    public function buildSlots()
    {
        $slots = [];

        for ($currentIndex = 0; $currentIndex < count($this->_layoutItems); $currentIndex++) {
            $layoutItem = $this->_layoutItems[$currentIndex];
            $item = $this->getItemStartingAt($layoutItem->beginDate());

            if ($item != null) {
                if ($this->itemEndsOnFutureDate($item)) {
                    $endTime = $this->_layoutDateEnd;
                } else {
                    $endTime = $item->endDate()->toTimezone($this->_destinationTimezone);
                }

                $endingPeriodIndex = max($this->getLayoutIndexEndingAt($endTime), $currentIndex);

                $span = ($endingPeriodIndex - $currentIndex) + 1;

                $slots[] = $item->buildSlot($layoutItem, $this->_layoutItems[$endingPeriodIndex],
                    $this->_layoutDateStart, $span);

                $currentIndex = $endingPeriodIndex;
            } else {
                $slots[] = new ReservationSlotEmpty($layoutItem, $layoutItem, $this->_layoutDateStart, $layoutItem->isReservable());
            }
        }

        return $slots;
    }

    private function indexItems()
    {
        foreach ($this->_items as $item) {
            if ($item->endDate()->toTimezone($this->_destinationTimezone)->equals($this->_firstLayoutTime)) {
                continue;
            }

            $start = $item->startDate()->toTimezone($this->_destinationTimezone);

            $startsInPast = $this->itemStartsOnPastDate($item);
            if ($startsInPast) {
                $start = $this->_firstLayoutTime;
            } elseif ($this->itemIsNotOnLayoutBoundary($item)) {
                $layoutItem = $this->findClosestLayoutIndexBeforeStartingTime($item);
                if (! empty($layoutItem)) {
                    $start = $layoutItem->beginDate()->toTimezone($this->_destinationTimezone);
                }
            }

            $this->_itemsByStartTime[$start->timestamp()] = $item;
        }
    }

    private function itemStartsOnPastDate(ReservationListItem $item)
    {
        return $item->startDate()->lessThan($this->_layoutDateStart);
    }

    private function itemEndsOnFutureDate(ReservationListItem $item)
    {
        //Log::Debug("%s %s %s", $reservation->GetReferenceNumber(), $reservation->GetEndDate()->GetDate(), $this->_layoutDateEnd->GetDate());
        return $item->endDate()->compare($this->_layoutDateEnd) >= 0;
    }

    private function indexLayout()
    {
        if (! LayoutIndexCache::contains($this->_layoutDateStart)) {
            LayoutIndexCache::add($this->_layoutDateStart, $this->_layoutItems, $this->_layoutDateStart,
                $this->_layoutDateEnd);
        }
        $cachedIndex = LayoutIndexCache::get($this->_layoutDateStart);
        $this->_firstLayoutTime = $cachedIndex->getFirstLayoutTime();
        $this->_layoutByStartTime = $cachedIndex->layoutByStartTime();
        $this->_layoutIndexByEndTime = $cachedIndex->layoutIndexByEndTime();
    }

    /**
     * @return int index of $_layoutItems which has the corresponding $endingTime
     */
    private function getLayoutIndexEndingAt(Carbon $endingTime)
    {
        $timeKey = $endingTime->timestamp;

        if (array_key_exists($timeKey, $this->_layoutIndexByEndTime)) {
            return $this->_layoutIndexByEndTime[$timeKey];
        }

        return $this->findClosestLayoutIndexBeforeEndingTime($endingTime);
    }

    /**
     * @return ReservationListItem
     */
    private function getItemStartingAt(Carbon $beginTime)
    {
        $timeKey = $beginTime->timestamp;
        if (array_key_exists($timeKey, $this->_itemsByStartTime)) {
            return $this->_itemsByStartTime[$timeKey];
        }

        return null;
    }

    /**
     * @return int index of $_layoutItems which has the closest ending time to $endingTime without going past it
     */
    private function findClosestLayoutIndexBeforeEndingTime(Carbon $endingTime)
    {
        for ($i = count($this->_layoutItems) - 1; $i >= 0; $i--) {
            $currentItem = $this->_layoutItems[$i];

            if ($currentItem->beginDate()->lessThan($endingTime)) {
                return $i;
            }
        }

        return 0;
    }

    /**
     * @return SchedulePeriod which has the closest starting time to $endingTime without going prior to it
     */
    private function findClosestLayoutIndexBeforeStartingTime(ReservationListItem $item)
    {
        for ($i = count($this->_layoutItems) - 1; $i >= 0; $i--) {
            $currentItem = $this->_layoutItems[$i];

            if ($currentItem->beginDate()->lessThan($item->startDate())) {
                return $currentItem;
            }
        }

        Log::Error('Could not find a fitting starting slot for reservation. Id %s, ResourceId: %s, Start: %s, End: %s',
            $item->id(), $item->resourceId(), $item->startDate()->toString(), $item->endDate()->toString());

        return null;
    }

    /**
     * @return bool
     */
    private function itemIsNotOnLayoutBoundary(ReservationListItem $item)
    {
        $timeKey = $item->startDate()->timestamp;

        return ! (array_key_exists($timeKey, $this->_layoutByStartTime));
    }
}
