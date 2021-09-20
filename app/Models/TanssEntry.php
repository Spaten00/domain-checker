<?php

namespace App\Models;

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
}
