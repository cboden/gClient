<?php
namespace gClient\Calendar;

/**
 * @property string $id
 * @property string $title
 * @property string $kind
 * @property string $etag
 * @property string $selfLink
 * @property string $alternateLink
 * @property int $canEdit
 * @property string $created
 * @property string $updated
 * @property string $details
 * @property string $status
 * @property Array $creator
 * @property int $anyoneCanAddSelf
 * @property int $guestsCanInviteOthers
 * @property int $guestsCanModify
 * @property int $guestsCanSeeGuests
 * @property int $sequence
 * @property string $transparency
 * @property string $visibility
 * @property string $location
 * @property array $attendees
 * @property array $when
 */
class Event extends \gClient\PropertyProxy {
    protected $connection;

    public function __construct(Array $data, \gClient\Connection $connection) {
//        $this->connection = $connection;
        $this->setData($data);
    }
}