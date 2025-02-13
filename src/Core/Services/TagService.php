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

	public function addTagsToProduct(int $productId, array $tagIds): void
	{
		foreach ($tagIds as $tagId) {
			$this->tagRepository->addTagToProduct($productId, $tagId);
		}
	}

	public function updateProductTags(int $productId, array $selectedTagIds): void
	{
		$currentTagIds = $this->tagRepository->getTagsByProductId($productId);

		foreach ($currentTagIds as $tagId) {
			if (!in_array($tagId, $selectedTagIds)) {
				$this->tagRepository->removeTagFromProduct($productId, $tagId);
			}
		}

		foreach ($selectedTagIds as $tagId) {
			if (!in_array($tagId, $currentTagIds)) {
				$this->tagRepository->addTagToProduct($productId, $tagId);
			}
		}
	}

	public function getTagsByProductId(int $productId): array
	{
		return $this->tagRepository->getTagsByProductId($productId);
	}
}
