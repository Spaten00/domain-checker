<?php

namespace App\Models;

use Database\Factories\DomainFactory;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
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
 * @property-read Collection|Contract[] $contracts
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
 * @property string $name
 * @property-read int|null $contracts_count
 * @method static Builder|Domain whereName($value)
 * @property-read RrpproxyEntry $rrpproxyEntry
 * @property-read int|null $rrpproxy_entries_count
 * @property-read TanssEntry $tanssEntry
 * @property-read int|null $tanss_entries_count
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
     * @return BelongsToMany
     */
    public function contracts(): BelongsToMany
    {
        return $this->belongsToMany(Contract::class);
    }

    /**
     * Get the tanssEntries for the Domain.
     *
     * @return HasOne
     */
    public function tanssEntry(): HasOne
    {
        return $this->hasOne(TanssEntry::class);
    }

    /**
     * Get the rrpproxyEntries for the Domain.
     *
     * @return HasOne
     */
    public function rrpproxyEntry(): HasOne
    {
        return $this->hasOne(RrpproxyEntry::class);
    }

    // OTHER METHODS

    /**
     * Create a new domain with the desired name if it does not exist yet.
     *
     * @param string $domainName
     * @return Domain
     */
    public static function createDomain(string $domainName): Domain
    {
        $domain = self::where('name', $domainName)->first();
        if (!$domain) {
            $domain = new Domain;
            $domain->name = $domainName;
            $domain->save();
        }
        return $domain;
    }

    /**
     * @return array
     */
    public function getStatusAndText(): array
    {
        // missing TANSS and RRPproxy is running
        if (!$this->tanssEntry && !$this->rrpproxyEntry->isExpired()) {
            return ['badge bg-danger', 'TANSS fehlt'];
        }

        // missing TANSS and RRPproxy is not running
        if (!$this->tanssEntry && $this->rrpproxyEntry->isExpired()) {
            return ['badge bg-success', 'OK'];
        }

        // missing RRPproxy and TANSS is running
        if (!$this->rrpproxyEntry && !$this->tanssEntry->isExpired()) {
            return ['badge bg-danger', 'RRPproxy fehlt'];
        }

        // missing RRPproxy and TANSS is not running
        if (!$this->rrpproxyEntry && $this->tanssEntry->isExpired()) {
            return ['badge bg-success', 'OK'];
        }

        // expired in both
        if ($this->tanssEntry->isExpired() && $this->rrpproxyEntry->isExpired()) {
            return ['badge bg-success', 'OK'];
        }

        // expired in TANSS or RRPproxy
        if ($this->tanssEntry->isExpired() || $this->rrpproxyEntry->isExpired()) {
            return ['badge bg-danger', 'Vertrag ausgelaufen'];
        }

        // will expire soon
        if ($this->tanssEntry->willExpireSoon() || $this->rrpproxyEntry->willExpireSoon()) {
            return ['badge bg-warning', 'Vertrag läuft aus'];
        }

        return ['badge bg-success', 'OK'];
    }

    public function getStatusBadge(): string
    {
        [$statusClass, $statusText] = $this->getStatusAndText();
        return '<span class="' . $statusClass . '">' . $statusText . '</span>';
    }

    public function getTanssEnd(): string
    {
        if ($this->tanssEntry) {
            return Carbon::parse($this->tanssEntry->contract_end)->toDateString();
        }
        // TODO color and message if missing and stuff
        return '';
    }

    public function getRrpproxyEnd(): string
    {
        if ($this->rrpproxyEntry) {
            return Carbon::parse($this->rrpproxyEntry->contract_end)->toDateString();
        }
        // TODO color and message if missing and stuff
        return '';
    }

    public function getRrpproxyRenewal(): string
    {
        if ($this->rrpproxyEntry) {
            return Carbon::parse($this->rrpproxyEntry->contract_renewal)->toDateString();
        }
        // TODO color and message if missing and stuff
        return '';
    }
}
