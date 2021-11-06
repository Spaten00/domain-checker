<?php

namespace App\Models;

use Database\Factories\RrpproxyEntryFactory;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

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
 * @property int $id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int $domain_id
 * @property string|null $contract_renewal
 * @method static Builder|RrpproxyEntry whereContractEnd($value)
 * @method static Builder|RrpproxyEntry whereContractRenewal($value)
 * @method static Builder|RrpproxyEntry whereContractStart($value)
 * @method static Builder|RrpproxyEntry whereCreatedAt($value)
 * @method static Builder|RrpproxyEntry whereDomainId($value)
 * @method static Builder|RrpproxyEntry whereId($value)
 * @method static Builder|RrpproxyEntry whereUpdatedAt($value)
 */
class RrpproxyEntry extends Entry
{
    use HasFactory;

    protected $table = 'rrpproxy_entries';

    /**
     * Create a new Eloquent Model instance and merges the fillable.
     *
     * @param array $attributes
     */
    public function __construct(array $attributes = array())
    {
        parent::__construct($attributes);

        $this->mergeFillable(['contract_renewal']);
    }

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

    /**
     * Create a new RRPproxyEntry if it does not exist yet or update it.
     *
     * @param array $entry
     * @param Domain $domain
     * @return RrpproxyEntry
     */
    public static function createOrUpdateRrpproxyEntry(array $entry, Domain $domain): RrpproxyEntry
    {
        $rrpproxyEntry = self::whereHas('domain', function (Builder $query) use ($entry) {
            $query->where('name', 'like', $entry['domain']);
        })->first();

        if (!$rrpproxyEntry) {
            $rrpproxyEntry = new RRPProxyEntry;
        }
        $rrpproxyEntry->contract_start = self::getValidDate($entry['rrpproxyContractStart']);
        $rrpproxyEntry->contract_end = self::getValidDate($entry['rrpproxyContractEnd']);
        $rrpproxyEntry->contract_renewal = self::getValidDate($entry['rrpproxyContractRenewal']);
        $rrpproxyEntry->domain()->associate($domain);
        $rrpproxyEntry->save();

        return $rrpproxyEntry;
    }
}
