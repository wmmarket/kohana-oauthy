<?php defined('SYSPATH') or die('No direct script access.');
/**
 * OAuth server for clients register management
 *
 * @author      sumh <oalite@gmail.com>
 * @package     Oauthz
 * @copyright   (c) 2010 OALite
 * @license     ISC License (ISCL)
 * @link		http://oalite.com
 * @see			Kohana_Controller
 * *
 */
class Oauthz_Server extends Kohana_Controller {

    protected $template = 'oauth-template';

    public function before()
    {
        $this->template = new View($this->template);
    }

    public function action_register($client_id = NULL)
    {
        $data = array();
        $server = new Model_Oauthz_Server;
        if($client_id)
        {
            $data = (array) $server->get($client_id, $_SESSION['user']['uid']);
        }
        elseif(isset($_POST['__v_state__']))
        {
            $_POST['user_id'] = $_SESSION['user']['uid'];

            $valid = empty($_POST['server_id']) ? $server->append($_POST) : $server->update($_POST['server_id'], $_POST);

            if($valid instanceOf Validate)
            {
                $data = $valid->as_array();
                $data['errors'] = $valid->errors('validate');
            }
            else
            {
                $data += $valid;
            }
        }

        $this->template->content = new View('oauthz-register', $data);

        $this->request->response = $this->template->render();
    }

    public function action_client()
    {
        $server = new Model_Oauthz_Server;

        $data = $server->lists();

        $this->template->content = new View('oauthz-server-client', $data);

        $this->request->response = $this->template->render();
    }

    public function action_access_deny()
    {
        $this->request->status = 302; #HTTP/1.1 302 Found
        $this->request->headers['Content-Type'] = 'application/x-www-form-urlencoded';
        $this->request->headers['Location'] = 'http://example.com/rd#error=user_denied';
    }

} // END Oauthz_Server
