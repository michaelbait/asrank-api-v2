<?php

/* @Twig/Exception/error500.html.twig */
class __TwigTemplate_2804ab8c9022144f566b918efcbc8d8991a39096932c4028d3713278fa5337ad extends Twig_Template
{
    private $source;

    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        // line 2
        $this->parent = $this->loadTemplate("base.html.twig", "@Twig/Exception/error500.html.twig", 2);
        $this->blocks = array(
            'body' => array($this, 'block_body'),
        );
    }

    protected function doGetParent(array $context)
    {
        return "base.html.twig";
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        $this->parent->display($context, array_merge($this->blocks, $blocks));
    }

    // line 4
    public function block_body($context, array $blocks = array())
    {
        // line 5
        echo "    <h1>Page not found!</h1>
    <p>
        Requested page not found!. Verify url or
        <a href=\"";
        // line 8
        echo $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("default");
        echo "\">return to main page</a>.
    </p>
";
    }

    public function getTemplateName()
    {
        return "@Twig/Exception/error500.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  40 => 8,  35 => 5,  32 => 4,  15 => 2,);
    }

    public function getSourceContext()
    {
        return new Twig_Source("", "@Twig/Exception/error500.html.twig", "/home/baitaluk/projects/asrank/web-server/templates/bundles/TwigBundle/Exception/error500.html.twig");
    }
}
