<?php

/* footer.html.twig */
class __TwigTemplate_d96688a64b786aab3b1a77e853189a429407a77883decbc2cfcf080ba526cbcf extends Twig_Template
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
        echo "<footer class=\"footer\">
<div id=\"footer\" class=\"container-full asrank-footer\">
<div class=\"row\">
    <div id=\"footer-dataset\" rel=\"tooltip\" class=\"col-3 col-lg-2\" title=\"\" data-toggle=\"tooltip\">
        <!-- a href=\"https://www.dhs.gov/\"> <img src=\"";
        // line 5
        echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\AssetExtension']->getAssetUrl("images/dhs-logo.svg"), "html", null, true);
        echo "\"
        class=\"d-none d-sm-inline-block asrank-support-dhs-logo\"> </a-->
        <a href=\"https://www.ucsd.edu/\"> <img src=\"";
        // line 7
        echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\AssetExtension']->getAssetUrl("images/ucsd-seal.svg"), "html", null, true);
        echo "\"
        class=\"d-none d-sm-inline-block asrank-support-ucsd-seal\"> </a>
    </div>
    <div class=\"col-9 col-lg-10 asrank-support\">
        <a href=\"http://www.caida.org\"> <img src=\"";
        // line 11
        echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\AssetExtension']->getAssetUrl("images/caida-logo.svg"), "html", null, true);
        echo "\" class=\"asrank-support-caida-logo\"> </a>
        <span class=\"asrank-support-text\">
            Support for this work is provided by the U.S. Department of Homeland Security's Science and Technology Directorate (
                <a href=\"https://www.caida.org/funding/cybersecurity/\">Cybersecurity</a> N66001-08-C-2029,
                <a href=\"http://www.caida.org/funding/sister/\">SISTER</a> HHSP 233201600012C
                ),
            the National Science Foundation Internet Laboratory for Empirical Network Science (
                <a href=\"http://www.caida.org/funding/dibbs-panda/\">PANDA</a> NSF OAC-1724853, 
                <a href=\"https://www.caida.org/funding/ilens/\">iLENS</a> CNS-0958547,
                <a href=\" https://sciencegateways.org/\">Science Gateway</a> NSF ACI--1547611),
            and Cisco's University Research Program.
        </span>
    </div>
</div>
</div>
</footer>

";
    }

    public function getTemplateName()
    {
        return "footer.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  41 => 11,  34 => 7,  29 => 5,  23 => 1,);
    }

    public function getSourceContext()
    {
        return new Twig_Source("", "footer.html.twig", "/home/baitaluk/projects/asrank/web-server/templates/footer.html.twig");
    }
}
