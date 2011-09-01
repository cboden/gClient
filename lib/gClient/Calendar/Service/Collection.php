<?php
namespace gClient\Calendar\Service;
use gClient\Calendar\Calendar;

/**
 * Container, Iterator of an accounts Calendars, provided from Service
 */
class Collection implements \SeekableIterator, \Countable {
    /**
     * @internal
     */
    protected $calendars = Array();

    /**
     * @internal
     */
    protected $lookup = Array();

    /**
     * @internal
     */
    protected $pos = 0;

    /**
     * @internal
     */
    public function insert(Calendar $calendar) {
        $this->calendars[$calendar->unique_id] = $calendar;

        foreach ($this->lookup as $i => &$id) {
            if (1 === $this->compare($calendar, $this->calendars[$id])) {
                array_splice($this->lookup, $i, 0, $calendar->unique_id);
                return;
            }
        }

        array_push($this->lookup, $calendar->unique_id);
    }

    /**
     * @internal
     */
    public function compare(Calendar $c1, Calendar $c2) {
        if ($c1->properties->accessLevel != $c2->properties->accessLevel) {
            if ($c1->properties->accessLevel == 'owner') {
                return 1;
            }

            if ($c2->properties->accessLevel == 'owner') {
                return -1;
            }
        }

        return ($c1->settings->title < $c2->settings->title ? 1 : -1);
    }

    /**
     * @internal
     */
    public function remove($calendar) {
        $id = $calendar;
        if ($calendar instanceof Calendar) {
            $id = $calendar->unique_id;
        }

        if (false === ($pos = array_search($id, $this->lookup))) {
            throw new \OutOfBoundsException("{$calendar} not found in collection");
        }

        unset($this->calendars[$id]);
        array_splice($this->lookup, $pos, 1);

        if ($pos == $this->pos && $pos > 0) {
            $this->pos--;
        }
    }

    /**
     * @return int
     */
    public function count() {
        return count($this->calendars);
    }

    /**
     * @param int|string The index or unique_id of the calendar to seek to
     * @return void
     */
    public function seek($position) {
        if (!is_integer($position)) {
            if (false === ($position = array_search($position, $this->lookup))) {
                throw new \OutOfBoundsException("Calendar with ID '{$position}' was not found");
            }
        }

        $current   = $this->pos;
        $this->pos = (int)$position;

        if (!$this->valid()) {
            $this->pos = $current;
            throw new \OutOfBoundsException('Invalid index');
        }
    }

    /**
     * @return Calendar
     */
    public function current() {
        return $this->calendars[$this->lookup[$this->pos]];
    }

    /**
     * @return int
     */
    public function key() {
        return $this->pos;
    }

    /**
     * @return void
     */
    public function next() {
        $this->pos++;
    }

    /**
     * @return void
     */
    public function rewind() {
        $this->pos = 0;
    }

    /**
     * @return bool
     */
    public function valid() {
        return isset($this->lookup[$this->pos]);
    }
}