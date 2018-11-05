<?php

/* base.html.twig */
class __TwigTemplate_116d0b9e34e83d66e790aac779a0d368c890b53ce1c4ee82aa51d178cf86c9cd extends Twig_Template
{
    private $source;

    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        $this->parent = false;

        $this->blocks = array(
            'title' => array($this, 'block_title'),
            'description' => array($this, 'block_description'),
            'stylesheets' => array($this, 'block_stylesheets'),
            'jsonld' => array($this, 'block_jsonld'),
            'navbar' => array($this, 'block_navbar'),
            'header_older' => array($this, 'block_header_older'),
            'body' => array($this, 'block_body'),
            'datasources' => array($this, 'block_datasources'),
            'footer' => array($this, 'block_footer'),
            'javascripts' => array($this, 'block_javascripts'),
        );
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        // line 1
        echo "<!DOCTYPE html>
";
        // line 2
        $context["maintenance"] = false;
        // line 3
        echo "<html lang=\"en\">
<head>
\t<!-- Global site tag (gtag.js) - Google Analytics -->
\t<script async src=\"https://www.googletagmanager.com/gtag/js?id=UA-116819380-1\"></script>
\t<script>
\t  window.dataLayer = window.dataLayer || [];
\t  function gtag(){dataLayer.push(arguments);}
\t  gtag('js', new Date());

\t  gtag('config', 'UA-116819380-1');
\t</script>

    <link rel=\"icon\" type=\"image/x-icon\" href=\"";
        // line 15
        echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\AssetExtension']->getAssetUrl("favicon.ico"), "html", null, true);
        echo "\" />
    <!-- Required meta tags -->
    <meta http-equiv=\"X-UA-Compatible\" content=\"IE=edge\">
    <meta name=\"viewport\" content=\"width=1000, initial-scale=1, shrink-to-fit=yes\">
    <!-- meta name=\"viewport\" content=\"width=device-width, initial-scale=1, shrink-to-fit=no\" -->
    <meta charset=\"UTF-8\">
    ";
        // line 21
        $context["page_title"] = "";
        // line 22
        echo "    <title>AS Rank";
        $this->displayBlock('title', $context, $blocks);
        echo "</title>
    <META NAME=\"Description\" CONTENT=\"";
        // line 23
        $this->displayBlock('description', $context, $blocks);
        echo "\">
    ";
        // line 24
        $this->displayBlock('stylesheets', $context, $blocks);
        // line 35
        echo "    ";
        $this->displayBlock('jsonld', $context, $blocks);
        // line 36
        echo "
    <style>
        .navbar .navbar-toggle .icon-bar {
        background-color: CornflowerBlue ;
        }
    </style>

</head>
<body>
    <div style=\"background-color:black;width:100%;height:.5em\"></div>
    ";
        // line 46
        $this->displayBlock('navbar', $context, $blocks);
        // line 47
        echo "    <!--
    <table border=\"0\" style=\"position:static;x:0;padding:0;margin:0;width:100%\">
        <tr>
            <td style=\"white-space:nowrap;\">";
        // line 50
        $this->displayBlock('header_older', $context, $blocks);
        echo "</td>
        </tr>
    </table>
    <div>
      <span class=\"d-none d-sm-block\">Small</span>
      <span class=\"d-none d-md-block\">Medium</span>
      <span class=\"d-none d-lg-block\">Large</span>
      <span class=\"d-none d-xl-block\">Extra Large</span>
    </div>
    -->

    <!--------------------------------------------------------->


