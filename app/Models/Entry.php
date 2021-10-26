<?php

namespace App\Models;

use Carbon\Carbon;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Entry
 *
 * @method static Builder|Entry newModelQuery()
 * @method static Builder|Entry newQuery()
 * @method static Builder|Entry query()
 * @mixin Eloquent
 * @property mixed $contract_end
 */
abstract class Entry extends Model
{
    use HasFactory;

    public const SOON = 30;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'domain_id',
        'contract_start',
        'contract_end',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [];

    /**
     * Check if the date is valid.
     *
     * @param $dateToCheck
     * @return string|null
     */
    public static function getValidDate($dateToCheck): string|null
    {
        if (Carbon::parse($dateToCheck) > Carbon::createFromTimestamp(0)) {
            return $dateToCheck;
        }
        return null;
    }

    /**
     * Check if entry is already expired.
     *
     * @return bool
     */
    public function isExpired(): bool
    {
        return !($this->contract_end > now());
    }

    /**
     * Check if the entry will expire soon.
     *
     * @return bool
     */
    public function willExpireSoon(): bool
    {
        return !($this->contract_end > now()->addDays(self::SOON));
    }
}
