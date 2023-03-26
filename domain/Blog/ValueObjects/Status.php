<?php

namespace Domain\Blog\ValueObjects;

/**
 * Statut d'un article
 */
enum Status: string
{
    case DRAFT = 'draft';
    case PUBLISHED = 'published';
}