    <div id=\"content\" class=\"asrank-content container\">
        <div id=\"default_error_location\" class=\"asrank_result_error\" style=\"visibility: hidden;\"></div>
        ";
        // line 66
        $this->displayBlock('body', $context, $blocks);
        // line 67
        echo "    </div>
    ";
        // line 68
        $this->displayBlock('datasources', $context, $blocks);
        // line 69
        echo "    ";
        $this->displayBlock('footer', $context, $blocks);
        // line 70
        echo "
    <!--------------------------------------------------------->
    ";
        // line 72
        $this->displayBlock('javascripts', $context, $blocks);
        // line 97
        echo "</body>
</html>
";
    }

    // line 22
    public function block_title($context, array $blocks = array())
    {
        echo twig_escape_filter($this->env, ($context["page_title"] ?? null), "html", null, true);
    }

    // line 23
    public function block_description($context, array $blocks = array())
    {
    }

    // line 24
    public function block_stylesheets($context, array $blocks = array())
    {
        // line 25
        echo "        <!-- Bootstrap CSS -->
        <link href=\"";
        // line 26
        echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\AssetExtension']->getAssetUrl("css/bootstrap.min.css"), "html", null, true);
        echo "\" rel=\"stylesheet\">
        <link href=\"";
        // line 27
        echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\AssetExtension']->getAssetUrl("css/flag-icon.css"), "html", null, true);
        echo "\" rel=\"stylesheet\">
        <!--[if lt IE 9]>
          <script src=\"https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js\"></script>
          <script src=\"https://oss.maxcdn.com/respond/1.4.2/respond.min.js\"></script>
        <![endif]-->

        <link rel=\"stylesheet\" href=\"";
        // line 33
        echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\AssetExtension']->getAssetUrl("css/asrank.css"), "html", null, true);
        echo "\">
    ";
    }

    // line 35
    public function block_jsonld($context, array $blocks = array())
    {
    }

    // line 46
    public function block_navbar($context, array $blocks = array())
    {
        echo " ";
        $this->loadTemplate("navbar.html.twig", "base.html.twig", 46)->display($context);
        echo " ";
    }

    // line 50
    public function block_header_older($context, array $blocks = array())
    {
        echo twig_escape_filter($this->env, ($context["page_title"] ?? null), "html", null, true);
    }

    // line 66
    public function block_body($context, array $blocks = array())
    {
    }

    // line 68
    public function block_datasources($context, array $blocks = array())
    {
    }

    // line 69
    public function block_footer($context, array $blocks = array())
    {
        echo " ";
        $this->loadTemplate("footer.html.twig", "base.html.twig", 69)->display($context);
        echo " ";
    }

    // line 72
    public function block_javascripts($context, array $blocks = array())
    {
        echo " 
        <script>
            // URL for the restful database
            var RESTFUL_DATABASE_URL=\"";
        // line 75
        echo twig_escape_filter($this->env, ($context["RESTFUL_DATABASE_URL"] ?? null), "html", null, true);
        echo "\";
            var PAGE_NUMBER_RANGE=";
        // line 76
        echo twig_escape_filter($this->env, ($context["PAGE_NUMBER_RANGE"] ?? null), "html", null, true);
        echo ";
        </script>

        <script src=\"";
        // line 79
        echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\AssetExtension']->getAssetUrl("js/popper.min.js"), "html", null, true);
        echo "\"></script>
        <script src=\"";
        // line 80
        echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\AssetExtension']->getAssetUrl("js/jquery.min.js"), "html", null, true);
        echo "\"></script>
        <script src=\"";
        // line 81
        echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\AssetExtension']->getAssetUrl("js/bootstrap.min.js"), "html", null, true);
        echo "\"></script>
        <script src=\"";
        // line 82
        echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\AssetExtension']->getAssetUrl("js/URI.min.js"), "html", null, true);
        echo "\"></script>
        <script src=\"";
        // line 83
        echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\AssetExtension']->getAssetUrl("js/asrank2.js"), "html", null, true);
        echo "\"></script>
        <script src=\"";
        // line 84
        echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\AssetExtension']->getAssetUrl("js/table_trees2.js"), "html", null, true);
        echo "\"></script>

        <script>
            // initilizes the tooltips
            \$(function () {
                \$('[data-toggle=\"tooltip\"]').tooltip()
            });
            ";
        // line 91
        if ((($context["maintenance"] ?? null) == false)) {
            // line 92
            echo "                // fills in the footer information
            RankUtil.dataset_information();
            ";
        }
        // line 95
        echo "        </script>
    ";
    }

    public function getTemplateName()
    {
        return "base.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  264 => 95,  259 => 92,  257 => 91,  247 => 84,  243 => 83,  239 => 82,  235 => 81,  231 => 80,  227 => 79,  221 => 76,  217 => 75,  210 => 72,  202 => 69,  197 => 68,  192 => 66,  186 => 50,  178 => 46,  173 => 35,  167 => 33,  158 => 27,  154 => 26,  151 => 25,  148 => 24,  143 => 23,  137 => 22,  131 => 97,  129 => 72,  125 => 70,  122 => 69,  120 => 68,  117 => 67,  115 => 66,  96 => 50,  91 => 47,  89 => 46,  77 => 36,  74 => 35,  72 => 24,  68 => 23,  63 => 22,  61 => 21,  52 => 15,  38 => 3,  36 => 2,  33 => 1,);
    }

    public function getSourceContext()
    {
        return new Twig_Source("", "base.html.twig", "/home/baitaluk/projects/asrank/web-server/templates/base.html.twig");
    }
}
