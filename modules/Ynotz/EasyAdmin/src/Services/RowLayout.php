<?php
namespace Ynotz\EasyAdmin\Services;

class RowLayout extends LayoutElement
{
    public function __construct($width = 'grow', $style = '')
    {
        parent::__construct($width, $style);
        $this->type = 'row';
        $this->style = $style;
    }
}
?>
