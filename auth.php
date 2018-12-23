<?php
/**
 * DokuWiki Plugin joomla3 (Auth Component)
 *
 * @license GPL 2 http://www.gnu.org/licenses/gpl-2.0.html
 * @author  Jury Verrigni <jury.verrigni@skayahack.com>
 */

if (!defined('DOKU_INC')) {
    die();
}

/**
 * Class auth_plugin_authjoomla3
 */
class auth_plugin_authpdodrupal extends auth_plugin_authpdo
{
    protected $drupalPath = '';

    /**
    * Before calling AuthPDO's construct we want to override database's
    * settings with Drupal's ones
    **/
    public function __construct()
    {
        $this->drupalPath = $this->getConf('drupalPath');
		
        if ($this->drupalPath == ''
            || !is_dir($this->drupalPath)
            || !file_exists($this->joinPaths($this->drupalPath, 'includes' ,'password.inc'))) {
            $this->_debug('Drupal password.inc not found at the specified path.', -1, __LINE__);
            $this->success = false;
            return;
        }

        parent::__construct();
    }
	
	/**
     * Check user+password
     *
     * @param   string $user the user name
     * @param   string $pass the clear text password
     * @return  bool
     */
    public function checkPass($user, $pass) {
		$this->_debug('Checking password.', -1, __LINE__);
        $userdata = $this->_selectUser($user);

        if($userdata == false) return false;

		require_once $this->joinPaths($this->drupalPath, 'includes' ,'password.inc');
		$hash = _password_crypt('sha512', $pass, $userdata['hash']);

		if(\hash_equals($hash, $userdata['hash'])) {
			$this->_debug('Correct password', -1, __LINE__);
            return true;
		}
		$this->_debug('Wrong password', -1, __LINE__);
		return false;
    }

    protected function joinPaths()
    {
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
