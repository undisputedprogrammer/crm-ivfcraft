<?php
namespace Ynotz\EasyAdmin\Contracts;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;

interface ModelViewConnector
{
    public function index(
        int $itemsCount,
        int $page,
        array $searches,
        array $sorts,
        array $filters,
        array $advParams,
        bool $indexMode,
        string $selectedIds,
        string $resultsName,
    ): array;

    public function indexDownload(
        array $searches,
        array $sorts,
        array $filters,
        array $advParams,
        string $selectedIds
    ): array;

    public function getIdsForParams(
        array $searches,
        array $sorts,
        array $filters,
    ): array;

    public function suggestlist(Request $request);

    public function getDownloadCols(): array;

    public function getCreatePageData(): array;

    public function getStoreValidationRules(): array;

}
?>
