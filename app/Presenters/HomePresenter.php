<?php
namespace App\Presenters;

use App\Model\PostFacade;
use Nette;

final class HomePresenter extends Nette\Application\UI\Presenter
{
    private PostFacade $facade;

    public function __construct(PostFacade $facade)
    {
        $this->facade = $facade;
    }

    protected function createComponentPostForm(): Nette\Application\UI\Form
    {
        $form = new Nette\Application\UI\Form;

        return $form;
    }

    protected function getPostsForUser(): Nette\Database\Table\Selection
    {
        $userId = $this->getUser()->getId();

        if ($userId === null) {
            return $this->facade->getPublicArticles();
        }

        if ($this->getUser()->isInRole('admin')) {
            return $this->facade->getPublicArticles();
        } elseif ($this->getUser()->isInRole('redaktor')) {
            return $this->facade->getUserArticles($userId);
        } else {
            return $this->facade->getPublicArticles();
        }
    }

    public function renderDefault(): void
    {
        $this->template->posts = $this->getPostsForUser()->limit(20);
    }
}
