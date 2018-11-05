<?php

/* asns/org.html.twig */
class __TwigTemplate_01bd47b9f48e06c4d22482151992b7707bd47096aabe1abc696dc5ba934244e8 extends Twig_Template
{
    private $source;

    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        // line 1
        $this->parent = $this->loadTemplate("base.html.twig", "asns/org.html.twig", 1);
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
        $context["org_info"] = twig_get_attribute($this->env, $this->source, ($context["params"] ?? null), "org_info", array());
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
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["org_info"] ?? null), "name", array()), "html", null, true);
        echo " ";
        if (twig_get_attribute($this->env, $this->source, ($context["org_info"] ?? null), "get_name", array(), "method")) {
            echo " (";
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["org_info"] ?? null), "get_name", array(), "method"), "html", null, true);
            echo ") ";
        }
    }

    // line 8
    public function block_description($context, array $blocks = array())
    {
        echo "AS Rank:";
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["org_info"] ?? null), "rank", array()), "html", null, true);
        echo " Customer Cone Asns:";
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["org_info"] ?? null), "cone", array()), "asns", array()), "html", null, true);
        echo " Org Transit Degree:";
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["org_info"] ?? null), "degree", array()), "org", array()), "transit", array()), "html", null, true);
    }

    // line 9
    public function block_jsonld($context, array $blocks = array())
    {
        echo "<script type=\"application/ld+json\"> ";
        echo twig_get_attribute($this->env, $this->source, ($context["org_info"] ?? null), "get_json_ld", array());
        echo "</script> ";
    }

    // line 13
    public function block_body($context, array $blocks = array())
    {
        // line 14
        echo "    ";
        $context["name"] = twig_get_attribute($this->env, $this->source, ($context["org_info"] ?? null), "name", array());
        echo " ";
        $this->loadTemplate("asns/asn_search_form.html.twig", "asns/org.html.twig", 14)->display($context);
        // line 15
        echo "
<div class=\"row\">
        ";
        // line 17
        $this->loadTemplate("as-core.html.twig", "asns/org.html.twig", 17)->display($context);
        // line 18
        echo "    <div class=\"asrank-asn-info-div\">
    <table class=\"asrank-info-table table-condensed\">
        <tr><th>Org name</th><td colspan=\"7\">";
        // line 20
        echo twig_get_attribute($this->env, $this->source, ($context["org_info"] ?? null), "name", array());
        echo "</td></tr>
        <tr><th>country</th><td colspan=\"7\">
                ";
        // line 22
        if (twig_get_attribute($this->env, $this->source, ($context["org_info"] ?? null), "country_name", array(), "any", true, true)) {
            // line 23
            echo "                    ";
            echo twig_get_attribute($this->env, $this->source, ($context["org_info"] ?? null), "country_name", array());
            echo " <span class=\"flag-icon flag-icon-";
            echo twig_escape_filter($this->env, twig_lower_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["org_info"] ?? null), "country", array())), "html", null, true);
            echo "\"></span>
                ";
        } else {
            // line 25
            echo "                    <span class=\"asrank-unknown\">unknown</span>
                ";
        }
        // line 27
        echo "            </td></tr>
        <tr><th>Org rank</th><td colspan=\"7\">";
        // line 28
        echo twig_get_attribute($this->env, $this->source, ($context["org_info"] ?? null), "rank", array());
        echo "</td></tr>
        <tr><th>Customer Cone </th>
            <td class=\"asrank-info-table-sub_facts\" data-toggle=\"tooltip\" data-placement=\"top\" title=\"The number of ASNs that are observed to be in the selected ASN's customer cone.\"> 
                ";
        // line 31
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["org_info"] ?? null), "cone", array()), "orgs", array()), "html", null, true);
        echo "<br>
                <span>orgs</span>
            </td>
            <td class=\"asrank-info-table-sub_facts\" data-toggle=\"tooltip\" data-placement=\"top\" title=\"The number of ASNs that are observed to be in the selected ASN's customer cone.\"> 
                ";
        // line 35
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["org_info"] ?? null), "cone", array()), "asns", array()), "html", null, true);
        echo "<br>
                <span>asns</span>
            </td>
            <td class=\"asrank-info-table-sub_facts\" data-toggle=\"tooltip\" data-placement=\"top\" title=\"The number of prefixes that are observed to be in the selected ASN's customer cone.\"> 
                ";
        // line 39
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["org_info"] ?? null), "cone", array()), "prefixes", array()), "html", null, true);
        echo "<br>
                <span>prefixes</span>
            </td>
            <td class=\"asrank-info-table-sub_facts\" data-toggle=\"tooltip\" data-placement=\"top\" title=\"The number of addresses that are observed to be in the selected ASN's customer cone.\"> 
                ";
        // line 43
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["org_info"] ?? null), "cone", array()), "addresses", array()), "html", null, true);
        echo "<br>
                <span>addresses</span>
            </td>
        </tr>
        <tr><th>ASN degree</th>
            <td class=\"asrank-info-table-sub_facts\" data-toggle=\"tooltip\" data-placement=\"top\" title=\"The number of ASNs that were observed as neighbors of the selected ASN in a path.\"> 
                ";
        // line 49
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["org_info"] ?? null), "degree", array()), "org", array()), "transit", array()), "html", null, true);
        echo "<br>
                <span>org transit</span>
            </td>
            <td class=\"asrank-info-table-sub_facts\" data-toggle=\"tooltip\" data-placement=\"top\" title=\"The number of ASNs that were observed as neighbors of the selected ASN in a path.\"> 
                ";
        // line 53
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["org_info"] ?? null), "degree", array()), "org", array()), "global", array()), "html", null, true);
        echo "<br>
                <span>org global</span>
            </td>
            <td class=\"asrank-info-table-sub_facts\" data-toggle=\"tooltip\" data-placement=\"top\" title=\"The number of ASNs that where observed as neighbors of the selected ASN in a path, where the selected ASN was between, i.e. providing transit, for two other ASNs.\"> 
                ";
        // line 57
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["org_info"] ?? null), "degree", array()), "asn", array()), "transit", array()), "html", null, true);
        echo "<br>
                <span>asn transit </span>
            </td>
            <td class=\"asrank-info-table-sub_facts\" data-toggle=\"tooltip\" data-placement=\"top\" title=\"The number of ASNs that are providers of the selected ASN.\"> 
                ";
        // line 61
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["org_info"] ?? null), "degree", array()), "asn", array()), "global", array()), "html", null, true);
        echo "<br>
                <span>asn global</span>
            </td>
        </tr>
        <tr><th>ASN members</th>
            <td class=\"asrank-info-table-sub_facts\" data-toggle=\"tooltip\" data-placement=\"top\" title=\"The number of member ASNs that where seen in BGP.\"> 
                ";
        // line 67
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["org_info"] ?? null), "number_members_ranked", array()), "html", null, true);
        echo "<br>
                <span>observed</span>
            </td>
            <td class=\"asrank-info-table-sub_facts\" data-toggle=\"tooltip\" data-placement=\"top\" title=\"The total number of member ASNs.\"> 
                ";
        // line 71
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["org_info"] ?? null), "number_members", array()), "html", null, true);
        echo "<br>
                <span>total</span>
            </td>
        </tr>
    </table>
    </div>
