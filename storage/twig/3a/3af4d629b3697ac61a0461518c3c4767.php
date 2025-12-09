<?php

use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Extension\CoreExtension;
use Twig\Extension\SandboxExtension;
use Twig\Markup;
use Twig\Sandbox\SecurityError;
use Twig\Sandbox\SecurityNotAllowedTagError;
use Twig\Sandbox\SecurityNotAllowedFilterError;
use Twig\Sandbox\SecurityNotAllowedFunctionError;
use Twig\Source;
use Twig\Template;
use Twig\TemplateWrapper;

/* page */
class __TwigTemplate_027fb533e4e556c116ad01b4f6ba04cd extends Template
{
    private Source $source;
    /**
     * @var array<string, Template>
     */
    private array $macros = [];

    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        $this->parent = false;

        $this->blocks = [
        ];
        $this->sandbox = $this->extensions[SandboxExtension::class];
        $this->checkSecurity();
    }

    protected function doDisplay(array $context, array $blocks = []): iterable
    {
        $macros = $this->macros;
        // line 1
        yield "<p>&lt;div id=&quot;perscom_widget_wrapper&quot;&gt;<br>    &lt;script<br>        id=&quot;perscom_widget&quot;<br>        data-apikey=&quot;";
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['App\Support\Twig\Extensions\SsoJwtExtension']->ssoJwt(), "html", null, true);
        yield "&quot;<br>        data-widget=&quot;roster&quot;<br>        src=&quot;{\$widgetUrl}&quot;<br>        type=&quot;text/javascript&quot;<br>    &gt;&lt;/script&gt;<br>&lt;/div&gt;</p>";
        yield from [];
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName(): string
    {
        return "page";
    }

    /**
     * @codeCoverageIgnore
     */
    public function isTraitable(): bool
    {
        return false;
    }

    /**
     * @codeCoverageIgnore
     */
    public function getDebugInfo(): array
    {
        return array (  44 => 1,);
    }

    public function getSourceContext(): Source
    {
        return new Source("<p>&lt;div id=&quot;perscom_widget_wrapper&quot;&gt;<br>    &lt;script<br>        id=&quot;perscom_widget&quot;<br>        data-apikey=&quot;{{ ssoJwt() }}&quot;<br>        data-widget=&quot;roster&quot;<br>        src=&quot;{\$widgetUrl}&quot;<br>        type=&quot;text/javascript&quot;<br>    &gt;&lt;/script&gt;<br>&lt;/div&gt;</p>", "page", "");
    }
    
    public function checkSecurity()
    {
        static $tags = [];
        static $filters = ["escape" => 1];
        static $functions = ["ssoJwt" => 1];

        try {
            $this->sandbox->checkSecurity(
                [],
                ['escape'],
                ['ssoJwt'],
                $this->source
            );
        } catch (SecurityError $e) {
            $e->setSourceContext($this->source);

            if ($e instanceof SecurityNotAllowedTagError && isset($tags[$e->getTagName()])) {
                $e->setTemplateLine($tags[$e->getTagName()]);
            } elseif ($e instanceof SecurityNotAllowedFilterError && isset($filters[$e->getFilterName()])) {
                $e->setTemplateLine($filters[$e->getFilterName()]);
            } elseif ($e instanceof SecurityNotAllowedFunctionError && isset($functions[$e->getFunctionName()])) {
                $e->setTemplateLine($functions[$e->getFunctionName()]);
            }

            throw $e;
        }

    }
}
