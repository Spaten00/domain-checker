<?php

namespace App\Models;

use Database\Factories\HostingFactory;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * App\Models\Hosting
 *
 * @property int $id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property int $contract_id
 * @property string $domain_name
 * @property-read Contract $contract
 * @method static HostingFactory factory(...$parameters)
 * @method static Builder|Hosting newModelQuery()
 * @method static Builder|Hosting newQuery()
 * @method static Builder|Hosting query()
 * @method static Builder|Hosting whereContractId($value)
 * @method static Builder|Hosting whereCreatedAt($value)
 * @method static Builder|Hosting whereDeletedAt($value)
 * @method static Builder|Hosting whereDomainName($value)
 * @method static Builder|Hosting whereId($value)
 * @method static Builder|Hosting whereUpdatedAt($value)
 * @mixin Eloquent
 * @property string $hosting_type
 * @method static Builder|Hosting whereHostingType($value)
 */
class Hosting extends Model
{
    use HasFactory;

    protected $table = 'hostings';

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'contract_id',
        'type',
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
     * Get the contract for the hosting.
     *
     * @return BelongsTo
     */
    public function contract(): BelongsTo
    {
        return $this->belongsTo(Contract::class);
    }

    // OTHER METHODS
}
