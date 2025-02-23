<?php
namespace Utils;

class PaginationHelper
{
	public static function buildPaginationUrl(?array  $tagIds = null,
											  int     $page,
											  ?float  $minPrice = null,
											  ?float  $maxPrice = null,
											  ?string $searchValue = null): string
	{
		$url = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

		$params = [];

		if ($tagIds && !empty($tagIds)) {
			$params['tags'] = implode(',', $tagIds);
		}

		if ($searchValue) {
			$params['searchInput'] = $searchValue;
		}

		if ($minPrice !== null) {
			$params['minPrice'] = $minPrice;
		}
		if ($maxPrice !== null) {
			$params['maxPrice'] = $maxPrice;
		}

		if ($page > 1) {
			$params['page'] = $page;
		}

		if (!empty($params)) {
			$url .= '?' . http_build_query($params);
		}

		return $url;
	}

	public static function getActiveTags(array $tags, ?array $selectedTagIds = null): array
	{
		$activeTags = [];
		foreach ($tags as $tag) {
			if (in_array($tag->toListDTO()->id, $selectedTagIds ?? [])) {
				$activeTags[] = $tag->toListDTO()->name;
			}
		}
		return $activeTags;
	}

	public static function buildTagParam(?array $selectedTagIds = null, int $tagId): string
	{
		if (in_array($tagId, $selectedTagIds ?? [])) {
			$newTagIds = array_diff($selectedTagIds, [$tagId]);
		} else {
			$newTagIds = array_merge($selectedTagIds ?? [], [$tagId]);
		}
		return implode(',', $newTagIds);
	}

	public static function removeQueryParams(array $paramsToRemove): string
	{
		$params = $_GET;

		foreach ($paramsToRemove as $param) {
			unset($params[$param]);
		}

		return '?' . http_build_query($params);
	}
}
