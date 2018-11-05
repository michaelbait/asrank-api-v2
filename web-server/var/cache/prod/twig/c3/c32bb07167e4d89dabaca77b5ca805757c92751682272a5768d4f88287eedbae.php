<?php

/* @Twig/Exception/error404.html.twig */
class __TwigTemplate_4a1bdd93519423d2b01256cb5c2680cb703ba51e5a5f59630ee0b1d5ea175724 extends Twig_Template
{
    private $source;

    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        // line 2
        $this->parent = $this->loadTemplate("base.html.twig", "@Twig/Exception/error404.html.twig", 2);
        $this->blocks = array(
            'body' => array($this, 'block_body'),
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

    // line 4
    public function block_body($context, array $blocks = array())
    {
        // line 5
        echo "    <h1>Page not found</h1>
    <p>
        Запрошенная страница не найдена. Проверьте опечатки в URL или
        <a href=\"";
        // line 8
        echo $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("homepage");
        echo "\">вернитесь на домашнюю сраницу</a>.
    </p>
";
    }

    public function getTemplateName()
    {
        return "@Twig/Exception/error404.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  40 => 8,  35 => 5,  32 => 4,  15 => 2,);
    }

    public function getSourceContext()
    {
        return new Twig_Source("", "@Twig/Exception/error404.html.twig", "/home/baitaluk/projects/asrank/web-server/templates/bundles/TwigBundle/Exception/error404.html.twig");
    }
}
