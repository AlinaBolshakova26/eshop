<?php

namespace Core\Services;

use Core\Repositories\TagRepository;
use Models\Tag\Tag;


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

	public function getIdByQuery(array $tags, string $query = ''): ?int
	{
		$query = trim($query, '%');
		$pattern = '/' . preg_quote($query, '/') . '/ui';

		foreach ($tags as $tag) {
			if (preg_match($pattern, $tag->getName())) {
				return $tag->getId();
			}
		}

		return null;
	}

	public function getPaginatedTags(
		int $page,
		int $itemsPerPage,
		?string $query = null,
	): array {
		$offset = ($page - 1) * $itemsPerPage;

		return $this->tagRepository->findAllPaginated($itemsPerPage, $offset, $query);
	}

	public function getTotalPages(int $itemsPerPage, ?string $query = null): int
	{
		$totalTags = $this->tagRepository->getTotalCount($query);

		return ceil($totalTags / $itemsPerPage);
	}

	public function createTag(string $name): int
	{
		if (trim($name) === '') {
			throw new \InvalidArgumentException('Название тега не может быть пустым');
		}

		return $this->tagRepository->create($name);
	}

	public function findTagById(int $id): ?Tag
	{
		return $this->tagRepository->findTagById($id);
	}

	public function updateTag(int $id, string $newName): void
	{
		if (trim($newName) === '') {
			throw new \InvalidArgumentException('Новое название тега не может быть пустым');
		}

		$this->tagRepository->updateName($id, $newName);
	}

	public function toggleTagsStatus(array $tagIds, bool $isActive): void
	{
		if (empty($tagIds)) {
			throw new \InvalidArgumentException('Не выбрано ни одного тега');
		}

		$this->tagRepository->updateStatus($tagIds, $isActive);
	}

	public function addTagToProduct(int $productId, int $tagId): void
	{
		$this->tagRepository->addTagToProduct($productId, $tagId);
	}

	public function getLastCreatedTagId(): int
	{
		return $this->tagRepository->getLastInsertedId();
	}

	public function isTagNameExists(string $name): bool
	{
		return $this->tagRepository->findByName($name) !== null;
	}

	public function updateTagWithStatus(int $id, string $newName, bool $isActive): void
	{
		if (trim($newName) === '') {
			throw new \InvalidArgumentException('Новое название тега не может быть пустым');
		}

		$this->tagRepository->updateName($id, $newName);

		$this->tagRepository->updateStatus([$id], $isActive);
	}
}
