<?php

/* asns/asn.html.twig */
class __TwigTemplate_0879a6f52a936e00c4e585d3cbb7b3af248e6fb8ce6a7883908a1e203e1cf5c2 extends Twig_Template
{
    private $source;

    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        // line 1
        $this->parent = $this->loadTemplate("base.html.twig", "asns/asn.html.twig", 1);
        $this->blocks = array(
            'title' => array($this, 'block_title'),
            'description' => array($this, 'block_description'),
            'jsonld' => array($this, 'block_jsonld'),
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
        // line 3
        $context["asn_info"] = twig_get_attribute($this->env, $this->source, ($context["params"] ?? null), "asn_info", array());
        // line 4
        $context["location"] = twig_get_attribute($this->env, $this->source, ($context["params"] ?? null), "location", array());
        // line 1
        $this->parent->display($context, array_merge($this->blocks, $blocks));
    }

    // line 6
    public function block_title($context, array $blocks = array())
    {
        $this->displayParentBlock("title", $context, $blocks);
        echo ": AS";
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["asn_info"] ?? null), "asn", array()), "html", null, true);
        echo " ";
        if (twig_get_attribute($this->env, $this->source, ($context["asn_info"] ?? null), "get_name", array(), "method")) {
            echo " (";
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["asn_info"] ?? null), "get_name", array(), "method"), "html", null, true);
            echo ") ";
        }
    }

    // line 8
    public function block_description($context, array $blocks = array())
    {
        echo "AS Rank:";
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["asn_info"] ?? null), "rank", array()), "html", null, true);
        echo " Customer Cone:";
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["asn_info"] ?? null), "cone", array()), "asns", array()), "html", null, true);
        echo " Transit Degree:";
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["asn_info"] ?? null), "degree", array()), "transits", array()), "html", null, true);
    }

    // line 9
    public function block_jsonld($context, array $blocks = array())
    {
        echo "<script type=\"application/ld+json\"> ";
        echo twig_get_attribute($this->env, $this->source, ($context["asn_info"] ?? null), "get_json_ld", array());
        echo "</script> ";
    }

    // line 13
    public function block_body($context, array $blocks = array())
    {
        // line 14
        echo "    ";
        $context["name"] = twig_get_attribute($this->env, $this->source, ($context["asn_info"] ?? null), "asn", array());
        echo " ";
        $this->loadTemplate("asns/asn_search_form.html.twig", "asns/asn.html.twig", 14)->display($context);
        // line 15
        echo "
     <div class=\"row\">
        ";
        // line 17
        $this->loadTemplate("as-core.html.twig", "asns/asn.html.twig", 17)->display($context);
        // line 18
        echo "        ";
        $this->loadTemplate("asns/asn-information-table.html.twig", "asns/asn.html.twig", 18)->display($context);
        // line 19
        echo "    </div>
    ";
        // line 20
        if ((twig_get_attribute($this->env, $this->source, ($context["location"] ?? null), "area", array()) == "as-core")) {
            // line 21
            echo "        ";
            $context["as_core_size"] = "700";
            // line 22
            echo "        ";
            $this->loadTemplate("as-core.html.twig", "asns/asn.html.twig", 22)->display($context);
            // line 23
            echo "    ";
        } elseif ((twig_get_attribute($this->env, $this->source, ($context["location"] ?? null), "area", array()) == "neighbors")) {
            // line 24
            echo "        <div class=\"asrank-asn-neighbors-div\" id=\"asn_neighbors_table\"> </div>
    ";
        }
    }

    // line 28
    public function block_datasources($context, array $blocks = array())
    {
        echo " ";
        $this->loadTemplate("data-sources.html.twig", "asns/asn.html.twig", 28)->display($context);
        echo " ";
    }

    // line 30
    public function block_javascripts($context, array $blocks = array())
    {
        // line 31
        echo "    ";
        $this->displayParentBlock("javascripts", $context, $blocks);
        echo "
    <script>
        ";
        // line 33
        if ((twig_get_attribute($this->env, $this->source, ($context["location"] ?? null), "area", array()) == "as-core")) {
            // line 34
            echo "        ";
        } elseif ((twig_get_attribute($this->env, $this->source, ($context["location"] ?? null), "area", array()) == "neighbors")) {
            // line 35
            echo "            ";
            // line 36
            echo "            RankUtil.build_info_table('";
            echo "asn_neighbors";
            echo "','";
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["asn_info"] ?? null), "asn", array()), "html", null, true);
            echo "', ";
            echo json_encode(($context["params"] ?? null));
            echo ");
        ";
        }
        // line 38
        echo "    </script>
";
    }

    public function getTemplateName()
    {
        return "asns/asn.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  146 => 38,  136 => 36,  134 => 35,  131 => 34,  129 => 33,  123 => 31,  120 => 30,  112 => 28,  106 => 24,  103 => 23,  100 => 22,  97 => 21,  95 => 20,  92 => 19,  89 => 18,  87 => 17,  83 => 15,  78 => 14,  75 => 13,  67 => 9,  56 => 8,  42 => 6,  38 => 1,  36 => 4,  34 => 3,  15 => 1,);
    }

    public function getSourceContext()
    {
        return new Twig_Source("", "asns/asn.html.twig", "/home/baitaluk/projects/asrank/web-server/templates/asns/asn.html.twig");
    }
}
