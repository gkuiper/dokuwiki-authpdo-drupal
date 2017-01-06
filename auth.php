<?php
/**
 * DokuWiki Plugin joomla3 (Auth Component)
 *
 * @license GPL 2 http://www.gnu.org/licenses/gpl-2.0.html
 * @author  Jury Verrigni <jury.verrigni@skayahack.it>
 */

// must be run within Dokuwiki
if(!defined('DOKU_INC')) die();
/**
 * Class auth_plugin_authjoomla3
 */
class auth_plugin_authjoomla3 extends auth_plugin_authpdo {
	protected $joomlaPath = '';
	protected $joomlaConfig = [];

	public function __construct() {
		$this->joomlaPath = $this->getConf('joomlaPath');

		if ($this->joomlaPath == '' 
			|| !is_dir($this->joomlaPath) 
			|| !file_exists($this->joinPaths($this->joomlaPath, 'configuration.php'))) {
            $this->_debug('Joomla not found at the specified path.', -1, __LINE__);
			$this->success = false;
			return;
		}

		$this->setupPdoConfig();

		parent::__construct();
	}

    protected function _selectUserGroups($userdata) {
    	$groups = parent::_selectUserGroups($userdata);
    	foreach ($groups as &$group) {
    		if (in_array($group, array('Administrator', 'Super Users'))) {
    			$group = 'admin';
    		}
    	}
        return $groups;
    }

	protected function setupPdoConfig() {
        require_once $this->joinPaths($this->joomlaPath, 'configuration.php');
		$this->joomlaConfig = new JConfig;
		$this->joomlaConfig->dbtype = str_replace('mysqli', 'mysql', $this->joomlaConfig->dbtype);
		$this->conf['dsn'] = sprintf('%s:dbname=%s;host=%s', $this->joomlaConfig->dbtype, $this->joomlaConfig->db, $this->joomlaConfig->host);
		$this->conf['user'] = $this->joomlaConfig->user;
		$this->conf['pass'] = $this->joomlaConfig->password;

		$this->setupPdoQueries();
	}

	protected function setupPdoQueries() {
		$this->conf['select-user'] = 'SELECT username as user, name, email as mail, password as hash, id as uid FROM ' . $this->getTableName('users') . ' WHERE username = :user';	

		$this->conf['select-user-groups'] = sprintf('
			SELECT title as `group` FROM %s as groups 
			LEFT JOIN %s as groupmap ON groups.id = groupmap.group_id 
			LEFT JOIN %s as user ON groupmap.user_id = user.id
			WHERE user.username = :user OR user.id = :uid ', 
			$this->getTableName('usergroups'), 
			$this->getTableName('user_usergroup_map'),
			$this->getTableName('users'));  

		$this->conf['select-groups'] = sprintf('
			SELECT title as `group`, id as gid FROM %s',
			$this->getTableName('usergroups'));
	}

	protected function getTableName($name) {
		return $this->joomlaConfig->dbprefix . $name;
	}

	protected function joinPaths() {
	    $args = func_get_args();
	    $paths = array();
	    foreach ($args as $arg) {
	        $paths = array_merge($paths, (array)$arg);
	    }
	    $paths = array_map(create_function('$p', 'return trim($p, "/");'), $paths);
	    $paths = array_filter($paths);
	    if (substr($args[0], 0, 1) == '/') {
	    	$paths[0] = '/' . $paths[0];
	    }
	    return join('/', $paths);
	}
}