<?php
namespace Ynotz\AccessControl\Services;


use Ynotz\AccessControl\Models\Role;
use Ynotz\EasyAdmin\Traits\IsModelViewConnector;
use Ynotz\EasyAdmin\Contracts\ModelViewConnector;

class RoleService implements ModelViewConnector
{
    use IsModelViewConnector;

    public function __construct()
    {
        $this->modelClass = Role::class;
    }

    protected function getPageTitle(): string
    {
        return 'Roles';
    }

    protected function getIndexHeaders(): array
    {
        return [
            [
                'title' => 'name',
                'sort' => [
                    'key' => 'name'
                ],
                'search' => true,
                'search_label' => 'Search Users',
            ],
            [
                'title' => 'Action'
            ]
        ];
    }

    protected function getIndexColumns(): array
    {
        return [
            [
                'fields' => ['name'],
                'component' => 'text'
            ],
            [
                'edit_route' => 'users.edit',
                'component' => 'actions'
            ]
        ];
    }

    protected function getRelationQuery(int $id = null) {
        return null;
    }

    protected function accessCheck($item): bool
    {
        return true;
    }
}
?>
