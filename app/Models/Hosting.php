<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\Hosting
 *
 * @property int $id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property int $contract_id
 * @property string $domain_name
 * @property-read \App\Models\Contract $contract
 * @method static \Database\Factories\HostingFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|Hosting newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Hosting newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Hosting query()
 * @method static \Illuminate\Database\Eloquent\Builder|Hosting whereContractId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Hosting whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Hosting whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Hosting whereDomainName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Hosting whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Hosting whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property string $hosting_type
 * @method static \Illuminate\Database\Eloquent\Builder|Hosting whereHostingType($value)
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
        'domain_name',
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
