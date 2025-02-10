<?php

namespace Core\Services\Product;

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
