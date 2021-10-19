<?php

namespace App\Models;

use Carbon\Carbon;
use Database\Factories\TanssEntryFactory;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\TanssEntry
 *
 * @method static TanssEntryFactory factory(...$parameters)
 * @method static Builder|TanssEntry newModelQuery()
 * @method static Builder|TanssEntry newQuery()
 * @method static Builder|TanssEntry query()
 * @mixin Eloquent
 * @property-read Customer $customer
 * @property-read Domain $domain
 * @property mixed|string|null $contract_start
 * @property mixed|string|null $contract_end
 * @property mixed $external_id
 * @property mixed $provider_name
 */
class TanssEntry extends Model
{
    use HasFactory;

    protected $table = 'tanss_entries';

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'external_id',
        'domain_id',
        'customer_id',
        'tanss_number',
        'provider_name',
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

    // SCOPES

    // RELATIONS

    /**
     * Get the domain for the tanssEntry.
     *
     * @return BelongsTo
     */
    public function domain(): BelongsTo
    {
        return $this->belongsTo(Domain::class);
    }

    /**
     * Get the customer for the tanssEntry.
     *
     * @return BelongsTo
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    // OTHER METHODS

    /**
     * Create a new tanssEntry if it does not exist yet.
     *
     * @param array $entry
     * @param Customer $customer
     * @param Domain $domain
     * @return TanssEntry
     */
    public static function createTanssEntry(array $entry, Customer $customer, Domain $domain): TanssEntry
    {
        $tanssEntry = TanssEntry::where('external_id', '=', $entry['externalId'])->first();
        if (!$tanssEntry) {
            $tanssEntry = new TanssEntry;
            $tanssEntry->external_id = $entry['externalId'];
            $tanssEntry->provider_name = $entry['providerName'];
            $tanssEntry->contract_start = self::getValidDate($entry['tanssContractStart']);
            $tanssEntry->contract_end = self::getValidDate($entry['tanssContractEnd']);
            $tanssEntry->customer()->associate($customer);
            $tanssEntry->domain()->associate($domain);
            $tanssEntry->save();
        }

        // TODO function for updating entries

        return $tanssEntry;
    }

    /**
     * Check if the date is valid.
     *
     * @param $dateToCheck
     * @return string|null
     */
    private static function getValidDate($dateToCheck): string|null
    {
        if (Carbon::parse($dateToCheck) > Carbon::createFromTimestamp(0)) {
            return $dateToCheck;
        }
        return null;
    }
}
