<?php

namespace App\Models;

use Database\Factories\TanssEntryFactory;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

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
 * @property int $id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property int $domain_id
 * @property int $customer_id
 * @method static Builder|TanssEntry whereContractEnd($value)
 * @method static Builder|TanssEntry whereContractStart($value)
 * @method static Builder|TanssEntry whereCreatedAt($value)
 * @method static Builder|TanssEntry whereCustomerId($value)
 * @method static Builder|TanssEntry whereDeletedAt($value)
 * @method static Builder|TanssEntry whereDomainId($value)
 * @method static Builder|TanssEntry whereExternalId($value)
 * @method static Builder|TanssEntry whereId($value)
 * @method static Builder|TanssEntry whereProviderName($value)
 * @method static Builder|TanssEntry whereUpdatedAt($value)
 */
class TanssEntry extends Entry
{
    use HasFactory;

    protected $table = 'tanss_entries';

    /**
     * Create a new Eloquent Model instance and merges the fillable.
     *
     * @param array $attributes
     */
    public function __construct(array $attributes = array())
    {
        parent::__construct($attributes);

        $this->mergeFillable(['external_id',
            'customer_id',
            'tanss_number',
            'provider_name']);
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
        $tanssEntry = self::where('external_id', '=', $entry['externalId'])->first();
//        var_dump($tanssEntry);
        if (!$tanssEntry) {
            $tanssEntry = new TanssEntry;
            $tanssEntry->external_id = $entry['externalId'];
            $tanssEntry->provider_name = $entry['providerName'];
            $tanssEntry->contract_start = self::getValidDate($entry['tanssContractStart']);
            $tanssEntry->contract_end = self::getValidDate($entry['tanssContractEnd']);
            $tanssEntry->customer()->associate($customer);
            $tanssEntry->domain()->associate($domain);
            $tanssEntry->save();
        } else {
        }

        // TODO function for updating entries

        return $tanssEntry;
    }
}
