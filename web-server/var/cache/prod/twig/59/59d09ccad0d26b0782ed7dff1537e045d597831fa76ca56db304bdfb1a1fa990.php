<?php

/* asns/asn-information-table.html.twig */
class __TwigTemplate_7553fe30c239eea1f9f2a8e740132b7f6ca210e2171c45e1048651394b918fe4 extends Twig_Template
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
        echo "<div class=\"asrank-asn-info-div\">
<table class=\"asrank-info-table table-condensed\" border=\"0\">
    <tr><th>AS number</th><td colspan=\"7\">";
        // line 3
        echo twig_get_attribute($this->env, $this->source, ($context["asn_info"] ?? null), "asn", array());
        echo "</td></tr>
    <tr><th>AS name</th><td colspan=\"7\">";
        // line 4
        echo twig_get_attribute($this->env, $this->source, ($context["asn_info"] ?? null), "name", array());
        echo "</td></tr>
    <tr><th>organization</th><td colspan=\"7\"><a href=\"";
        // line 5
        echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("org_members", array("org" => twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["asn_info"] ?? null), "org", array()), "id", array()))), "html", null, true);
        echo "\">";
        echo twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["asn_info"] ?? null), "org", array()), "name", array());
        echo "</a></td></tr>
    <tr><th>country</th><td colspan=\"7\">
            ";
        // line 7
        if (twig_get_attribute($this->env, $this->source, ($context["asn_info"] ?? null), "country_name", array(), "any", true, true)) {
            // line 8
            echo "                ";
            echo twig_get_attribute($this->env, $this->source, ($context["asn_info"] ?? null), "country_name", array());
            echo " 
                <span class=\"flag-icon flag-icon-";
            // line 9
            echo twig_escape_filter($this->env, twig_lower_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["asn_info"] ?? null), "country", array())), "html", null, true);
            echo "\"></span>
            ";
        } else {
            // line 11
            echo "                <span class=\"asrank-unknown\">unknown</span>
            ";
        }
        // line 13
        echo "        </td></tr>
    <tr><th>customer cone </th>
        <td class=\"asrank-info-table-sub_facts\" data-toggle=\"tooltip\" data-placement=\"top\" title=\"The number of ASNs that are observed to be in the selected ASN's customer cone.\"> 
            ";
        // line 16
        echo twig_escape_filter($this->env, (($__internal_7cd7461123377b8c9c1b6a01f46c7bbd94bd12e59266005df5e93029ddbc0ec5 = twig_get_attribute($this->env, $this->source, ($context["asn_info"] ?? null), "cone", array())) && is_array($__internal_7cd7461123377b8c9c1b6a01f46c7bbd94bd12e59266005df5e93029ddbc0ec5) || $__internal_7cd7461123377b8c9c1b6a01f46c7bbd94bd12e59266005df5e93029ddbc0ec5 instanceof ArrayAccess ? ($__internal_7cd7461123377b8c9c1b6a01f46c7bbd94bd12e59266005df5e93029ddbc0ec5["asns"] ?? null) : null), "html", null, true);
        echo "<br>
            <span>asn</span>
        </td>
        <td class=\"asrank-info-table-sub_facts\" data-toggle=\"tooltip\" data-placement=\"top\" title=\"The number of prefixes that are observed to be in the selected ASN's customer cone.\"> 
            ";
        // line 20
        echo twig_escape_filter($this->env, (($__internal_3e28b7f596c58d7729642bcf2acc6efc894803703bf5fa7e74cd8d2aa1f8c68a = twig_get_attribute($this->env, $this->source, ($context["asn_info"] ?? null), "cone", array())) && is_array($__internal_3e28b7f596c58d7729642bcf2acc6efc894803703bf5fa7e74cd8d2aa1f8c68a) || $__internal_3e28b7f596c58d7729642bcf2acc6efc894803703bf5fa7e74cd8d2aa1f8c68a instanceof ArrayAccess ? ($__internal_3e28b7f596c58d7729642bcf2acc6efc894803703bf5fa7e74cd8d2aa1f8c68a["prefixes"] ?? null) : null), "html", null, true);
        echo "<br>
            <span>prefix</span>
        </td>
        <td class=\"asrank-info-table-sub_facts\" data-toggle=\"tooltip\" data-placement=\"top\" title=\"The number of addresses that are observed to be in the selected ASN's customer cone.\"> 
            ";
        // line 24
        echo twig_escape_filter($this->env, (($__internal_b0b3d6199cdf4d15a08b3fb98fe017ecb01164300193d18d78027218d843fc57 = twig_get_attribute($this->env, $this->source, ($context["asn_info"] ?? null), "cone", array())) && is_array($__internal_b0b3d6199cdf4d15a08b3fb98fe017ecb01164300193d18d78027218d843fc57) || $__internal_b0b3d6199cdf4d15a08b3fb98fe017ecb01164300193d18d78027218d843fc57 instanceof ArrayAccess ? ($__internal_b0b3d6199cdf4d15a08b3fb98fe017ecb01164300193d18d78027218d843fc57["addresses"] ?? null) : null), "html", null, true);
        echo "<br>
            <span>address</span>
        </td>
    </tr>
    <tr><th>AS rank</th><td colspan=\"7\">";
        // line 28
        echo twig_get_attribute($this->env, $this->source, ($context["asn_info"] ?? null), "rank", array());
        echo "</td></tr>
    <tr><th>AS degree</th>
        <td class=\"asrank-info-table-sub_facts\" data-toggle=\"tooltip\" data-placement=\"top\" title=\"The number of ASNs that were observed as neighbors of the selected ASN in a path.\">
            ";
        // line 31
        echo twig_escape_filter($this->env, (($__internal_81ccf322d0988ca0aa9ae9943d772c435c5ff01fb50b956278e245e40ae66ab9 = twig_get_attribute($this->env, $this->source, ($context["asn_info"] ?? null), "degree", array())) && is_array($__internal_81ccf322d0988ca0aa9ae9943d772c435c5ff01fb50b956278e245e40ae66ab9) || $__internal_81ccf322d0988ca0aa9ae9943d772c435c5ff01fb50b956278e245e40ae66ab9 instanceof ArrayAccess ? ($__internal_81ccf322d0988ca0aa9ae9943d772c435c5ff01fb50b956278e245e40ae66ab9["globals"] ?? null) : null), "html", null, true);
        echo "<br>
            <span>global</span>
        </td>
        <td class=\"asrank-info-table-sub_facts\" data-toggle=\"tooltip\" data-placement=\"top\" title=\"The number of ASNs that where observed as neighbors of the selected ASN in a path, where the selected ASN was between, i.e. providing transit, for two other ASNs.\"> 
            ";
        // line 35
        echo twig_escape_filter($this->env, (($__internal_add9db1f328aaed12ef1a33890510da978cc9cf3e50f6769d368473a9c90c217 = twig_get_attribute($this->env, $this->source, ($context["asn_info"] ?? null), "degree", array())) && is_array($__internal_add9db1f328aaed12ef1a33890510da978cc9cf3e50f6769d368473a9c90c217) || $__internal_add9db1f328aaed12ef1a33890510da978cc9cf3e50f6769d368473a9c90c217 instanceof ArrayAccess ? ($__internal_add9db1f328aaed12ef1a33890510da978cc9cf3e50f6769d368473a9c90c217["transits"] ?? null) : null), "html", null, true);
        echo "<br>
            <span>transit</span>
        </td>
        <td class=\"asrank-info-table-sub_facts\" data-toggle=\"tooltip\" data-placement=\"top\" title=\"The number of ASNs that are providers of the selected ASN.\"> 
            ";
        // line 39
        echo twig_escape_filter($this->env, (($__internal_128c19eb75d89ae9acc1294da2e091b433005202cb9b9351ea0c5dd5f69ee105 = twig_get_attribute($this->env, $this->source, ($context["asn_info"] ?? null), "degree", array())) && is_array($__internal_128c19eb75d89ae9acc1294da2e091b433005202cb9b9351ea0c5dd5f69ee105) || $__internal_128c19eb75d89ae9acc1294da2e091b433005202cb9b9351ea0c5dd5f69ee105 instanceof ArrayAccess ? ($__internal_128c19eb75d89ae9acc1294da2e091b433005202cb9b9351ea0c5dd5f69ee105["providers"] ?? null) : null), "html", null, true);
        echo "<br>
            <span>provider</span>
        </td>
        <td class=\"asrank-info-table-sub_facts\" data-toggle=\"tooltip\" data-placement=\"top\" title=\"The number of ASNs that are peers of the selected ASN.\"> 
            ";
        // line 43
        echo twig_escape_filter($this->env, (($__internal_921de08f973aabd87ecb31654784e2efda7404f12bd27e8e56991608c76e7779 = twig_get_attribute($this->env, $this->source, ($context["asn_info"] ?? null), "degree", array())) && is_array($__internal_921de08f973aabd87ecb31654784e2efda7404f12bd27e8e56991608c76e7779) || $__internal_921de08f973aabd87ecb31654784e2efda7404f12bd27e8e56991608c76e7779 instanceof ArrayAccess ? ($__internal_921de08f973aabd87ecb31654784e2efda7404f12bd27e8e56991608c76e7779["peers"] ?? null) : null), "html", null, true);
        echo "<br>
            <span>peer</span>
        </td>
        <td class=\"asrank-info-table-sub_facts\" data-toggle=\"tooltip\" data-placement=\"top\" title=\"The number of ASNs that are customers of the selected ASN.\"> 
            ";
        // line 47
        echo twig_escape_filter($this->env, (($__internal_3e040fa9f9bcf48a8b054d0953f4fffdaf331dc44bc1d96f1bb45abb085e61d1 = twig_get_attribute($this->env, $this->source, ($context["asn_info"] ?? null), "degree", array())) && is_array($__internal_3e040fa9f9bcf48a8b054d0953f4fffdaf331dc44bc1d96f1bb45abb085e61d1) || $__internal_3e040fa9f9bcf48a8b054d0953f4fffdaf331dc44bc1d96f1bb45abb085e61d1 instanceof ArrayAccess ? ($__internal_3e040fa9f9bcf48a8b054d0953f4fffdaf331dc44bc1d96f1bb45abb085e61d1["customers"] ?? null) : null), "html", null, true);
        echo "<br>
            <span>customer</span>
        </td>
    </tr>
</table>
</div>
";
    }

    public function getTemplateName()
    {
        return "asns/asn-information-table.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  118 => 47,  111 => 43,  104 => 39,  97 => 35,  90 => 31,  84 => 28,  77 => 24,  70 => 20,  63 => 16,  58 => 13,  54 => 11,  49 => 9,  44 => 8,  42 => 7,  35 => 5,  31 => 4,  27 => 3,  23 => 1,);
    }

    public function getSourceContext()
    {
        return new Twig_Source("", "asns/asn-information-table.html.twig", "/home/baitaluk/projects/asrank/web-server/templates/asns/asn-information-table.html.twig");
    }
}
