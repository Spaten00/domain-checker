<?php

namespace App\Models;

use Database\Factories\DomainFactory;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * App\Models\Domain
 *
 * @property int $id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property int $contract_id
 * @property string $domain_name
 * @property-read Contract $contract
 * @method static DomainFactory factory(...$parameters)
 * @method static Builder|Domain newModelQuery()
 * @method static Builder|Domain newQuery()
 * @method static Builder|Domain query()
 * @method static Builder|Domain whereContractId($value)
 * @method static Builder|Domain whereCreatedAt($value)
 * @method static Builder|Domain whereDeletedAt($value)
 * @method static Builder|Domain whereDomainName($value)
 * @method static Builder|Domain whereId($value)
 * @method static Builder|Domain whereUpdatedAt($value)
 * @mixin Eloquent
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
        'name',
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
