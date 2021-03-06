<?php

namespace Reborn\MVC\Controller;

use Reborn\Cores\Application;
use Reborn\Http\Response;
use Reborn\Exception\HttpNotFoundException;
use Reborn\MVC\Controller\Exception\NotAuthException;
use Reborn\MVC\Controller\Exception\NotAdminAccessException;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

/**
 * Container class for Controller
 *
 * @package Reborn\MVC\Controller
 * @author Myanmar Links Professional Web Development Team
 **/
class Controller
{

    /**
     * IOC Application Container
     *
     * @var \Reborn\Cores\Application
     **/
    protected $app;

    /**
     * request object variable for controller
     *
     * @var \Reborn\Http\Request
     **/
    protected $request = null;

    /**
     * template object variable for controller
     *
     * @var \Reborn\MVC\View\Template
     **/
    protected $template = null;

    /**
     * theme object variable for controller
     *
     * @var \Reborn\MVC\View\Theme
     **/
    protected $theme = null;

    /**
     * Variable for session object
     *
     * @var string
     **/
    protected $session = null;

    /**
     * Variable for HTTP status code for response
     *
     * @var int
     */
    protected $HTTPstatus = 200;

    /**
     * Variable for ETag use or not. Default is false.
     *
     * @var boolean
     */
    protected $Etag = false;

    /**
     * Variable for Request Format.
     *
     * ?_format=html
     *
     * @var string
     **/
    protected $format = 'html';

    /**
     * Initial Method
     *
     * @return void
     **/
    protected function init() {}

    /**
     * Before Method.
     * This method will be call before call the request action.
     * Can be set global data setter in these method.
     * eg: [Set stylesheet for all action]
     * <code>
     *  public function before()
     *  {
     *      $this->template->style('typo.css');
     *  }
     * </code>
     *
     * @return void
     */
    public function before() {}

    /**
     * After Method for Controller
     * This method return the Response Object.
     *
     * @param \Symfony\Component\HttpFoundation\Response
     */
    public function after($response)
    {
        // If request in inner call, partialRender Only
        if ($this->request->inner and !$response) {
            $this->request->inner = false;

           $response = $this->template->partialRender();
        }

        // Need to render the template
        if (is_null($response)) {
            $response = $this->template->render();
        }

        if (! $response instanceof SymfonyResponse) {

            $response = Response::make($response, $this->HTTPstatus);

            $response->prepare($this->app['request']);

            // Inject the CSRF Token to Response
            $response = $this->app->injectCSRFToken($response);
        }

        if ($this->Etag) {
            $response->setEtag(md5($response->getContent()));
        }

        return $response;
    }

    /**
     * Get value from Application
     *
     * @return mixed
     **/
    protected function get($name)
    {
        return $this->app->{$name};
    }

    /**
     * Set JS variables for the template.
     * Shortcut of $this->template->jsValue()
     *
     * @param  string|array $key   JS variable key or key value array
     * @param  mixed|null   $value Value for JS variable
     * @return void
     **/
    protected function jsValue($key, $value = null)
    {
        $this->template->jsValue($key, $value);
    }

    /**
     * Return the Json from Controller
     *
     * @param  mixed                                          $data    The response data
     * @param  integer                                        $status  The response status code
     * @param  array                                          $headers An array of response headers
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     **/
    protected function returnJson($data, $status = 200, $headers = array())
    {
        return $this->json($data, $status, $headers);
    }

    /**
     * Return the Json from Controller
     *
     * @param  mixed                                          $data    The response data
     * @param  integer                                        $status  The response status code
     * @param  array                                          $headers An array of response headers
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     **/
    protected function json($data, $status = 200, $headers = array())
    {
        return Response::json($data, $status, $headers, 'json');
    }

    /**
     * Return the Stream Response from Controller
     *
     * @param  mixed                                            $callback Callback function for StreamResponse
     * @param  integer                                          $status   The response status code
     * @param  array                                            $headers  An array of response headers
     * @return \Symfony\Component\HttpFoundation\StreamResponse
     **/
    protected function stream($callback, $status = 200, $headers = array())
    {
        return Response::stream($callback, $status, $headers, 'stream');
    }

    /**
     * Call the Action Method
     *
     * @param  \Reborn\Cores\Application $app    Application instance
     * @param  string                    $method Action method name
     * @param  array                     $params Parameter array for action method
     * @return \Reborn\Http\Response
     **/
    public function actionCaller(Application $app, $method, $params)
    {
        $this->app = $app;

        $this->variablesAssign();

        \Event::call('reborn.controller.process.starting', array($this, $method));

        // Call Inital Method
        try {
            $this->init();
        } catch (NotAuthException $e) {
            throw $e;
        } catch (NotAdminAccessException $e) {
            throw $e;
        }

        $this->before();

        if (! method_exists($this, $method)) {
            return $this->notFound();
        }

        // Catch for Method Parameters Problem
        try {
            $response = call_user_func_array(array($this, $method), (array) $params);
        } catch (\Exception $e) {
            if ($this->app['env'] == 'dev') {
                throw $e;
            } else {
                throw new HttpNotFoundException("Request params doesn't match for method!");
            }
        }

        $response = $this->after($response);

        \Event::call('reborn.controller.process.ending', array($response));

        return $response;
    }

    /**
     * Get request param by name.
     *
     * @param  string $name    Param name
     * @param  mixed  $default Default value for param name
     * @return mixed
     **/
    protected function param($name, $default = null)
    {
        if (isset($this->request->params[$name]) and $this->request->params[$name] != '') {
            return $this->request->params[$name];
        }

        return $default;
    }

    /**
     * Assign default variables from Controller
     *
     * @return void
     **/
    protected function variablesAssign()
    {
        $app = $this->app;
        $this->request = $app['request'];
        $this->session = $app['session'];
        $this->template = $app['template'];
        $this->theme = $app['theme'];

        // Set Request Format
        switch ($this->request->getRequestFormat()) {
            case 'json':
                $this->format = 'json';
                break;

            case 'xml':
                $this->format = 'xml';
                break;

            case 'csv':
                $this->format = 'csv';
                break;

            case 'html':
            default:
                $this->format = 'html';
                break;
        }
    }

    /**
     * Set the current theme's layout name
     *
     * @param  string $name Layout name for current theme
     * @return void
     **/
    protected function setLayout($name)
    {
        $this->template->setLayout($name);
    }

    /**
     * Set the session flush message
     *
     * @param  string $key   Flush name
     * @param  string $value Flash value
     * @return void
     **/
    protected function flash($key, $value)
    {
        $this->session->getFlashBag()->set($key, $value);
    }

    /**
     * Return 404 Result
     *
     * @param  string                $message Message for 404 template
     * @return \Reborn\Http\Response
     **/
    protected function notFound($message = null)
    {
        return Response::clueless($message);
    }

    /**
     * Parse the template string.
     *
     * @param  string $template
     * @param  array  $data
     * @return string
     **/
    protected function parse($template, $data = array())
    {
        $view = $this->app['view'];

        return $view->renderAsStr($template, $data);
    }

    /**
     * Set the HTTP status code to use at response.
     *
     * @param  int  $code HTTP status code.
     * @return void
     */
    protected function setHTTPstatus($code)
    {
        $realCode = Response::$statusTexts;

        if (isset($realCode[$code])) {
            $this->HTTPstatus = $code;
        }
    }

} // END class ControllerContainer
