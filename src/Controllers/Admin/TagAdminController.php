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
use Controllers\Admin\AdminBaseController;

use Core\Services\TransliterateService;


class TagAdminController extends AdminBaseController
{
  
  private AdminService $adminService;
  private TagService $tagService;

  private RatingService $ratingService;
  private ProductService $productService;

  public function __construct()
  {
    parent::__construct();

    $database = new MySQLDatabase();
    $pdo = $database->getConnection();

    $this->adminService = new AdminService(new AdminRepository($pdo));
    $this->tagService = new TagService(new TagRepository($pdo));
    $this->productService = new ProductService(new ProductRepository($pdo),new RatingRepository($pdo));
  }


  public function index(): void
  {

    try 
    {
      $currentPage = max(1, (int)($_GET['page'] ?? 1));

      $tags = $this->tagService->getPaginatedTags($currentPage, BY_RATING_OR_TAG_ITEMS_PER_PAGE_ADMIN);

      $totalPages = $this->tagService->getTotalPages(BY_RATING_OR_TAG_ITEMS_PER_PAGE_ADMIN);

      $this->render
      (
        'admin/tags/index',
      [
          'tags' => $tags,
          'totalPages' => $totalPages,
          'currentPage' => $currentPage,
        ]  
      );
    } 
    catch (\Exception $e) 
    {
      echo "Ошибка: " . htmlspecialchars($e->getMessage());
    }

  }

  public function create(): void
  {

    if ($_SERVER['REQUEST_METHOD'] === 'POST') 
    {
      $name = trim($_POST['name'] ?? '');
      if ($name === '') 
      {
        echo "Ошибка: Название тега не может быть пустым.";
        return;
      }

      if ($this->tagService->isTagNameExists($name)) 
      {
        echo "Ошибка: Тег с таким названием уже существует.";
        return;
      }

      try 
      {
        $this->tagService->createTag($name);

        $tagId = $this->tagService->getLastCreatedTagId();

        if (isset($_POST['products']) && is_array($_POST['products'])) 
        {
          foreach ($_POST['products'] as $productId) 
          {
            $this->tagService->addTagToProduct((int)$productId, $tagId);
          }
        }

        $this->redirect('/admin/tags');
      } 
      catch (\Exception $e) 
      {
        echo "Ошибка: " . htmlspecialchars($e->getMessage());
      }
    }

    $products = $this->productService->getAllProducts();

    $this->render
    (
      'admin/tags/add_tag',
      [
        'products' => $products,
      ]
    );
  }

  public function edit(int $id): void
  {

    $tag = $this->tagService->findTagById($id);

    if (!$tag) 
    {
      echo "Тег не найден.";
      return;
    }

    $this->render
    (
      'admin/tags/detail',
      [
        'tag' => $tag,
      ]
    );
  }

  public function process(): void
  {

    if ($_SERVER['REQUEST_METHOD'] !== 'POST')
    {
      $this->redirect('/admin/tags');
    }

    $action = $_POST['action'] ?? '';
    $selectedTags = $_POST['selected_tags'] ?? [];

    if (empty($selectedTags) && in_array($action, ['activate', 'deactivate'])) 
    {
      $this->redirect('/admin/tags?error=no_tags_selected');
      return;
    }

    try 
    {
      switch ($action) 
      {
        case 'activate':
          $this->tagService->toggleTagsStatus($selectedTags, true);
          break;

        case 'deactivate':
          $this->tagService->toggleTagsStatus($selectedTags, false);
          break;

        default:
          throw new \Exception('Неизвестное действие');
      }

      $this->redirect('/admin/tags');
    } 
    catch (\Exception $e) 
    {
      echo "Ошибка: " . htmlspecialchars($e->getMessage());
    }

  }
  public function store(): void
  {
    
    $name = trim($_POST['name'] ?? '');

    if ($name === '') 
    {
      echo "Ошибка: Название тега не может быть пустым.";
      return;
    }

    if ($this->tagService->isTagNameExists($name))
    {
      echo "Ошибка: Тег с таким названием уже существует.";
      return;
    }


    try 
    {
      $this->tagService->createTag($name);

      if (isset($_POST['products']) && is_array($_POST['products'])) 
      {
        $tagId = $this->tagService->getLastCreatedTagId();

        foreach ($_POST['products'] as $productId) 
        {
          $this->tagService->addTagToProduct((int)$productId, $tagId);
        }
      }

      $this->redirect('/admin/tags');
    } 
    catch (\Exception $e) 
    {
      echo "Ошибка: " . htmlspecialchars($e->getMessage());
    }

  }

  public function update(int $id): void
  {

    $newName = trim($_POST['name'] ?? '');
    $isActive = isset($_POST['is_active']) && $_POST['is_active'] === 'on';

    if ($newName === '') 
    {
      echo "Ошибка: Название тега не может быть пустым.";
      return;
    }

    try 
    {
      $this->tagService->updateTagWithStatus($id, $newName, $isActive);

      $this->redirect('/admin/tags');
    } 
    catch (\Exception $e) 
    {
      echo "Ошибка: " . htmlspecialchars($e->getMessage());
    }

  }
  
}
