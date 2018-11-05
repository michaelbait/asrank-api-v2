<?php

/* navbar-logo.html.twig */
class __TwigTemplate_e8f3206924fc8341a5c2cf5cbe50a8795f9a290b41d3fa498df1738f23cec786 extends Twig_Template
{
    private $source;

    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        $this->parent = false;

        $this->blocks = array(
        );
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        // line 1
        echo "<a href=\"";
        echo $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("default");
        echo "\">
    <img src=\"";
        // line 2
        echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\AssetExtension']->getAssetUrl("images/asrank-logo-letters.svg"), "html", null, true);
        echo "\" class=\"img-fluid asrank-asrank-logo\">
    <img src=\"";
        // line 3
        echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\AssetExtension']->getAssetUrl("images/asrank-logo-ank.svg"), "html", null, true);
        echo "\" class=\"d-none d-sm-inline-block img-fluid asrank-asrank-logo\" style=\"margin-left:-2px\">
</a>
";
    }

    public function getTemplateName()
    {
        return "navbar-logo.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  32 => 3,  28 => 2,  23 => 1,);
    }

    public function getSourceContext()
    {
        return new Twig_Source("", "navbar-logo.html.twig", "/home/baitaluk/projects/asrank/web-server/templates/navbar-logo.html.twig");
    }
}
