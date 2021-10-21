<?php

namespace App\Models;

use Carbon\Carbon;
use Database\Factories\RrpproxyEntryFactory;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * App\Models\RrpproxyEntry
 *
 * @method static RrpproxyEntryFactory factory(...$parameters)
 * @method static Builder|RrpproxyEntry newModelQuery()
 * @method static Builder|RrpproxyEntry newQuery()
 * @method static Builder|RrpproxyEntry query()
 * @mixin Eloquent
 * @property-read Domain $domain
 * @property mixed|string|null $contract_start
 * @property mixed|string|null $contract_end
 */
class RrpproxyEntry extends Model
{
    use HasFactory;

    const SOON = 30;
    protected $table = 'rrpproxy_entries';

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'domain_id',
        'contract_start',
        'contract_end',
        'contract_renewal',
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

    // SCOPES

    // RELATIONS


    /**
     * Get the domain for the rrpproxyEntry.
     *
     * @return BelongsTo
     */
    public function domain(): BelongsTo
    {
        return $this->belongsTo(Domain::class);
    }

    // OTHER METHODS
    public static function createRrpproxyEntry(array $entry, Domain $domain): RRPProxyEntry
    {
        $rrpproxyEntry = RRPProxyEntry::whereHas('domain', function (Builder $query) use ($entry) {
            $query->where('name', 'like', $entry['domain']);
        })->first();

        if (!$rrpproxyEntry) {
            $rrpproxyEntry = new RRPProxyEntry;
            $rrpproxyEntry->contract_start = self::getValidDate($entry['rrpproxyContractStart']);
            $rrpproxyEntry->contract_end = self::getValidDate($entry['rrpproxyContractEnd']);
            $rrpproxyEntry->contract_renewal = self::getValidDate($entry['rrpproxyContractRenewal']);
            $rrpproxyEntry->domain()->associate($domain);
            $rrpproxyEntry->save();
        }

        return $rrpproxyEntry;
    }

    //TODO refactor to a new parent class which rrpproxy and tanss can inherit from
    private static function getValidDate(mixed $dateToCheck): string|null
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
        if ($this->contract_end > now()) {
            return false;
        }
        return true;
    }

    /**
     * Check if the entry will expire soon.
     *
     * @return bool
     */
    public function willExpireSoon(): bool
    {
        if ($this->contract_end > now()->addDays(self::SOON)) {
            return false;
        }
        return true;
    }
}
