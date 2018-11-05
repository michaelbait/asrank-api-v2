<?php

/* asns/asns.html.twig */
class __TwigTemplate_4145f73845b8cf81ad2e08abe2fd2f5dd7a90997e0c8815ec2b361b4d6c29655 extends Twig_Template
{
    private $source;

    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        // line 1
        $this->parent = $this->loadTemplate("base.html.twig", "asns/asns.html.twig", 1);
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
        if ((twig_get_attribute($this->env, $this->source, ($context["params"] ?? null), "top_ten", array()) == true)) {
            // line 7
            echo "        <div class=\"row\">
            <div class=\"col-sm-12\" style=\"margin-top:1em;\">
                <img class=\"as-core-small\" src='images/as-core.png'/>
                <img src=\"";
            // line 10
            echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\AssetExtension']->getAssetUrl("images/asrank-logo-letters.svg"), "html", null, true);
            echo "\" class=\"img-fluid asrank-asrank-logo-small\">
                <img src=\"";
            // line 11
            echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\AssetExtension']->getAssetUrl("images/asrank-logo-ank.svg"), "html", null, true);
            echo "\" class=\"img-fluid asrank-asrank-logo-small\" style=\"margin-left:-2px\">
                is CAIDA's ranking of
                <a href=\"http://en.wikipedia.org/wiki/Autonomous_system_%28Internet%29\">
                Autonomous Systems (AS)</a> (which approximately map to Internet Service
                Providers) and organizations (Orgs) (which are a collection of one or more ASes).
                This ranking is derived from topological data collected by
                CAIDA's <a href=\"http://www.caida.org/projects/ark/\">  Archipelago
                Measurement Infrastructure </a> and
                <a href=\"http://en.wikipedia.org/wiki/Border_Gateway_Protocol\"> Border Gateway
                Protocol (BGP)</a> routing data collected by the
                <a href=\"http://www.routeviews.org/\">Route Views Project</a>
                and <a href=\"http://www.ripe.net/\">RIPE NCC</a>.
                </p>

                <p>ASes and Orgs are ranked by their
                <a href=\"http://as-rank.caida.org/?mode0=as-intro#customer-cone\">customer cone size</a>,
                which is the number of their direct and indirect customers.
                Note:  We do <i>not</i> have data to rank ASes (ISPs) by traffic,
                revenue, users, or any other non-topological metric.
                </p>
                ";
            // line 31
            $context["name"] = "";
            echo " ";
            $this->loadTemplate("asns/asn_search_form.html.twig", "asns/asns.html.twig", 31)->display($context);
            // line 32
            echo "            </div>
        </div>
    ";
        } else {
            // line 35
            echo "        ";
            $context["name"] = "";
            echo " ";
            $this->loadTemplate("asns/asn_search_form.html.twig", "asns/asns.html.twig", 35)->display($context);
            // line 36
            echo "    ";
        }
        // line 37
        echo "    <div id=\"asns_table\" class=\"ds_table\">
    </div>
";
    }

    // line 41
    public function block_datasources($context, array $blocks = array())
    {
        echo " ";
        $this->loadTemplate("data-sources.html.twig", "asns/asns.html.twig", 41)->display($context);
        echo " ";
    }

    // line 43
    public function block_javascripts($context, array $blocks = array())
    {
        // line 44
        echo "    ";
        $this->displayParentBlock("javascripts", $context, $blocks);
        echo "
    <script> 
        ";
        // line 46
        if ((twig_get_attribute($this->env, $this->source, ($context["params"] ?? null), "top_ten", array()) == true)) {
            // line 47
            echo "            ";
            $context["table"] = "asns_top_ten";
            // line 48
            echo "        ";
        } else {
            // line 49
            echo "            ";
            $context["table"] = "asns";
            // line 50
            echo "        ";
        }
        // line 51
        echo "        RankUtil.build_info_table('";
        echo twig_escape_filter($this->env, ($context["table"] ?? null), "html", null, true);
        echo "','";
        echo false;
        echo "', ";
        echo json_encode(($context["params"] ?? null));
        echo ");
        ";
        // line 53
        echo "    </script>
";
    }

    public function getTemplateName()
    {
        return "asns/asns.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  143 => 53,  134 => 51,  131 => 50,  128 => 49,  125 => 48,  122 => 47,  120 => 46,  114 => 44,  111 => 43,  103 => 41,  97 => 37,  94 => 36,  89 => 35,  84 => 32,  80 => 31,  57 => 11,  53 => 10,  48 => 7,  46 => 6,  41 => 5,  35 => 3,  15 => 1,);
    }

    public function getSourceContext()
    {
        return new Twig_Source("", "asns/asns.html.twig", "/home/baitaluk/projects/asrank/web-server/templates/asns/asns.html.twig");
    }
}
