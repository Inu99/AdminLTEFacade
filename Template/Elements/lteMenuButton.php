<?php
namespace exface\AdminLteTemplate\Template\Elements;

use exface\Core\Widgets\Button;
use exface\Core\Templates\AbstractAjaxTemplate\Elements\JqueryButtonTrait;
use exface\Core\Widgets\MenuButton;

/**
 * generates jQuery Mobile buttons for ExFace
 *
 * @method MenuButton getWidget()
 * 
 * @author Andrej Kabachnik
 *        
 */
class lteMenuButton extends lteAbstractElement
{
    
    use JqueryButtonTrait;

    /**
     * 
     * {@inheritDoc}
     * @see \exface\Core\Templates\AbstractAjaxTemplate\Elements\AbstractJqueryElement::buildHtml()
     */
    function buildHtml()
    {
        $output = '';
        
        $icon = ($this->getWidget()->getIcon() ? '<i class="' . $this->buildCssIconClass($this->getWidget()->getIcon()) . '"></i> ' : '');
        
        $button_class = $this->getWidget()->getVisibility() == EXF_WIDGET_VISIBILITY_PROMOTED ? ' btn-primary' : ' btn-default';
        $align_class = $this->getAlignClass();
        
        $output .= <<<HTML
 
<div class="dropdown {$align_class}">
<button type="button" class="btn dropdown-toggle{$button_class}" data-toggle="dropdown">{$icon}{$this->getWidget()->getCaption()} <span class="caret"></span></button>
	<ul class="dropdown-menu" role="menu">
		{$this->getTemplate()->getElement($this->getWidget()->getMenu())->buildHtmlButtons()}
	</ul>
</div>
HTML;
        
        return $output;
    }

    /**
     * {@inheritdoc}
     *
     * @see \exface\AdminLteTemplate\Template\Elements\lteAbstractElement::buildJs()
     */
    function buildJs()
    {
        $output = '';
        foreach ($this->getWidget()->getButtons() as $b) {
            if ($js_click_function = $this->getTemplate()->getElement($b)->buildJsClickFunction()) {
                $output .= "
					function " . $this->getTemplate()->getElement($b)->buildJsClickFunctionName(). "(){
						" . $js_click_function . "
					}
					";
            }
        }
        return $output;
    }

    function getAlignClass()
    {
        $align = $this->getWidget()->getAlign();
        if ($align == 'left') {
            $align_class = ' pull-left';
        } elseif ($align == 'right') {
            $align_class = ' pull-right';
        } else {
            $align_class = '';
        }
        return $align_class;
    }
}
?>