<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\Domain
 *
 * @property int $id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property int $contract_id
 * @property string $domain_name
 * @property-read \App\Models\Contract $contract
 * @method static \Database\Factories\DomainFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|Domain newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Domain newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Domain query()
 * @method static \Illuminate\Database\Eloquent\Builder|Domain whereContractId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Domain whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Domain whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Domain whereDomainName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Domain whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Domain whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Domain extends Model
{
    use HasFactory;

    protected $table = 'domains';

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
     * Get the contract for the domain.
     *
     * @return BelongsTo
     */
    public function contract(): BelongsTo
    {
        return $this->belongsTo(Contract::class);
    }

    // OTHER METHODS
}
