<?php

/* asns/orgs.html.twig */
class __TwigTemplate_e1d2736f994cc61571f99ffe9ef30939319e2c3e685cede1bdcca03e65934a2c extends Twig_Template
{
    private $source;

    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        // line 1
        $this->parent = $this->loadTemplate("base.html.twig", "asns/orgs.html.twig", 1);
        $this->blocks = array(
            'title' => array($this, 'block_title'),
            'body' => array($this, 'block_body'),
            'datasources' => array($this, 'block_datasources'),
            'javascripts' => array($this, 'block_javascripts'),
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

    // line 3
    public function block_title($context, array $blocks = array())
    {
        echo ": A ranking of the largest Autonomous Systems (AS) in the Internet.";
    }

    // line 5
    public function block_body($context, array $blocks = array())
    {
        echo " 
    ";
        // line 6
        $context["name"] = "";
        echo " ";
        $this->loadTemplate("asns/asn_search_form.html.twig", "asns/orgs.html.twig", 6)->display($context);
        // line 7
        echo "    <div id=\"orgs_table\" class=\"ds_table\">
    </div>
";
    }

    // line 11
    public function block_datasources($context, array $blocks = array())
    {
        echo " ";
        $this->loadTemplate("data-sources.html.twig", "asns/orgs.html.twig", 11)->display($context);
        echo " ";
    }

    // line 13
    public function block_javascripts($context, array $blocks = array())
    {
        // line 14
        echo "    ";
        $this->displayParentBlock("javascripts", $context, $blocks);
        echo "
    <script>
        ";
        // line 17
        echo "        RankUtil.build_info_table('";
        echo "orgs";
        echo "','";
        echo false;
        echo "', ";
        echo json_encode(($context["params"] ?? null));
        echo ");
    </script>
";
    }

    public function getTemplateName()
    {
        return "asns/orgs.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  73 => 17,  67 => 14,  64 => 13,  56 => 11,  50 => 7,  46 => 6,  41 => 5,  35 => 3,  15 => 1,);
    }

    public function getSourceContext()
    {
        return new Twig_Source("", "asns/orgs.html.twig", "/home/baitaluk/projects/asrank/web-server/templates/asns/orgs.html.twig");
    }
}
