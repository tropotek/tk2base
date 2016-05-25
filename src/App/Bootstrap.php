<?php
namespace App;


use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Psr\Log\NullLogger;


/**
 * Class Bootstrap
 *
 * This should be called to setup the App lib environment
 *
 * ~~~php
 *     \App\Bootstrap::execute();
 * ~~~
 *
 * I am using the composer.json file to auto execute this file using the following entry:
 *
 * ~~~json
 *   "autoload":  {
 *     "psr-0":  {
 *       "":  [
 *         "src/"
 *       ]
 *     },
 *     "files" : [
 *       "src/App/Bootstrap.php"    <-- This one
 *     ]
 *   }
 * ~~~
 *
 *
 * @author Michael Mifsud <info@tropotek.com>  
 * @link http://www.tropotek.com/  
 * @license Copyright 2015 Michael Mifsud  
 */
class Bootstrap
{

    /**
     * This will also load dependant objects into the config, so this is the DI object for now.
     *
     */
    static function execute()
    {
        if (version_compare(phpversion(), '5.4.0', '<')) {
            // php version must be high enough to support traits
            throw new \Exception('Your PHP5 version must be greater than 5.4.0 [Curr Ver: '.phpversion().']');
        }
        
        // Do not call \Tk\Config::getInstance() before this point
        $config = \Tk\Config::getInstance();
        
        // Include any config overriding settings
        include($config->getSrcPath() . '/config/config.php');
        
        /**
         * This makes our life easier when dealing with paths. Everything is relative
         * to the application root now.
         */
        chdir($config->getSitePath());
        
        // * Logger [use error_log()]
        ini_set('error_log', $config->getSystemLogPath());
        \Tk\ErrorHandler::getInstance($config->getLog());
        
        $logger = new NullLogger();
        if (is_readable($config->getSystemLogPath())) {
            $logger = new Logger('system');
            $handler = new StreamHandler($config->getSystemLogPath(), $config->getSystemLogLevel());
            //$formatter = new LineFormatter(null, 'H:i:s', true, true);
            $formatter = new Util\LogLineFormatter();
            $handler->setFormatter($formatter);
            $logger->pushHandler($handler);
        }
        $config['log'] = $logger;
        
        \Tk\Uri::$BASE_URL_PATH = $config->getSiteUrl();
        
        
        // * Database init
        try {
            $pdo = \Tk\Db\Pdo::create($config->getGroup('db'));
//            $pdo->setOnLogListener(function ($entry) {
//                error_log('[' . round($entry['time'], 4) . 'sec] ' . $entry['query']);
//            });
            $config->setDb($pdo);
        } catch (\Exception $e) {
            error_log('<p>' . $e->getMessage() . '</p>');
            exit;
        }
        
        // Return if using cli (Command Line)
        if ($config->isCli()) {
            return $config;
        }

        // --- HTTP only boostrapping from here ---
        
        // * Request
        $request = \Tk\Request::create();
        $config->setRequest($request);
        
        // * Cookie
        $cookie = new \Tk\Cookie($config->getSiteUrl());
        $config->setCookie($cookie);
        
        // * Session
        $session = new \Tk\Session($config, $request, $cookie);
        //$session->start(new \Tk\Session\Adapter\Database( $config->getDb() ));
        $session->start();
        $config->setSession($session);
        
        return $config;
    }

}

// called by autoloader, see composer.json -> "autoload" : files [].....
Bootstrap::execute();

