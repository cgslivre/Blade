<?php 

namespace Slim;

use Psr\Http\Message\ResponseInterface;

/**
 * Blade View
 *
 * This class is a Slim Framework view helper built
 * on top of a cut down Blade template component. Blade is
 * a PHP component for Laravel created by Taylor Otwell.
 *
 * @link http://laravel.com
 */
class Blade 
{
    /**
     * Blade renderer instance
     *
     * @var \Dijix\Blade
     */
    protected $blade;

	
    /********************************************************************************
     * Constructors and service provider registration
     *******************************************************************************/
    /**
     * Create new Blade view
     *
     * @param array  $settings Blade environment settings
     */
    public function __construct($settings=array())
    {
        $this->blade = new \Dijix\Blade($settings);
    }
    /**
     * Output rendered template
     *
     * @param ResponseInterface $response
     * @param  string $template Template pathname relative to templates directory
     * @param  array $data Associative array of template variables
     * @return ResponseInterface
     */
    public function render(ResponseInterface $response, $template, $data=array())
    {
		$response->getBody()->write($this->blade->render($template, $data));
		return $response;
    }

}