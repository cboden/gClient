<?php
namespace gClient\Calendar;

class Collection implements \SeekableIterator, \Countable {
    protected $calendars = Array();
    protected $lookup    = Array();

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
        if ($c1->accessLevel != $c2->accessLevel) {
            if ($c1->accessLevel == 'owner') {
                return 1;
            }

            if ($c2->accessLevel == 'owner') {
                return -1;
            }
        }

        return ($c1->title < $c2->title ? 1 : -1);
    }

    /**
     * @internal
     */
    public function remove($calendar) {
        $id = $calendar;
        if ($calendar instanceof Calendar) {
            $id = $calendar->unique_id;
        }

        $pos = array_search($id, $this->lookup);

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
     */
    public function seek($position) {
        if (!is_integer($position)) {
            $position = array_search($position, $this->lookup);
        }

        $this->pos = $position;

        if (!$this->valid()) {
            throw new OutOfBoundsException('Invalid index');
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

    public function next() {
        $this->pos++;
    }

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