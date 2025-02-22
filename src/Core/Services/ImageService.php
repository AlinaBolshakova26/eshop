<?php

namespace Core\Services;

use PDO;

class ImageService
{
	private const MAIN_IMAGE_DIR = '/assets/images/main_product_images/';
	private const ADDITIONAL_IMAGE_DIR = '/assets/images/additional_product_images/';
	private const UPLOAD_BASE_DIR = __DIR__ . '/../../../public';

	private PDO $pdo;

	public function __construct(PDO $pdo)
	{
		$this->pdo = $pdo;
	}

	public function saveImage(int $productId, array $uploadedFile, bool $isMain = false): void
	{
		if (!$this->validateUploadedFile($uploadedFile)) {
			return;
		}

		$imageDir = $isMain ? self::MAIN_IMAGE_DIR : self::ADDITIONAL_IMAGE_DIR;
		$uploadPath = $this->generateUploadPath($uploadedFile, $imageDir);

		$this->moveUploadedFile($uploadedFile['tmp_name'], $uploadPath);

		list($width, $height) = getimagesize($uploadPath);

		$this->saveImageToDatabase($productId, $imageDir . basename($uploadPath), $isMain, $width, $height, $uploadedFile['name']);
	}

	private function validateUploadedFile(array $uploadedFile): bool
	{
		if (!isset($uploadedFile['tmp_name']) || !is_uploaded_file($uploadedFile['tmp_name'])) {
			return false;
		}

		$fileMimeType = mime_content_type($uploadedFile['tmp_name']);

		if (!str_starts_with($fileMimeType, 'image/')) {
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
		if (!move_uploaded_file($tmpFilePath, $uploadPath)) {
			throw new \RuntimeException('Failed to move uploaded file.');
		}
	}

	private function saveImageToDatabase(int $productId, string $imagePath, bool $isMain, int $width, int $height, string $description): void
	{
		if ($isMain) {
			$this->saveOrUpdateMainImage($productId, $imagePath, $width, $height, $description);
		} else {
			$this->insertAdditionalImage($productId, $imagePath, $width, $height, $description);
		}
	}

	private function saveOrUpdateMainImage(int $productId, string $imagePath, int $width, int $height, string $description): void
	{
		$this->pdo->beginTransaction();

		try {
			$stmt = $this->pdo->prepare("SELECT id, path FROM up_image WHERE item_id = :item_id AND is_main = 1 FOR UPDATE");
			$stmt->execute([':item_id' => $productId]);
			$existingImage = $stmt->fetch(PDO::FETCH_ASSOC);

			if ($existingImage) {
				$oldImagePath = self::UPLOAD_BASE_DIR . $existingImage['path'];
				if (file_exists($oldImagePath)) {
					unlink($oldImagePath);
				}

				$stmt = $this->pdo->prepare("
                UPDATE up_image 
                SET path = :path, height = :height, width = :width, description = :description, updated_at = NOW()
                WHERE id = :id
            ");
				$stmt->execute([
					':path' => $imagePath,
					':height' => $height,
					':width' => $width,
					':description' => $description,
					':id' => $existingImage['id'],
				]);
			} else {
				$stmt = $this->pdo->prepare("
                INSERT INTO up_image (path, item_id, is_main, height, width, description, created_at, updated_at) 
                VALUES (:path, :item_id, :is_main, :height, :width, :description, NOW(), NOW())
            ");
				$stmt->execute([
					':path' => $imagePath,
					':item_id' => $productId,
					':is_main' => 1,
					':height' => $height,
					':width' => $width,
					':description' => $description,
				]);
			}

			$this->pdo->commit();
		} catch (\Exception $e) {
			$this->pdo->rollBack();
			throw new \RuntimeException('Failed to save or update main image: ' . $e->getMessage());
		}
	}

	private function insertAdditionalImage(int $productId, string $imagePath, int $width, int $height, string $description): void
	{
		$stmt = $this->pdo->prepare("
            INSERT INTO up_image (path, item_id, is_main, height, width, description, created_at, updated_at) 
            VALUES (:path, :item_id, :is_main, :height, :width, :description, NOW(), NOW())
        ");
		$stmt->execute([
			':path' => $imagePath,
			':item_id' => $productId,
			':is_main' => 0,
			':height' => $height,
			':width' => $width,
			':description' => $description,
		]);
	}

	public function deleteImage(int $imageId): void
	{
		$stmt = $this->pdo->prepare("SELECT path FROM up_image WHERE id = :id");
		$stmt->execute([':id' => $imageId]);
		$image = $stmt->fetch(PDO::FETCH_ASSOC);

		if ($image) {
			$filePath = __DIR__ . '/../../../public' . $image['path'];
			if (file_exists($filePath)) {
				unlink($filePath);
			}

			$stmt = $this->pdo->prepare("DELETE FROM up_image WHERE id = :id");
			$stmt->execute([':id' => $imageId]);
		}
	}
}