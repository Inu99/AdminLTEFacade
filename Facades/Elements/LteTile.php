<?php
namespace exface\AdminLTEFacade\Facades\Elements;

use exface\Core\Widgets\Tile;
use exface\Core\Widgets\Container;

/**
 * Tile-widget for AdminLTE-Facade.
 * 
 * @author SFL
 *
 */
class LteTile extends lteButton
{
    private $cssColorClass = null;
    
    function buildHtml()
    {
        $widget = $this->getWidget();
        
        $icon_class = $widget->getIcon() && $widget->getShowIcon(true) ? $this->buildCssIconClass($widget->getIcon()) : '';
        
        return <<<JS
                <div class="{$this->getMasonryItemClass()} {$this->getWidthClasses()}"</div>
                    <div id="{$this->getId()}" class="small-box overlay-wrapper exf-tile {$this->buildCssColorClass($widget)}" style="{$this->buildCssElementStyle()}">
                        <div class="inner">
                            <h3>{$widget->getTitle()}</h3>
           					<p>{$widget->getSubtitle()}</p>
                		</div>
                		<div class="icon">
                			<i class="{$icon_class}"></i>
                		</div>
                		<a href="javascript:void(0)" onclick="{$this->buildJsClickFunctionName()}();" class="small-box-footer">Start <i class="fa fa-arrow-circle-right"></i></a>
        			</div>
                </div>
JS;
    }
       
    /**
     * 
     * @param Tile $widget
     * @return string
     */
    public function buildCssColorClass(Tile $widget) : string
    {
        if ($widget->getColor() !== null) {
            return '';
        }
        
        if ($this->cssColorClass !== null) {
            return $this->cssColorClass;
        }
        
        $container = $widget->getParent();
        if ($container instanceof Container) {
            $idx = $container->getWidgetIndex($widget);
        } else {
            $idx = 0;
        }
        
        return $this->getFacade()->getConfig()->getOption('WIDGET.TILE.AUTOCOLORS')->getProperty($idx);
    }
    
    public function setCssColorClass(string $class) : lteTile
    {
        $this->cssColorClass = $class;
        return $this;
    }
    
    /**
     * 
     * {@inheritDoc}
     * @see \exface\Core\Facades\AbstractAjaxFacade\Elements\AbstractJqueryElement::buildCssElementStyle()
     */
    public function buildCssElementStyle()
    {
        $style = '';
        $bgColor = $this->getWidget()->getColor();
        if ($bgColor !== null && $bgColor !== '') {
            $style .= 'background-color:' . $bgColor . ';';
        }
        return $style;
    }
}
