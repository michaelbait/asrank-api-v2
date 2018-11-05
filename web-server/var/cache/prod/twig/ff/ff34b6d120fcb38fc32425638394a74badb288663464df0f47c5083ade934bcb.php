<?php

/* navbar.html.twig */
class __TwigTemplate_eb66282610724d8564744182859febdb1c067bea7bb665bd68b489af32efa3c3 extends Twig_Template
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
        echo "<div id=\"navbar\" class=\"container asrank-navbar-container\">
    <nav class=\"navbar navbar-expand navbar-light justify-content-front asrank-navbar\">
        ";
        // line 3
        $this->loadTemplate("navbar-logo.html.twig", "navbar.html.twig", 3)->display($context);
        // line 4
        echo "        <div>
        <div class=\"collapse navbar-collapse justify-content-front\" id=\"navbarToggler\">
            <ul class=\"navbar-nav\">
              <li class=\"nav-item dropdown\">
                <a href=\"#\" class=\"nav-link dropdown-toggle\" id=\"navAbout\" data-toggle=\"dropdown\" aria-haspopup=\"true\" aria-expanded=\"false\">About</a>
                <div class=\"dropdown-menu\" aria-labelledby=\"navAbout\">
                    <a class=\"dropdown-item\" href=\"";
        // line 10
        echo $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("faq");
        echo "\">FAQ</a>
                    <a class=\"nav-link\" href=\"";
        // line 11
        echo $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("about");
        echo "\">&nbsp;&nbsp;&nbsp;Background</a>
                    <ul>
                        <a href=\"";
        // line 13
        echo $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("about");
        echo "#rank\">- AS Ranking</a><br>
                        <a href=\"";
        // line 14
        echo $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("about");
        echo "#citation\">- Citation</a>
                    </ul>
                </div>
              </li>
              <li class=\"nav-item dropdown\">
                <a href=\"#\" class=\"nav-link dropdown-toggle\" id=\"navRanking\" data-toggle=\"dropdown\" aria-haspopup=\"true\" aria-expanded=\"false\">Ranking</a>
                <div class=\"dropdown-menu\" aria-labelledby=\"navRanking\">
                    <a class=\"dropdown-item\" href=\"";
        // line 21
        echo $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("asns_ranking");
        echo "\">AS Ranking</a>
                    <a class=\"dropdown-item\" href=\"";
        // line 22
        echo $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("orgs_ranking");
        echo "\">Org Ranking</a>
                </div>
              </li>
              <li class=\"nav-item\">
                  <a class=\"nav-link\" href=\"";
        // line 26
        echo $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("asn_neighbors");
        echo "/\">Search</a>
              </li>
              <li class=\"nav-item\">
                  <a class=\"nav-link\" href=\"";
        // line 29
        echo $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("contact");
        echo "\">Contact</a>
              </li>
              <li class=\"nav-item dropdown\">
                <a href=\"#\" class=\"nav-link dropdown-toggle\" id=\"navData\" data-toggle=\"dropdown\" aria-haspopup=\"true\" aria-expanded=\"false\">Data </a>
                <div class=\"dropdown-menu\" aria-labelledby=\"navData\">
                    <div style=\"text-align:center;font-size:80%;font-color:grey;\">(external links)</div>
                    <a class=\"dropdown-item\" href=\"http://www.caida.org/data/as-relationships/\">AS Relationship</a>
                    <a class=\"dropdown-item\" href=\"http://www.caida.org/data/as-organizations/\">AS Organization</a>
                  <a class=\"dropdown-item\" href=\"";
        // line 37
        echo twig_escape_filter($this->env, ($context["RESTFUL_DATABASE_URL"] ?? null), "html", null, true);
        echo "\">Data API</a>
                </div>
              </li>
            </ul>
        </div>
        ";
        // line 42
        if ((isset($context["location"]) || array_key_exists("location", $context))) {
            // line 43
            echo "            <div style=\"
                position:absolute;
                left:130px;
                top:33px;
                z-index:1;
                color:grey;
                font-size:80%
                \" class=\"navbar-nav\">
                    <div style=\"color:black;baseline:10px;margin-top:8px;\">";
            // line 51
            echo twig_get_attribute($this->env, $this->source, ($context["location"] ?? null), "name", array());
            echo ":</div>
                    <a href=\"#\" class=\"nav-link dropdown-toggle\" id=\"navLocation\" data-toggle=\"dropdown\" aria-haspopup=\"true\"
                        aria-expanded=\"false\" >";
            // line 53
            echo twig_get_attribute($this->env, $this->source, ($context["location"] ?? null), "area", array());
            echo "<a>
                    <div class=\"dropdown-menu\" aria-labelledby=\"navLocation\"
                        style=\"line-height:10px;font-size:80%;padding:0;left:-4em;top:2.5em;position:relative;\">
                        ";
            // line 56
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable(twig_get_attribute($this->env, $this->source, ($context["location"] ?? null), "areas", array()));
            foreach ($context['_seq'] as $context["_key"] => $context["area"]) {
                // line 57
                echo "                            <a class=\"dropdown-item\" href=\"";
                echo twig_get_attribute($this->env, $this->source, $context["area"], "url", array());
                echo "\">";
                echo twig_get_attribute($this->env, $this->source, $context["area"], "label", array());
                echo "</a>
                        ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['area'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 59
            echo "                    </div>
            </div>
        ";
        }
        // line 62
        echo "    </nav>
</div>
";
    }

    public function getTemplateName()
    {
        return "navbar.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  139 => 62,  134 => 59,  123 => 57,  119 => 56,  113 => 53,  108 => 51,  98 => 43,  96 => 42,  88 => 37,  77 => 29,  71 => 26,  64 => 22,  60 => 21,  50 => 14,  46 => 13,  41 => 11,  37 => 10,  29 => 4,  27 => 3,  23 => 1,);
    }

    public function getSourceContext()
    {
        return new Twig_Source("", "navbar.html.twig", "/home/baitaluk/projects/asrank/web-server/templates/navbar.html.twig");
    }
}
