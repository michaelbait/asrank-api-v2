<?php

/* asns/asn_search_form.html.twig */
class __TwigTemplate_7502939d968ef69f0fe8cf0784e64c97e5c62de0b3ef2be23d027269f939af5a extends Twig_Template
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
        echo "    <form action=\"";
        echo $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("asn_search", array("asn" => null));
        echo "\">
        <div class=\"asrank-asn-search-form-div input-group\"
            <label for=\"asn_search\"></label>
            <input type=\"text\" class=\"form-control\" value=\"";
        // line 4
        echo twig_escape_filter($this->env, ($context["name"] ?? null), "html", null, true);
        echo "\" name=\"name\"
            placeholder=\"ASN name or number\">
            <button type=\"search\" name=\"type\" value=\"search\" class=\"btn btn-primary\" style=\"margin-left:.2em;\">search</button>
            <button type=\"search\" name=\"type\" value=\"go to\" class=\"btn btn-primary\" style=\"margin-left:.2em;\">go to</button>
        </div>
    </form>
";
    }

    public function getTemplateName()
    {
        return "asns/asn_search_form.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  30 => 4,  23 => 1,);
    }

    public function getSourceContext()
    {
        return new Twig_Source("", "asns/asn_search_form.html.twig", "/home/baitaluk/projects/asrank/web-server/templates/asns/asn_search_form.html.twig");
    }
}
