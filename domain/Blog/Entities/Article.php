<?php

namespace Domain\Blog\Entities;

use Carbon\Carbon;
use Domain\Blog\ValueObjects\Status;
use Domain\Common\Entities\BaseEntity;
use Helpers\StringUtils;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;
use InvalidArgumentException;

/**
 * Article de blog
 * @property string $id
 * @property string $title
 * @property string $content
 * @property \DateTime $published_at
 * @property Status $status;
 *
 * @property \DateTime $updated_at
 * @property \DateTime $created_at
 * @property \DateTime $deleted_at
 */
class Article extends BaseEntity
{
    use HasUuids, SoftDeletes;

    /* ------------ CONST - ENTITY BUSINESS RULES ------------ */
    // Nombre maximum / minimum de caractères dans le titre d'un article
    const TITLE_MIN_LENGTH = 3;
    const TITLE_MAX_LENGTH = 128;


    /* ------------ ELOQUENT PROPERTIES ------------ */
    protected $fillable = ['title', 'content', 'status', 'published_at'];
    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];
    protected $attributes = [ // Valeurs par défaut des propriétés de l'entité
        'published_at' => null,
    ];

    /**
     * @param array $attributes
     * @throws InvalidArgumentException
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
    }

    /* ------------ HELPERS ------------*/

    /**
     * Indique si un article est publié
     * @return bool
     */
    public function isPublished() : bool
    {
        return Status::tryFrom($this->status) === Status::PUBLISHED;
    }

    public function isDraft() : bool
    {
        return Status::tryFrom($this->status) === Status::DRAFT;
    }


    /* ------------ ACCESSOR / MUTATOR ------------ */

    protected function title(): Attribute
    {
        return Attribute::make(
            set: function (string $value) {
                if (false ===
                    StringUtils::isLenBetween($value, min: self::TITLE_MIN_LENGTH, max : self::TITLE_MAX_LENGTH)) {
                    throw new InvalidArgumentException(
                        'Title must be between '. self::TITLE_MIN_LENGTH .' and ' . self::TITLE_MAX_LENGTH . ' chars'
                    );
                }
                return $value;
            }
        );
    }

    protected function status() : Attribute
    {
        return Attribute::make(
            set: function (string|Status $value) {
                if (false === $value instanceof  Status && null === Status::tryFrom($value)) {
                    throw new InvalidArgumentException(
                        'Status must be in list ['. implode(', ', array_column(Status::cases(), 'value')) .']'
                    );
                }
                return $value;
            }
        );
    }

    protected function publishedAt() : Attribute
    {
        return Attribute::make(
            get:  fn($value) => $value === null ? null : (new Carbon($value))->format(DATE_RFC3339),

            set: function (string|\DateTime $value) {
                $carbonDate = new Carbon($value);
                if (false === $carbonDate instanceof Carbon) {
                    throw new InvalidArgumentException(
                        'Article publish date should be a valid date'
                    );
                }
                return $carbonDate->format(DATE_RFC3339);
            }
        );
    }
}
