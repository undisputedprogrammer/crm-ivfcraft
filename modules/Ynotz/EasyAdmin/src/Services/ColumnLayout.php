<?php
namespace Ynotz\EasyAdmin\Services;

class ColumnLayout extends LayoutElement
{
    public function __construct($width = "grow", $style = '')
    {
        parent::__construct($width, $style);
        $this->type = 'column';
        $this->style = $style;
    }
}
?>
