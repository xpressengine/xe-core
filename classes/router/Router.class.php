<?php
class Router
{
	private $routes = array();
	private $rewrite_map = array();
	private $segments = array();
	private $stop_proc = FALSE;

	/**
	 * returns static context object (Singleton). It's to use Router without declaration of an object
	 *
	 * @return object Instance
	 */
	public function &getInstance()
	{
		static $theInstance = null;
		if(!$theInstance) $theInstance = new Router();

		return $theInstance;
	}

	/**
	 * Initialization, it sets routes and so on.
	 *
	 * @see This function should be called only once
	 * @return void
	 */
	public function init()
	{
		// Get path info
		$path_info = parse_url(substr($_SERVER['REQUEST_URI'], 1));
		$path = $path_info['path'];
		if(strlen($path) < 1)
		{
			$this->stop_proc = TRUE;
			return TRUE;
		}

		$this->segments = explode('/', $path);
		array_shift($this->segments);

		$this->routes = array(
			// rss , blogAPI
			'(rss|atom)' => array('module' => 'rss', 'act' => '$1'),
			'([a-zA-Z0-9_]+)/(rss|atom|api)' => array('mid' => '$1', 'act' => '$2'),
			'([a-zA-Z0-9_]+)/([a-zA-Z0-9_]+)/(rss|atom|api)' => array('vid' => '$1', 'mid' => '$2', 'act' => '$3'),
			// trackback
			'([0-9]+)/(.+)/trackback' => array('document_srl' => '$1', 'key' => '$2', 'act' => 'trackback'),
			'([a-zA-Z0-9_]+)/([0-9]+)/(.+)/trackback' => array('mid' => '$1', 'document_srl' => '$2', 'key' => '$3', 'act' => 'trackback'),
			'([a-zA-Z0-9_]+)/([a-zA-Z0-9_]+)/([0-9]+)/(.+)/trackback' => array('vid' => '$1', 'mid' => '$2', 'document_srl' => '$3' , 'key' => '$4', 'act' => 'trackback'),
			// mid
			'([a-zA-Z0-9_]+)/?' => array('mid' => '$1'),
			// mid + document_srl
			'([a-zA-Z0-9_]+)/([0-9]+)' => array('mid' => '$1', 'document_srl' => '$2'),
			// vid + mid
			'([a-zA-Z0-9_]+)/([a-zA-Z0-9_]+)/' => array('vid' => '$1', 'mid' => '$2'),
			// vid + mid + document_srl
			'([a-zA-Z0-9_]+)/([a-zA-Z0-9_]+)/([0-9]+)?' => array('vid' => '$1', 'mid' => '$2', 'document_srl' => '$3'),
			// document_srl
			'([0-9]+)' => array('document_srl' => '$1'),
			// mid + entry title
			'([a-zA-Z0-9_]+)/entry/(.+)' => array('mid' => '$1', 'entry' => '$2'),
			// vid + mid + entry title
			'([a-zA-Z0-9_]+)/([a-zA-Z0-9_]+)/entry/(.+)' => array('vid' => '$1', 'mid' => '$2', 'entry' => '$3'),
			// shop / vid / [category|product] / identifier
			'([a-zA-Z0-9_]+)/([a-zA-Z0-9_]+)/([a-zA-Z0-9_\.-]+)' => array('act' => 'route', 'vid' => '$1', 'type' => '$2', 'identifier'=> '$3'),
		);
	}

	public function proc()
	{
		if($this->stop_proc)
		{
			return TRUE;
		}

		if(isset($this->routes[$path]))
		{
			foreach($this->routes[$path] as $key => $val)
			{
				$key = preg_replace('#^\$([0-9]+)$#e', '\$matches[$1]', $key);
				$val = preg_replace('#^\$([0-9]+)$#e', '\$matches[$1]', $val);

				Context::set($key, $val, TRUE);
			}

			return TRUE;
		}

		// Apply routes
		foreach($this->routes as $regex => $query)
		{
			if(preg_match('#^' . $regex . '$#', $path_info['path'], $matches))
			{
				foreach($query as $key => $val)
				{
					$val = preg_replace('#^\$([0-9]+)$#e', '\$matches[$1]', $val);

					Context::set($key, $val, TRUE);
				}
			}
		}

		return TRUE;
	}

	/**
	 * Add a rewrite map(s)
	 */
	public function setMap($map)
	{
		foreach($map as $key => $val)
		{
			$this->rewrite_map[$key] = $val;
		}
	}

	/**
	 * Add a route
	 */
	public function add($target, $query)
	{
		$this->routes[$target] = $query;
	}

	/**
	 * Add multiple routes
	 */
	public function adds($routes)
	{
		foreach($routes as $target => $query)
		{
			$this->routes[$target] = $query;
		}
	}

	/**
	 * Get segment from request uri
	 */
	public function getSegment($index)
	{
		return $this->segments[$index];
	}

	public function getRoutes()
	{
		return $this->routes;
	}

	public function getRoute($key)
	{
		return $this->routes[$key];
	}

	public function isExistsRoute($key)
	{
		return isset($this->routes[$key]);
	}

	public function makePrettyUrl($key)
	{
		return $this->rewrite_map[$key];
	}
}