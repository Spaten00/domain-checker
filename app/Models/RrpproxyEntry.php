<?php

namespace App\Models;

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
}
