<?php
namespace Utils;

class PaginationHelper
{
	public static function buildPaginationUrl(?array $tagIds = null, int $page): string {
		$url = "/tag";
		if ($tagIds && !empty($tagIds)) {
			$url .= "?tags=" . htmlspecialchars(implode(',', $tagIds));
		}
		if ($page > 1) {
			$url .= ($url === "/tag" ? "?" : "&") . "page=$page";
		} else {
			$url .= ($url === "/tag" ? "?" : "&") . "page=1";
		}
		return $url;
	}

	public static function getActiveTags(array $tags, ?array $selectedTagIds = null): array {
		$activeTags = [];
		foreach ($tags as $tag) {
			if (in_array($tag->toListDTO()->id, $selectedTagIds ?? [])) {
				$activeTags[] = $tag->toListDTO()->name;
			}
		}
		return $activeTags;
	}

	public static function buildTagParam(?array $selectedTagIds = null, int $tagId, int $maxTags = 3): string {
		if (in_array($tagId, $selectedTagIds ?? [])) {
			$newTagIds = array_diff($selectedTagIds, [$tagId]);
		} else {
			$newTagIds = array_slice(array_merge($selectedTagIds ?? [], [$tagId]), -3);
		}
		return htmlspecialchars(implode(',', $newTagIds));
	}
}