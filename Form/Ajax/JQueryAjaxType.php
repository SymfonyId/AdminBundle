<?php
namespace Symfonian\Indonesia\AdminBundle\Form\Ajax;

/**
 * Author: Muhammad Surya Ihsanuddin<surya.kejawen@gmail.com>
 * Url: http://blog.khodam.org
 */

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\OptionsResolver\Exception\InvalidArgumentException;

abstract class JQueryAjaxType extends AbstractType
{
    /**
     * @var RouterInterface
     */
    protected $router;

    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'javascript' => null,
            'method' => 'GET',
            'event' => 'onkeydown',
            'function' => null,
            'callback' => null,
        ));

        $resolver->setOptional(array('event', 'callback'));
        $resolver->setRequired(array('target', 'action'));

        $resolver->setAllowedValues(array(
            'method' => array('post', 'POST', 'get', 'GET'),
            'event' => array('onchange', 'onkeydown'),
        ));
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        if (! is_array($options['target']) || ! isset($options['target']['selector'])) {
            throw new InvalidArgumentException('options target must be an array with key selector (required) and hendler (optional)');
        }

        if ($options['javascript']) {

            $view->vars['javascript'] = sprintf('<script type="text/javascript">%s</script>', $options['javascript']);
        } else {
            $options['callback'] = $options['callback'] ?: sprintf('fn_%s_%s', uniqid(), $form->getName());

            if ('onchange' === $options['event']) {

                $view->vars['attr']['onchange'] = sprintf('%s(this); return false;', $options['callback']);
            } else {

                $view->vars['attr']['onkeydown'] = sprintf('if (13 === event.keyCode) { event.preventDefault(); %s(this); return false; }', $options['callback']);
            }

            $view->vars['javascript'] =
<<<EOD
<script type="text/javascript">
function %function%(field) {
    jQuery.ajax({
        type: '%method%',
        url: '%url%',
        data: {value: field.value},
        success: function(data) {
            %target%
        }
    });
}
</script>
EOD;
            if (isset($options['target']['handler'])) {

                $success = strtr($options['target']['handler'], array('target:selector' => $options['target']['selector']));
            } else {

                $success = sprintf('jQuery("%s").val(data)', $options['target']['selector']);
            }

            $view->vars['javascript'] = strtr($view->vars['javascript'], array(
                '%function%' => $options['callback'],
                '%method%' => $options['method'],
                '%url%' => $this->router->generate($options['action']),
                '%target%' => $success,
            ));
        }
    }
}
