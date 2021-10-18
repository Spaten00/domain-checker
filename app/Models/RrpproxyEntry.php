<?php

namespace App\Models;

use Carbon\Carbon;
use Database\Factories\RrpproxyEntryFactory;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\RrpproxyEntry
 *
 * @method static RrpproxyEntryFactory factory(...$parameters)
 * @method static Builder|RrpproxyEntry newModelQuery()
 * @method static Builder|RrpproxyEntry newQuery()
 * @method static Builder|RrpproxyEntry query()
 * @mixin Eloquent
 * @property-read Domain $domain
 */
class RrpproxyEntry extends Model
{
    use HasFactory;

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
            var_dump("Gibbet noch nicht");
            $rrpproxyEntry = new RRPProxyEntry;
            $rrpproxyEntry->contract_start = self::getValidDate($entry['rrpproxyContractStart']);
            $rrpproxyEntry->contract_end = self::getValidDate($entry['rrpproxyContractEnd']);
            $rrpproxyEntry->domain()->associate($domain);
            $rrpproxyEntry->save();
        }
        var_dump($rrpproxyEntry);
        return $rrpproxyEntry;
    }

    //TODO refactor
    private static function getValidDate(mixed $dateToCheck): string|null
    {
        if (Carbon::parse($dateToCheck) > Carbon::createFromTimestamp(0)) {
            return $dateToCheck;
        }
        return null;
    }
}