</div>
    ";
        // line 78
        if ((twig_get_attribute($this->env, $this->source, ($context["location"] ?? null), "area", array()) == "as-core")) {
            // line 79
            echo "        ";
            $context["as_core_size"] = "700";
            // line 80
            echo "        ";
            $this->loadTemplate("as-core.html.twig", "asns/org.html.twig", 80)->display($context);
            // line 81
            echo "    ";
        } elseif ((twig_get_attribute($this->env, $this->source, ($context["location"] ?? null), "area", array()) == "links")) {
            // line 82
            echo "        <div class=\"asrank-asn-links-div\" id=\"asn_links_table\"> </div>
    ";
        }
        // line 84
        echo "    <div class=\"asrank-asn-links-div\" id=\"org_members_table\"> </div>
";
    }

    // line 87
    public function block_datasources($context, array $blocks = array())
    {
        echo " ";
        $this->loadTemplate("data-sources.html.twig", "asns/org.html.twig", 87)->display($context);
        echo " ";
    }

    // line 89
    public function block_javascripts($context, array $blocks = array())
    {
        // line 90
        echo "    ";
        $this->displayParentBlock("javascripts", $context, $blocks);
        echo "
    <script>
        ";
        // line 92
        if ((twig_get_attribute($this->env, $this->source, ($context["location"] ?? null), "area", array()) == "members")) {
            // line 93
            echo "            ";
            // line 94
            echo "            RankUtil.build_info_table('";
            echo "org_members";
            echo "','";
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["org_info"] ?? null), "id", array()), "html", null, true);
            echo "', ";
            echo json_encode(($context["params"] ?? null));
            echo ");
        ";
        }
        // line 96
        echo "    </script>
";
    }

    public function getTemplateName()
    {
        return "asns/org.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  249 => 96,  239 => 94,  237 => 93,  235 => 92,  229 => 90,  226 => 89,  218 => 87,  213 => 84,  209 => 82,  206 => 81,  203 => 80,  200 => 79,  198 => 78,  188 => 71,  181 => 67,  172 => 61,  165 => 57,  158 => 53,  151 => 49,  142 => 43,  135 => 39,  128 => 35,  121 => 31,  115 => 28,  112 => 27,  108 => 25,  100 => 23,  98 => 22,  93 => 20,  89 => 18,  87 => 17,  83 => 15,  78 => 14,  75 => 13,  67 => 9,  56 => 8,  42 => 6,  38 => 1,  36 => 4,  34 => 3,  15 => 1,);
    }

    public function getSourceContext()
    {
        return new Twig_Source("", "asns/org.html.twig", "/home/baitaluk/projects/asrank/web-server/templates/asns/org.html.twig");
    }
}
