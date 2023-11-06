<?php
namespace Ynotz\EasyAdmin\View\Composers;

use Illuminate\Contracts\View\View;
use Ynotz\EasyAdmin\Services\SidebarServiceInterface;

class SidebarComposer
{
    private $dataSource;

    public function __construct(SidebarServiceInterface $source)
    {
        $this->dataSource = $source;
    }

    public function compose(View $view)
    {
        $view->with([
            'sidebar_data' => $this->dataSource->getSidebardata()
        ]);
    }
}
?>
