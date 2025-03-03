<?php

namespace Core\Services;

use Exception;
use Core\Repositories\ImageRepository;

class ImageService
{

	private const MAIN_IMAGE_DIR = '/assets/images/main_product_images/';
	private const ADDITIONAL_IMAGE_DIR = '/assets/images/additional_product_images/';
	private const UPLOAD_BASE_DIR = __DIR__ . '/public';

	private ImageRepository $repository;

	public function __construct(ImageRepository $repository)
	{
		$this->repository = $repository;
	}

	public function saveImage(int $productId, array $uploadedFile, bool $isMain = false): void
	{

		if (!$this->validateUploadedFile($uploadedFile)) 
		{
			return;
		}

		$imageDir = $isMain ? self::MAIN_IMAGE_DIR : self::ADDITIONAL_IMAGE_DIR;
		$uploadPath = $this->generateUploadPath($uploadedFile, $imageDir);

		$this->moveUploadedFile($uploadedFile['tmp_name'], $uploadPath);

		list($width, $height) = getimagesize($uploadPath);

		$this->processImage($productId, $imageDir . basename($uploadPath), $width, $height, $uploadedFile['name'], $isMain);

	}

	private function validateUploadedFile(array $uploadedFile): bool
	{

		if (!isset($uploadedFile['tmp_name']) || !is_uploaded_file($uploadedFile['tmp_name'])) 
		{
			return false;
		}

		$fileMimeType = mime_content_type($uploadedFile['tmp_name']);

		if (!str_starts_with($fileMimeType, 'image/')) 
		{
			return false;
		}

		return true;

	}

	private function generateUploadPath(array $uploadedFile, string $imageDir): string
	{

		$uniqueName = uniqid() . '_' . time() . '.' . pathinfo($uploadedFile['name'], PATHINFO_EXTENSION);

		return self::UPLOAD_BASE_DIR . $imageDir . $uniqueName;

	}

	private function moveUploadedFile(string $tmpFilePath, string $uploadPath): void
	{
		if (!move_uploaded_file($tmpFilePath, $uploadPath)) 
		{
			throw new \RuntimeException('Failed to move uploaded file.');
		}
	}

	private function processImage(int $productId, string $imagePath, int $width, int $height, string $description, bool $isMain): void
	{

		try 
		{
			$pdo = $this->repository->getPDO();
			$pdo->beginTransaction();

			if ($isMain === false)
			{
				$params = 
					[
						':path' => $imagePath,
						':item_id' => $productId,
						':is_main' => 0,
						':height' => $height,
						':width' => $width,
						':description' => $description,
					];
				$this->repository->insert($params);
			}
			else
			{
				$params =
				[
					'product_id' => $productId,
					'image_path' => $imagePath,
					'width' => $width,
					'height' => $height,
					'description' => $description
				];

				$result = $this->repository->processMainImage($params);	

				if ($result['action'] === 'update' && isset($result['existingImage']))
				{
					$oldImagePath = self::UPLOAD_BASE_DIR . $result['existingImage']['path'];
					if (file_exists($oldImagePath))
					{
						unlink($oldImagePath);
					}
				}
			}	
			$pdo->commit();   
		} 
		catch (\Exception $e) 
		{
			$pdo->rollBack();
			throw new \RuntimeException('Failed to save or update main image: ' . $e->getMessage());
		}

	}

	public function deleteImage(int $imageId): void
	{

		$image = $this->repository->findPathById($imageId);

		if (!$image) 
		{
			return;
		}

		$filePath = __DIR__ . '/public' . $image['path'];
		$fileExists = file_exists($filePath);

		$pdo = $this->repository->getPDO();
		$pdo->beginTransaction();

		try
		{
			$this->repository->deleteById($imageId);

			$pdo->commit();

			if ($fileExists) 
			{
				if (!unlink($filePath)) 
				{
					error_log('Не удалось удалить файл: ' . $filePath);
				}
			}
		}
		catch(Exception $e)
		{
			$pdo->rollBack();
			throw new \RuntimeException('Не удалось удалить изображение из базы данных: ' . $e->getMessage());
	
		}

	}
	
}