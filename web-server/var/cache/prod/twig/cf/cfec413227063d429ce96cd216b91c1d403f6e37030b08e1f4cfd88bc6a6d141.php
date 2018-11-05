<?php

/* as-core.html.twig */
class __TwigTemplate_ffc763a554704732482492829afea282efb4b3f41979cc94b80e6c4b38e28f4f extends Twig_Template
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
        if ((isset($context["location"]) || array_key_exists("location", $context))) {
            // line 2
            echo "    ";
            if ( !(isset($context["as_core_size"]) || array_key_exists("as_core_size", $context))) {
                // line 3
                echo "        ";
                $context["as_core_png_url"] = "/images/as-core-small.png";
                // line 4
                echo "        ";
                $context["as_core_image_id"] = "as_core_image_small_id";
                // line 5
                echo "        ";
                $context["as_core_loading_id"] = "as_core_loading_small_id";
                // line 6
                echo "        ";
                $context["as_core_size_small"] = "210";
                // line 7
                echo "        ";
                if ((twig_get_attribute($this->env, $this->source, ($context["location"] ?? null), "type", array()) == "org")) {
                    // line 8
                    echo "            ";
                    $context["as_core_large_url"] = $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("org_as_core", array("org" => twig_get_attribute($this->env, $this->source, ($context["location"] ?? null), "id", array())));
                    // line 9
                    echo "        ";
                } else {
                    // line 10
                    echo "            ";
                    $context["as_core_large_url"] = $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("asn_as_core", array("asn" => twig_get_attribute($this->env, $this->source, ($context["location"] ?? null), "id", array())));
                    // line 11
                    echo "        ";
                }
                // line 12
                echo "

        <div class=\"col-sm-2 col-md-3\" style=\"height:";
                // line 14
                echo twig_escape_filter($this->env, ($context["as_core_size_small"] ?? null), "html", null, true);
                echo "px;width:";
                echo twig_escape_filter($this->env, ($context["as_core_size_small"] ?? null), "html", null, true);
                echo "px;\">
        <a href=\"";
                // line 15
                echo twig_escape_filter($this->env, ($context["as_core_large_url"] ?? null), "html", null, true);
                echo "\">

    ";
            } else {
                // line 18
                echo "        ";
                $context["as_core_png_url"] = "/images/as-core.png";
                // line 19
                echo "        ";
                $context["as_core_image_id"] = "as_core_image_large";
                // line 20
                echo "        ";
                $context["as_core_loading_id"] = "as_core_loading_large";
                // line 21
                echo "        ";
                $context["as_core_size_inner"] = (($context["as_core_size"] ?? null) * 0.8);
                // line 22
                echo "        ";
                $context["as_core_shift_top"] = ((($context["as_core_size"] ?? null) - ($context["as_core_size_inner"] ?? null)) / 2.1);
                // line 23
                echo "        ";
                $context["as_core_shift_left"] = ((($context["as_core_size"] ?? null) - ($context["as_core_size_inner"] ?? null)) / 1.1);
                // line 24
                echo "        <div class=\"row\">
        <div class=\"col\" style=\"height:";
                // line 25
                echo twig_escape_filter($this->env, ($context["as_core_size"] ?? null), "html", null, true);
                echo "px;width:";
                echo twig_escape_filter($this->env, ($context["as_core_size"] ?? null), "html", null, true);
                echo "px;z-index:0\">
            <div style=\"position absolute;margin-top:1em;
                top:-3em;
                right:0;
                margin-top:2em;
                z-index:1;
                line-height:95%;
                \"
                >
                ";
                // line 34
                echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["location"] ?? null), "name", array()), "html", null, true);
                echo "'s <b>AS Core</b><br>&nbsp;(<a href=\"";
                echo $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("about");
                echo "#as-core\">about AS Core</a>)
            </div>
            <img src=\"/images/as-core-continents.svg\" style=\"height: 103%; width: auto;position: absolute;
                top: -15px;
                left: -35px;
                z-index:-1;\">
            <div class=\"col\" style=\"height:";
                // line 40
                echo twig_escape_filter($this->env, ($context["as_core_size_inner"] ?? null), "html", null, true);
                echo "px;width:";
                echo twig_escape_filter($this->env, ($context["as_core_size_inner"] ?? null), "html", null, true);
                echo "px;
                margin-top:";
                // line 41
                echo twig_escape_filter($this->env, ($context["as_core_shift_top"] ?? null), "html", null, true);
                echo "px;
                margin-left:";
                // line 42
                echo twig_escape_filter($this->env, ($context["as_core_shift_left"] ?? null), "html", null, true);
                echo "px;
                position:absolute;
                top:0px;
                left:0px;\">
    ";
            }
            // line 47
            echo "
        <img src=\"";
            // line 48
            echo twig_escape_filter($this->env, ($context["as_core_png_url"] ?? null), "html", null, true);
            echo "\" style=\"height: 100%; width: auto;position: absolute;
            top: 0;
            left: 0;
            z-index:-3;\">

        <svg style=\"height: 100%; width: auto;position: absolute;
            top: 0;
            left: 0;
            z-index:-2;\" viewBox=\"0 0 300 300\">
            <circle cx=\"150\" cy=\"150\" r=\"147\" stroke=\"black\" stroke-width=\"0\" fill=\"black\" fill-opacity=\"0.7\"/>
        </svg>


        ";
            // line 61
            $context["as_core_url"] = $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("as_core", array("asn" => twig_get_attribute($this->env, $this->source, ($context["location"] ?? null), "id", array())));
            // line 62
            echo "        ";
            if ((isset($context["as_core_url"]) || array_key_exists("as_core_url", $context))) {
                // line 63
                echo "            ";
                // line 68
                echo "            <img id=\"";
                echo twig_escape_filter($this->env, ($context["as_core_image_id"] ?? null), "html", null, true);
                echo "\" src=\"";
                echo twig_escape_filter($this->env, ($context["as_core_url"] ?? null), "html", null, true);
                echo "\" style=\"height: 100%; width: auto;position: absolute;
                top: 0;
                left: 0;
                z-index:0\"\">
        ";
            }
            // line 73
            echo "
    ";
            // line 74
            if ( !(isset($context["as_core_size"]) || array_key_exists("as_core_size", $context))) {
                // line 75
                echo "        </a>
        </div>
    ";
            } else {
                // line 78
                echo "            </div>
        </div>
      </div>
    ";
            }
        }
    }

    public function getTemplateName()
    {
        return "as-core.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  182 => 78,  177 => 75,  175 => 74,  172 => 73,  161 => 68,  159 => 63,  156 => 62,  154 => 61,  138 => 48,  135 => 47,  127 => 42,  123 => 41,  117 => 40,  106 => 34,  92 => 25,  89 => 24,  86 => 23,  83 => 22,  80 => 21,  77 => 20,  74 => 19,  71 => 18,  65 => 15,  59 => 14,  55 => 12,  52 => 11,  49 => 10,  46 => 9,  43 => 8,  40 => 7,  37 => 6,  34 => 5,  31 => 4,  28 => 3,  25 => 2,  23 => 1,);
    }

    public function getSourceContext()
    {
        return new Twig_Source("", "as-core.html.twig", "/home/baitaluk/projects/asrank/web-server/templates/as-core.html.twig");
    }
}
