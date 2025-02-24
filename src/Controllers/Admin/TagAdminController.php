<?php
namespace Controllers\Admin;

use Core\Database\MySQLDatabase;
use Core\Repositories\AdminRepository;
use Core\Repositories\ProductRepository;
use Core\Repositories\RatingRepository;
use Core\Repositories\TagRepository;
use Core\Services\AdminService;
use Core\Services\ProductService;
use Core\Services\RatingService;
use Core\Services\TagService;
use Core\View;

use Core\Services\TransliterateService;


class TagAdminController
{
	private AdminService $adminService;
	private TagService $tagService;

	private RatingService $ratingService;
	private ProductService $productService;

	public function __construct()
	{
		$database = new MySQLDatabase();
		$pdo = $database->getConnection();

		$this->adminService = new AdminService(new AdminRepository($pdo));
		$this->tagService = new TagService(new TagRepository($pdo));
		$this->productService = new ProductService(new ProductRepository($pdo),new RatingRepository($pdo));
	}


	public function index(): void
	
	{

		try {
			$currentPage = max(1, (int)($_GET['page'] ?? 1));
			define("ITEMS_PER_PAGE", 10);

			$tags = $this->tagService->getPaginatedTags($currentPage, ITEMS_PER_PAGE);

			$totalPages = $this->tagService->getTotalPages(ITEMS_PER_PAGE);

			$content = View::make(__DIR__ . '/../../Views/admin/tags/index.php', [
				'tags' => $tags,
				'totalPages' => $totalPages,
				'currentPage' => $currentPage,
			]);

			echo View::make(__DIR__ . '/../../Views/layouts/admin_layout.php', [
				'content' => $content,
			]);
		} catch (\Exception $e) {
			echo "Ошибка: " . htmlspecialchars($e->getMessage());
		}
	}

	public function create(): void
	{
		if ($_SERVER['REQUEST_METHOD'] === 'POST') {
			$name = trim($_POST['name'] ?? '');
			if ($name === '') {
				echo "Ошибка: Название тега не может быть пустым.";
				return;
			}

			if ($this->tagService->isTagNameExists($name)) {
				echo "Ошибка: Тег с таким названием уже существует.";
				return;
			}

			try {
				$this->tagService->createTag($name);

				$tagId = $this->tagService->getLastCreatedTagId();

				if (isset($_POST['products']) && is_array($_POST['products'])) {
					foreach ($_POST['products'] as $productId) {
						$this->tagService->addTagToProduct((int)$productId, $tagId);
					}
				}

				header('Location: /admin/tags');
				exit;
			} catch (\Exception $e) {
				echo "Ошибка: " . htmlspecialchars($e->getMessage());
			}
		}

		$products = $this->productService->getAllProducts();

		$content = View::make(__DIR__ . '/../../Views/admin/tags/add_tag.php', [
			'products' => $products,
		]);

		echo View::make(__DIR__ . '/../../Views/layouts/admin_layout.php', [
			'content' => $content,
		]);
	}

	public function edit(int $id): void
	{
		$tag = $this->tagService->findTagById($id);

		if (!$tag) {
			echo "Тег не найден.";
			return;
		}

		$content = View::make(__DIR__ . '/../../Views/admin/tags/detail.php', [
			'tag' => $tag,
		]);

		echo View::make(__DIR__ . '/../../Views/layouts/admin_layout.php', [
			'content' => $content,
		]);
	}

	public function process(): void
	{
		if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
			header('Location: /admin/tags');
			exit;
		}

		$action = $_POST['action'] ?? '';
		$selectedTags = $_POST['selected_tags'] ?? [];

		try {
			switch ($action) {
				case 'activate':
					$this->tagService->toggleTagsStatus($selectedTags, true);
					break;

				case 'deactivate':
					$this->tagService->toggleTagsStatus($selectedTags, false);
					break;

				default:
					throw new \Exception('Неизвестное действие');
			}

			header('Location: /admin/tags');
			exit;
		} catch (\Exception $e) {
			echo "Ошибка: " . htmlspecialchars($e->getMessage());
		}
	}
	public function store(): void
	{
		$name = trim($_POST['name'] ?? '');
		if ($name === '') {
			echo "Ошибка: Название тега не может быть пустым.";
			return;
		}

		if ($this->tagService->isTagNameExists($name)) {
			echo "Ошибка: Тег с таким названием уже существует.";
			return;
		}

		try {
			$this->tagService->createTag($name);

			if (isset($_POST['products']) && is_array($_POST['products'])) {
				$tagId = $this->tagService->getLastCreatedTagId();
				foreach ($_POST['products'] as $productId) {
					$this->tagService->addTagToProduct((int)$productId, $tagId);
				}
			}

			header('Location: /admin/tags');
			exit;
		} catch (\Exception $e) {
			echo "Ошибка: " . htmlspecialchars($e->getMessage());
		}
	}

	public function update(int $id): void
	{
		$newName = trim($_POST['name'] ?? '');
		$isActive = isset($_POST['is_active']) && $_POST['is_active'] === 'on';

		if ($newName === '') {
			echo "Ошибка: Название тега не может быть пустым.";
			return;
		}

		try {
			// Обновляем тег
			$this->tagService->updateTagWithStatus($id, $newName, $isActive);

			header('Location: /admin/tags');
			exit;
		} catch (\Exception $e) {
			echo "Ошибка: " . htmlspecialchars($e->getMessage());
		}
	}
}