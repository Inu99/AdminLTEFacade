<?php
namespace exface\AdminLteTemplate\Templates\Elements;

use exface\Core\Interfaces\Actions\ActionInterface;
use exface\Core\Templates\AbstractAjaxTemplate\Elements\JqueryLiveReferenceTrait;
use exface\Core\Templates\AbstractAjaxTemplate\Elements\JqueryDisableConditionTrait;

class lteInput extends lteValue
{
    
    use JqueryLiveReferenceTrait;
    use JqueryDisableConditionTrait;

    protected function init()
    {
        parent::init();
        $this->setElementType('text');
        // If the input's value is bound to another element via an expression, we need to make sure, that other element will
        // change the input's value every time it changes itself. This needs to be done on init() to make sure, the other element
        // has not generated it's JS code yet!
        $this->registerLiveReferenceAtLinkedElement();
        
        // Register an onChange-Script on the element linked by a disable condition.
        $this->registerDisableConditionAtLinkedElement();
    }

    function buildHtml()
    {
        $requiredScript = $this->getWidget()->isRequired() ? 'required="true" ' : '';
        $disabledScript = $this->getWidget()->isDisabled() ? 'disabled="disabled" ' : '';
        
        $output = <<<HTML

                        {$this->buildHtmlLabel()}
                        <input class="form-control"
                            type="{$this->getElementType()}"
                            name="{$this->getWidget()->getAttributeAlias()}" 
                            value="{$this->escapeString($this->getValueWithDefaults())}" 
                            id="{$this->getId()}"  
                            {$requiredScript}
                            {$disabledScript} />

HTML;
        return $this->buildHtmlGridItemWrapper($output);
    }

    /**
     * Returns the escaped and ready-to-use value of the widget including the default value (if applicable).
     * 
     * @return string
     */
    public function getValueWithDefaults()
    {
        return $this->escapeString($this->getWidget()->getValueWithDefaults());
    }

    function buildJs()
    {
        $output = '';
        
        if ($this->getWidget()->isRequired()) {
            $output .= $this->buildJsRequired();
        }
        
        $output .= $this->buildJsEventHandlers();
        
        return $output;
    }
    
    protected function buildJsEventHandlers()
    {
        $output .= $this->buildJsLiveReference();
        $output .= $this->buildJsOnChangeHandler();
        
        // Initialize the disabled state of the widget if a disabled condition is set.
        $output .= $this->buildJsDisableConditionInitializer();
        return $output;
    }

    /**
     * Returns a JavaScript-snippet, which highlights an invalid widget
     * (similiar to the JEasyUi-Template).
     *  
     * @return string
     */
    function buildJsRequired()
    {
        $output = <<<JS

    function {$this->buildJsFunctionPrefix()}validate() {
        if ({$this->buildJsValidator()}) {
            $("#{$this->getId()}").parent().removeClass("invalid");
        } else {
            $("#{$this->getId()}").parent().addClass("invalid");
        }
    }
    
    // Ueberprueft die Validitaet wenn das Element erzeugt wird.
    {$this->buildJsFunctionPrefix()}validate();
    // Ueberprueft die Validitaet wenn das Element geaendert wird.
    $("#{$this->getId()}").on("input change", function() {
        {$this->buildJsFunctionPrefix()}validate();
    });
JS;
        
        return $output;
    }

    /**
     *
     * {@inheritdoc}
     *
     * @see \exface\Core\Templates\AbstractAjaxTemplate\Elements\AbstractJqueryElement::buildJsDataGetter($action, $custom_body_js)
     */
    public function buildJsDataGetter(ActionInterface $action = null)
    {
        if ($this->getWidget()->isDisplayOnly()) {
            return '{}';
        } else {
            return parent::buildJsDataGetter($action);
        }
    }

    protected function buildJsOnChangeHandler()
    {
        $output = '';
        if ($this->getOnChangeScript()) {
            $output = <<<JS

$("#{$this->getId()}").on("input change", function() {
    {$this->getOnChangeScript()}
});
JS;
        }
        
        return $output;
    }

    /**
     * 
     * {@inheritDoc}
     * @see \exface\Core\Templates\AbstractAjaxTemplate\Elements\AbstractJqueryElement::buildJsValueSetter()
     */
    function buildJsValueSetter($value)
    {
        return '$("#' . $this->getId() . '").val(' . $value . ').trigger("change")';
    }

    /**
     * 
     * {@inheritDoc}
     * @see \exface\Core\Templates\AbstractAjaxTemplate\Elements\AbstractJqueryElement::buildJsValidator()
     */
    function buildJsValidator()
    {
        $widget = $this->getWidget();
        
        $must_be_validated = $widget->isRequired() && ! ($widget->isHidden() || $widget->isReadonly() || $widget->isDisabled() || $widget->isDisplayOnly());
        if ($must_be_validated) {
            $output = 'Boolean($("#' . $this->getId() . '").val())';
        } elseif ($widget->isRequired()) {
            $output = '(' . $this->buildJsValueGetter() . ' === "" ? false : true)';
        } else {
            $output = 'true';
        }
        
        return $output;
    }
}
?>