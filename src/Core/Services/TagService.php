<?php

namespace Core\Services;

use Core\Repositories\TagRepository;

class TagService
{
    
    private TagRepository $tagRepository;

    public function __construct(TagRepository $tagRepository)
    {

        $this->tagRepository = $tagRepository;

    }

    public function getAllTags(): array
    {
        return $this->tagRepository->getAll();
    }
}
