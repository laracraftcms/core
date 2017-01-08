<?php namespace Laracraft\Core\Entities\Traits;

use Carbon\Carbon;
use Illuminate\Support\Facades\Event;
use Laracraft\Core\Entities\Observers\PublishableObserver;
use Laracraft\Core\Entities\Scopes\PublishedScope;

trait Publishable
{

    protected $published_at_column;
    protected $expired_at_column;
    public $publishing = false;
    public $expiring = false;

    public static function bootPublishable()
    {

        static::observe(new PublishableObserver());
        static::addGlobalScope(new PublishedScope());

    }

    /**
     * Get the "published_at" column.
     *
     * @return string
     */
    public function getPublishedAtColumn()
    {
        if (!isset($this->published_at_column)) {
            $this->published_at_column = 'published_at';
        }
        return $this->published_at_column;
    }

    /**
     * Get the "expired_at" column.
     *
     * @return string
     */
    public function getExpiredAtColumn()
    {
        if (!isset($this->expired_at_column)) {
            $this->expired_at_column = 'expired_at';
        }
        return $this->expired_at_column;
    }

    /**
     * Get the fully qualified "published_at" column.
     *
     * @return string
     */
    public function getQualifiedPublishedAtColumn()
    {
        return $this->getTable() . '.' . $this->getPublishedAtColumn();
    }

    /**
     * Get the fully qualified "published_at" column.
     *
     * @return string
     */
    public function getQualifiedExpiredAtColumn()
    {
        return $this->getTable() . '.' . $this->getExpiredAtColumn();
    }


    function setPublishedAtAttribute($published)
    {
        $published = $published instanceof Carbon ? $published : new Carbon($published);

        $this->attributes[$this->getPublishedAtColumn()] = $published;

    }

    function getPublishedAtAttribute()
    {
        $published = $this->attributes[$this->getPublishedAtColumn()];

        if (isNull($published)) {
            return $published;
        }

        return $published instanceof Carbon ? $published : new Carbon($published);

    }

    function setExpiredAtAttribute($expired)
    {
        $expired = $expired instanceof Carbon ? $expired : new Carbon($expired);

        $this->attributes[$this->getExpiredAtColumn()] = $expired;

    }

    function getExpiredAtAttribute()
    {
        $expired = $this->attributes[$this->getExpiredAtColumn()];

        if (isNull($expired)) {
            return $expired;
        }

        return $expired instanceof Carbon ? $expired : new Carbon($expired);

    }

    /**
     * Fire the namespaced published_at.change event.
     *
     * @param $to Carbon
     * @param $from Carbon
     * @param $event string|null - the action that triggered this event, either `saving`, or `restoring`
     * a deleted but still enabled event
     * @return mixed
     */
    protected function firePublishedAtChangeEvent(Carbon $to, Carbon $from, $event = null)
    {
        return Event::until("eloquent.published_at.change: " . get_class($this), [$this, $to, $from, $event]);
    }

    /**
     * Fire the namespaced expired_at.change event.
     *
     * @param $to Carbon
     * @param $from Carbon
     * @param $event string|null
     */
    protected function fireExpiredAtChangedEvent(Carbon $to, Carbon $from, $event = null)
    {
        Event::fire("eloquent.expired_at.change: " . get_class($this), [$this, $to, $from, $event]);
    }

}