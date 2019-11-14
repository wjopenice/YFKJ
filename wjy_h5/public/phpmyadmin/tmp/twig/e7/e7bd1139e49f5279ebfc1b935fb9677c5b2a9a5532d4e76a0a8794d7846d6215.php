<?php

/* display/export/options_output_save_dir.twig */
class __TwigTemplate_68e0d6efba61811eaedbe6f4bb372fd97c242dc94fffc59b2c624d4203ea2ad0 extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = false;

        $this->blocks = [
        ];
    }

    protected function doDisplay(array $context, array $blocks = [])
    {
        // line 1
        echo "<li>
    <input type=\"checkbox\" name=\"onserver\" value=\"saveit\"
        id=\"checkbox_dump_onserver\"";
        // line 3
        echo ((($context["export_is_checked"] ?? null)) ? (" checked") : (""));
        echo ">
    <label for=\"checkbox_dump_onserver\">
        ";
        // line 5
        echo sprintf(_gettext("Save on server in the directory <strong>%s</strong>"), twig_escape_filter($this->env, ($context["save_dir"] ?? null)));
        echo "
    </label>
</li>
<li>
    <input type=\"checkbox\" name=\"onserver_overwrite\"
        value=\"saveitover\" id=\"checkbox_dump_onserver_overwrite\"";
        // line 11
        echo ((($context["export_overwrite_is_checked"] ?? null)) ? (" checked") : (""));
        echo ">
    <label for=\"checkbox_dump_onserver_overwrite\">
        ";
        // line 13
        echo _gettext("Overwrite existing file(s)");
        // line 14
        echo "    </label>
</li>
";
    }

    public function getTemplateName()
    {
        return "display/export/options_output_save_dir.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  43 => 14,  41 => 13,  36 => 11,  28 => 5,  23 => 3,  19 => 1,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Twig_Source("", "display/export/options_output_save_dir.twig", "/home/wwwroot/ETH_yaf/phpmyadmin/templates/display/export/options_output_save_dir.twig");
    }
}
