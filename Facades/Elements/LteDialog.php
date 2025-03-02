<?php
namespace exface\AdminLTEFacade\Facades\Elements;

use exface\Core\Widgets\Dialog;
use exface\Core\Interfaces\Widgets\iLayoutWidgets;
use exface\Core\Widgets\AbstractWidget;
use exface\Core\Interfaces\Widgets\iContainOtherWidgets;
use exface\Core\Interfaces\WidgetInterface;
use exface\Core\Interfaces\Widgets\iFillEntireContainer;

/**
 *
 * @method Dialog getWidget()
 *        
 * @author aka
 *        
 */
class LteDialog extends lteForm
{

    /**
     *
     * @return boolean
     */
    protected function isLazyLoading()
    {
        return $this->getWidget()->getLazyLoading(false);
    }
    
    function buildJs()
    {
        $output = '';
        if (! $this->isLazyLoading()) {
            $output .= $this->buildJsForWidgets();
        }
        $output .= $this->buildJsButtons();
        // Layout-Funktionen hinzufuegen
        $output .= $this->buildJsLayouterFunction();
        $output .= $this->buildJsLayouterOnShownFunction();
        // Masonry layout starten wenn der Dialog gezeigt wird
        $output .= <<<JS

    $("#{$this->getId()}").on("shown.bs.modal", function() {
        {$this->buildJsFunctionPrefix()}layouterOnShown();
    });
JS;
        
        return $output;
    }

    public function buildHtml()
    {
        $output = '';
        $dialogStyle = '';
        $bodyStyle = '';
        $widget = $this->getWidget();
        
        if ($widget->getWidth()->isRelative() || $widget->getWidth()->isPercentual()){
            $dialogStyle .= 'width: ' . $widget->getValue() . ';';
        }
        
        if ($widget->countWidgetsVisible() === 1) {
            $firstWidget = $widget->getWidgetFirst(function($w){return $w->isHidden() === false;});
            if ($firstWidget instanceof iFillEntireContainer) {
                $bodyStyle .= 'padding-top: 0; padding-bottom: 0;';
            }
        }
        
        if ($widget->hasHeader() === true) {
            $dialogHeader = $widget->getHeader();
            foreach ($dialogHeader->getWidgets() as $dhw) {
                $dialogHeaderContent .= $this->getFacade()->getElement($dhw)->buildHtml();
            }
            $headerHtml = <<<HTML
            
                <div class="exf-dialogheader">
                    <div class="">
                      <h5 class="box-title"><a href="javascript:;" data-toggle="collapse" data-target="#{$this->getId()}_header"><i class="fa fa-chevron-down"></i> {$dialogHeader->getCaption()}</a></h3>
                      <!-- /.box-tools -->
                    </div>
                    <!-- /.box-header -->
                    <div id="{$this->getId()}_header" class="collapse">
                      {$dialogHeaderContent}
                    </div>
                    <!-- /.box-body -->
                  </div>

HTML;
        }
        
        if (! $this->isLazyLoading()) {
            $output = <<<HTML

<div class="modal" id="{$this->getId()}">
    <div class="modal-dialog {$this->getWidthClasses()}" style="{$dialogStyle}">
        <div class="modal-content box">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">{$this->getWidget()->getCaption()}</h4>
                {$headerHtml}
            </div>
            <div class="modal-body" style="{$bodyStyle}">
                <div class="modal-body-content-wrapper row">
                    {$this->buildHtmlForWidgets()}
                </div>
            </div>
            <div class="modal-footer">
                {$this->buildHtmlToolbars()}
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
HTML;
        }
        return $output;
    }
    
    public function getWidthClasses()
    {
        $dim = $this->getWidget()->getWidth();   
        if ($dim->isUndefined() || $dim->isMax()) {
            return 'modal-lg';
        } elseif ($dim->isRelative()){
            if ($dim->getValue() >= 2){
                return 'modal-lg';
            } else {
                return ''; 
            }
        }
        return '';
    }

    /**
     * 
     * {@inheritDoc}
     * @see \exface\AdminLTEFacade\Facades\Elements\LteWidgetGrid::buildJsLayouterFunction()
     */
    protected function buildJsLayouterFunction() : string
    {
        $output = <<<JS

    function {$this->buildJsFunctionPrefix()}layouter() {}
JS;
        
        return $output;
    }

    /**
     * Returns a JavaScript-Function which layouts the dialog once it is visible.
     *
     * @return string
     */
    public function buildJsLayouterOnShownFunction()
    {
        $output = <<<JS

    function {$this->buildJsFunctionPrefix()}layouterOnShown() {
        {$this->getChildrenLayoutScript($this->getWidget())}
    }
JS;
        
        return $output;
    }

    /**
     * Returns a JavaScript-Snippet which layouts the children of the dialog.
     *
     * @param AbstractWidget $widget
     * @return string
     */
    protected function getChildrenLayoutScript(AbstractWidget $widget)
    {
        // Diese Funktion bewegt sich rekursiv durch den Baum und gibt Layout-Skripte fuer
        // alle Layout-Widgets zurueck.
        $output = '';
        if ($widget instanceof iContainOtherWidgets) {
            foreach ($widget->getWidgets() as $child) {
                $output .= $this->getChildrenLayoutScript($child);
            }
        }
        if ($widget instanceof iLayoutWidgets) {
            $output .= $this->getFacade()->getElement($widget)->buildJsLayouter() . ';';
        }
        return $output;
    }

    /**
     *
     * {@inheritdoc}
     *
     * @see \exface\AdminLTEFacade\Facades\Elements\LtePanel::getNumberOfColumnsByDefault()
     */
    public function getNumberOfColumnsByDefault() : int
    {
        return $this->getFacade()->getConfig()->getOption("WIDGET.DIALOG.COLUMNS_BY_DEFAULT");
    }

    /**
     *
     * {@inheritdoc}
     *
     * @see \exface\AdminLTEFacade\Facades\Elements\LtePanel::inheritsNumberOfColumns()
     */
    public function inheritsNumberOfColumns() : bool
    {
        return false;
    }
}
?>