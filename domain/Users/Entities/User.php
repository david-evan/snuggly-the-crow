<?php

namespace Domain\Users\Entities;

use Carbon\Carbon;
use Domain\Common\Entities\BaseEntity;
use Helpers\StringUtils;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;
use InvalidArgumentException;
use Ramsey\Uuid\Uuid;

/**
 * Utilisateur de l'application (API)
 * @property string $id
 * @property string $username
 * @property string $password
 * @property \DateTime $last_login
 * @property string $api_key
 * @property \DateTime $api_key_expire_at
 *
 * @property \DateTime $updated_at
 * @property \DateTime $created_at
 * @property \DateTime $deleted_at
 *
 * @method static Builder whereUsernameIs(string $username)
 */
class User extends BaseEntity
{
    use HasUuids;

    /* ------------ CONST - ENTITY BUSINESS RULES ------------ */
    // Nombre maximum / minimum de caractères dans le titre d'un article
    const USERNAME_MIN_LENGTH = 3;
    const USERNAME_MAX_LENGTH = 32;

    const PASSWORD_MIN_LENGTH = 6;
    const PASSWORD_MAX_LENGTH = 128;
    const PASSWORD_HASH_ALGO = 'sha256';

    const API_KEY_VALIDITY_MINUTES = 120;


    /* ------------ ELOQUENT PROPERTIES ------------ */
    protected $fillable = ['username', 'password'];
    protected $hidden = ['updated_at', 'password', 'api_key', 'api_key_expire_at'];

    /**
     * @param array $attributes
     * @throws InvalidArgumentException
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
    }

    /* ------------ HELPERS  ------------ */

    /**
     * Met à jour la date de connexion de l'utilisateur et génère une nouvelle clef API
     * @return void
     */
    public function updateLastLoginAndGenerateNewApiKey(): self
    {
        $this->last_login = now();
        $this->api_key = Uuid::uuid4()->toString();
        $this->api_key_expire_at = now()->addMinutes(self::API_KEY_VALIDITY_MINUTES);
        return $this;
    }

    public function scopeWhereUsernameIs($query, string $username)
    {
        return $query->where('username', $username);
    }

    /* ------------ ELOQUENT SCOPES  ------------ */

    protected function username(): Attribute
    {
        return Attribute::make(
            set: function (string $value) {
                if (false ===
                    StringUtils::isLenBetween($value, min: self::USERNAME_MIN_LENGTH, max: self::USERNAME_MAX_LENGTH)) {
                    throw new InvalidArgumentException(
                        'Username must be between ' . self::USERNAME_MIN_LENGTH . ' and ' . self::USERNAME_MAX_LENGTH . ' chars'
                    );
                }
                return $value;
            }
        );
    }

    /* ------------ ACCESSOR / MUTATOR ------------ */

    protected function password(): Attribute
    {
        return Attribute::make(
            set: function (string $value) {
                if (false ===
                    StringUtils::isLenBetween($value, min: self::PASSWORD_MIN_LENGTH, max: self::PASSWORD_MAX_LENGTH)) {
                    throw new InvalidArgumentException(
                        'Login must be between ' . self::PASSWORD_MIN_LENGTH . ' and ' . self::PASSWORD_MAX_LENGTH . ' chars'
                    );
                }
                return $this->getHashedPassword($value);
            }
        );
    }

    public function getHashedPassword(string $password): string
    {
        return hash(self::PASSWORD_HASH_ALGO, $password);
    }

    protected function lastLogin(): Attribute
    {
        return Attribute::make(
            get: fn($value) => $value === null ? null : (new Carbon($value))->format(DATE_RFC3339)
        );
    }

    protected function apiKeyExpireAt(): Attribute
    {
        return Attribute::make(
            get: fn($value) => $value === null ? null : (new Carbon($value))->format(DATE_RFC3339)
        );
    }
}
