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
        // ... (implementace formuláře pro přidání/upravení příspěvku)
        return $form;
    }

    protected function getPostsForUser(): Nette\Database\Table\Selection
    {
        $userId = $this->getUser()->getId();

        if ($userId === null) {
            // Uživatel není přihlášen, můžete definovat chování pro nepřihlášeného uživatele
            return $this->facade->getPublicArticles();
        }

        if ($this->getUser()->isInRole('admin')) {
            return $this->facade->getPublicArticles(); // Všechny příspěvky pro admina
        } elseif ($this->getUser()->isInRole('redaktor')) {
            return $this->facade->getUserArticles($userId); // Příspěvky redaktora
        } else {
            return $this->facade->getPublicArticles(); // Všechny příspěvky pro ostatní uživatele
        }
    }

    public function renderDefault(): void
    {
        $this->template->posts = $this->getPostsForUser()->limit(20);
    }
}
